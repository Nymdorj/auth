<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    /**
     * @Route("/", name="index")
     * @Method({"GET"})
     */
    public function index()
    {
        return $this->render('/login/login.html.twig', ['error' => null]);
    }

    /**
     * @Route("/check", name="check")
     * @Method({"POST"})
     */
    public function check(Request $request)
    {
        $password = $request->request->get('password');
        $email = $request->request->get('email');

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $email,
            'password' => base64_encode($password)
        ]);

        if (!$user) {
            return $this->render('login/login.html.twig', ['error' => 'Wrong username or password']);
        }

        return $this->render('login/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/signup", name="singup")
     * @Method({"GET"})
     */
    public function singup()
    {
        return $this->render('login/signup.html.twig');
    }
}
