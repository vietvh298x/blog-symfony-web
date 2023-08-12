<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Json;

class MemberController extends AbstractController
{
    private $security;
    private $orm;
    public function __construct(ManagerRegistry $doctrine, Security $security)
    {
        $this->security = $security;
        $this->orm = $doctrine;
    }

    /**
     * @Route("/member/posts", name="blog_manage_posts")
     */
    public function my_posts(): Response
    {
        $author = $this->security->getUser();
        $posts = $this->orm->getRepository(Post::class)->findBy(
            ['author' => $author,
             'deleted' => false  
            ]
        );
        return $this->render('member/my_posts.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/member/trash", name="blog_trash_posts")
     */
    public function my_trash(): Response
    {
        $author = $this->security->getUser();
        $posts = $this->orm->getRepository(Post::class)->findBy(
            ['author' => $author,
             'deleted' => true  
            ]
        );
        return $this->render('member/trash_posts.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/member/write", name="blog_add_post")
     */
    public function add_post(Request $req): Response
    {
        $post = new Post();
        $post->setAuthor($this->security->getUser());
        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('save', SubmitType::class, ['label' => 'Add Post'])
            ->getForm();
        $msg = '';
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->orm->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            $msg = 'Post has been added!';
        }
        return $this->renderForm('member/write.html.twig', [
            'form' => $form,
            'message' => $msg,
        ]);
    }

    /**
     * @Route("/member/edit/{id}", name="blog_edit_post")
     */
    public function edit_post(int $id, Request $req): Response
    {
        $entityManager = $this->orm->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('save', SubmitType::class, ['label' => 'Save Post'])
            ->getForm();
        $msg = '';
        $form->handleRequest($req);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $msg = 'Post has been edited successfully!';
        }
        return $this->renderForm('member/edit.html.twig', [
            'form' => $form,
            'message' => $msg,
        ]);
    }
    
    /**
     * @Route("/member/move_to_trash/{id}", name="blog_move_post_to_trash")
     */
    public function move_to_trash(int $id): RedirectResponse
    {
        $entityManager = $this->orm->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $post->setDeleted(true);
        $entityManager->flush(); //sava changes to DB
       return $this->redirectToRoute('blog_manage_posts'); //redirect to route blog_manage_posts
    }

    /**
     * @Route("/member/recover/{id}", name="blog_recover_deleted")
     */
    public function recover_post(int $id): RedirectResponse
    {
        $entityManager = $this->orm->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $post->setDeleted(false);
        $entityManager->flush(); //sava changes to DB
       return $this->redirectToRoute('blog_trash_posts'); //redirect to route blog_trash_posts
    }

    /**
     * @Route("/member/remove/{id}", name="blog_deleted_permanently")
     */
    public function deleted_post(int $id): RedirectResponse
    {
        $entityManager = $this->orm->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $entityManager->remove($post); //remove from db
        $entityManager->flush(); //sava changes to DB
       return $this->redirectToRoute('blog_trash_posts'); //redirect to route blog_trash_posts
    }
    
}