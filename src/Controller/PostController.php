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

            $this->addFlash('success', 'Your post has been created successfully');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/post/edit/{id<\d+>}", name="app_post_edit", methods="GET|POST|PUT")
     */
    public function edit(Request $request, Post $post, EntityManagerInterface $em, Security $security)
    {

        $form = $this->createForm(CreatePostType::class, $post);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();

            if ($user === $post->getAuthor()) {
                $em->flush();
                $this->addFlash('success', 'Your post has been edited successfully');
            } else {
                $this->addFlash('danger', 'Your post could not be edited. Try again.');
            }
            return $this->redirectToRoute('app_home');
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
        ]);
    }

    /**
     * @Route("/post/{id<\d+>}", name="app_post_delete", methods="DELETE")
     */
    public function delete(Request $request, Post $post, EntityManagerInterface $em, Security $security)
    {
        $user = $security->getUser();

        if ($this->isCsrfTokenValid('post_delete_' . $post->getId(), $request->request->get('csrf_token'))) {

            if ($user === $post->getAuthor()) {
                $em->remove($post);
                $em->flush();
                $this->addFlash('warning', 'Your post has been deleted successfully !');
            }
        }
        return $this->redirectToRoute('app_home');
    }


    /**
     * @Route("/post/{id<\d+>}", name="app_post_show")
     */
    public function show(Post $post)
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }
}
