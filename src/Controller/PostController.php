<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CreatePostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function home(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', ['posts' => $posts]);
        // equivalent fonctionne SEULEMENT SI la dénomination 'posts' a le même nom que la variable qu'on lui passe $posts ici 
        // return $this->render('post/index.html.twig', compact('posts'));

    }
    /**
     * @Route("/post/create", name="app_post_create", methods="GET|POST")
     */
    public function create(Request $request, EntityManagerInterface $em, Security $security)
    {
        $post = new Post();
        $form = $this->createForm(CreatePostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $post->setAuthor($user);

            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/create.html.twig', ['form' => $form->createView()]);
    }
}
