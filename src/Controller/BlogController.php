<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

class BlogController extends AbstractController
{
    private $orm;
    private $security;
    public function __construct(ManagerRegistry $doctrine, Security $security)
    {
        $this->security = $security;
        $this->orm = $doctrine;  
    }

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        $posts = $this->orm->getRepository(Post::class)->findBy(
            ['deleted' => false]
        );
        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/author/{id<\d+>}", name="author_posts")
     */
    public function author_posts(int $id): Response
    {
        $entityManager = $this->orm->getManager();
        $author = $entityManager->getRepository(User::class)->find($id);
        $posts = $this->orm->getRepository(Post::class)->findBy(
            ['author' => $author,
             'deleted' => false
            ]
        );
        return $this->render('blog/author_posts.html.twig', [
            'author'=> $author,
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/blog/{id<\d+>}/{slug}.html", name="blog_view_post")
     */
    public function view_post(int $id, Request $req): Response
    {
        $entityManager = $this->orm->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        if(is_null($post) || $post->isDeleted()) {
            return $this->render('404.html.twig'); //render 404
        }else{
            // $cmt_form = null;
            $comment = new Comment();
            $comment->setPost($post);

            $cmt_form = $this->createFormBuilder($comment)
                    ->add('content', TextareaType::class)
                    ->add('replyTo', HiddenType::class, [
                        'required' => false,
                        'mapped' => false
                        ])
                    ->add('save', SubmitType::class, ['label' => 'Add Comment'])
                    ->getForm();
            $cmt_form->handleRequest($req);
            $user = $this->security->getUser();
            // if (is_null($user)) { //check if user now not login
            //     //redirect to login page
            //     return $this->redirectToRoute('login');
            // }else{ //have been logging
            //     $comment->setUser($user); //comment of user loging
            //     if ($form->isSubmitted() && $form->isValid()) {
            //         $entityManager->persist($comment);
            //         $entityManager->flush();
            //     }
            // }
            
            if ($cmt_form->isSubmitted() && $cmt_form->isValid()){
                    $replyToId = $cmt_form->get('replyTo')->getData();
                    if (!empty($replyToId)) {
                        $parentComment = $entityManager->getRepository(Comment::class)->find($replyToId);
                        $comment->setReplyTo($parentComment);
                    }
                    if(is_null($user)){ //user not login
                        return $this->redirectToRoute('login'); //redirect to login
                    }else{
                        $comment->setUser($user); //comment of user logged in
                        $entityManager->persist($comment);
                        $entityManager->flush();
                    }
            }

            // foreach($post->getComments() as $cmt){
            //     if (is_null($cmt->getReplyTo())) {
            //         $reply = new Comment();
            //         $reply->setPost($post);
            //         $reply->setReplyTo($cmt);
            //         $rep_form = $this->createFormBuilder($reply)
            //             ->add('content', TextareaType::class, ['label' => 'Reply'])
            //             ->add('save', SubmitType::class, ['label' => 'Add Reply'])
            //             ->getForm();
            //         $cmt -> replyForm = $rep_form->createView();
            //         $rep_form->handleRequest($req);

            //         if ($rep_form->isSubmitted() && $rep_form->isValid()){
            //             if(is_null($user)){
            //                 return $this->redirectToRoute('login');
            //             }else{
            //                 $reply->setUser($user); //comment of user loging
            //                 $entityManager->persist($reply);
            //                 $entityManager->flush();
            //             }
            //         }
            //     }
            // }
            
            $post->setViews($post->getViews() + 1); //increase view +1
            $entityManager->flush(); // save change to DB
            return $this->renderForm('blog/view.html.twig', [
                'post' => $post,
                'comment_form' => $cmt_form
            ]);
        }
    }
    
}