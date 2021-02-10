<?php

namespace App\Controller;

use App\Entity\Post;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class PostController extends AbstractController
{
    /**
     * @Route("/profile", name="profile", methods={"GET"})
     */
    public function profile(Request $request)
    {
        $session  = $request->getSession();
        $user     = $session->get('user');

        if ($user) {
            $posts = $this->getDoctrine()->getRepository(Post::class)->findBy(['author' => $user->getUsername()]);
            $param = ['user' => $user];
            if (sizeof($posts) > 0) {
                $param['posts'] = $posts;
            }
            return $this->render('post/profile.html.twig', $param);
        }

        return $this->render('login/login.html.twig', ['error' => 'Session expired']);
    }

    /**
     * @Route("/postView/{id}", name="post_view", methods={"GET"})
     */
    public function postView(Request $request, $id)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        return $this->render('post/post.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/post/new", name="new_post", methods={"GET", "POST"})
     */
    public function new(Request $request)
    {
        $post = new Post();

        $session = $request->getSession();
        $user = $session->get('user');

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add(
                'content',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'style' => 'height: 250px'
                    ]
                ]
            )
            ->add('duration', NumberType::class, ['attr' => ['class' => 'form-control']])
            ->add('author', HiddenType::class, [
                'data' => $user->getUsername()
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('profile');
        }

        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/post/edit/{id}", name="edit_post", methods={"GET", "POST"})
     */
    public function edit(Request $request, $id)
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        $session = $request->getSession();
        $user    = $session->get('user');

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add(
                'content',
                TextareaType::class,
                [
                    'required' => false,
                    'attr' => [
                        'class' => 'form-control',
                        'style' => 'height: 250px'
                    ]
                ]
            )
            ->add('duration', NumberType::class, ['attr' => ['class' => 'form-control']])
            ->add('author', HiddenType::class, [
                'data' => $user->getUsername()
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('post_view', ['id' => $id]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form->createView(),
            'post_id' => $id
        ]);
    }

    /**
     * @Route("/post/delete/{id}", name="remove_post", methods={"DELETE"})
     */
    public function deletePost(Request $request, $id)
    {
        $user = $this->getDoctrine()->getRepository(Post::class)->find($id);

        try {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        } catch (\Exception $th) {
            return new Response(
                "Error: $th",
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        return new Response(
            'Successfully removed.',
            Response::HTTP_OK
        );
    }
}
