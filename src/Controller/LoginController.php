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
        return $this->render('/login/login.html.twig');
    }

    /**
     * @Route("/show/{id}", name="showWithId")
     * @Method({"GET"})
     */
    public function showWithId(Request $request, $id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        return $this->render('login/show.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/show", name="show")
     * @Method({"POST"})
     */
    public function show(Request $request)
    {
        $password = $request->request->get('password');
        $email    = $request->request->get('email');
        $pwdPep   = hash_hmac("sha256", $password, 'c1isvFdxMDdmjOlvxpecFw');

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'email' => $email
        ]);

        if ($user) {
            if (password_verify($pwdPep, $user->getPassword())) {
                return $this->render('login/show.html.twig', ['user' => $user]);
            }
        }

        return $this->render('login/login.html.twig', ['error' => 'Wrong username or password']);
    }

    /**
     * @Route("/signup", name="singup")
     * @Method({"GET"})
     */
    public function singup()
    {
        return $this->render('login/signup.html.twig');
    }

    /**
     * @Route("/user/add", name="user_add")
     * @Method({"POST"})
     */
    public function createUser(Request $request)
    {
        $user = new User();

        $confirmPassword = $request->request->get('confirm_password');
        $password = $request->request->get('password');
        $username = $request->request->get('username');
        $email    = $request->request->get('email');

        if ($this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email])) {
            return $this->render('login/signup.html.twig', ['error' => 'The email is already in list.']);
        }

        if ($password !== $confirmPassword) {
            return $this->render('login/signup.html.twig', ['error' => 'The password fields must match.']);
        }

        $pwd = hash_hmac("sha256", $password, 'c1isvFdxMDdmjOlvxpecFw');
        $pwd_hashed = password_hash($pwd, PASSWORD_ARGON2ID);

        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($pwd_hashed);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->render('login/login.html.twig', ['success' => 'Saved!']);
    }

    /**
     * @Route("/user/edit/{id}", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editUser(Request $request, $id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($request->getContent()) {
            $confirmPassword = $request->request->get('confirm_password');
            $password = $request->request->get('password');
            $username = $request->request->get('username');
            $email    = $request->request->get('email');

            if ($password !== $confirmPassword) {
                return $this->render('login/edit.html.twig', ['user' => $user, 'error' => 'The password fields must match.']);
            }

            $pwd = hash_hmac("sha256", $password, 'c1isvFdxMDdmjOlvxpecFw');
            $pwd_hashed = password_hash($pwd, PASSWORD_ARGON2ID);

            $user->setEmail($email);
            $user->setUsername($username);
            $user->setPassword($pwd_hashed);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('showWithId', ['id' => $id]);
        }

        return $this->render('login/edit.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/user/delete/{id}")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

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
