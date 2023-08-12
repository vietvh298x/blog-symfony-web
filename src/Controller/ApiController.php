<?php

namespace App\Controller;

use App\Entity\Post;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraints\Json;

class ApiController extends AbstractController
{
    private $orm;
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->orm = $doctrine;
    }

    /**
     * @Route("/api/like_post/{id<\d+>}", name="api_like_post")
     */
    public function like_post(int $id): Response
    {
        $entityManager = $this->orm->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);
        $post->setLikes($post->getLikes() + 1);
        $entityManager->flush(); // save change to DB
        $data = new \stdClass();
        $data->status = "OK";
        $data->likes = $post->getLikes();
        return new JsonResponse($data);
    }
    
}