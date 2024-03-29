<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\UserAlreadyExists;
use App\Service\JokeService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private JokeService $jokeService,
    ) {
    }

    /**
     * @Route("/user", name="createUser", methods={"POST"})
     */
    public function createUser(Request $request): Response
    {
        $userName = (string) $request->request->get('username');
        $user = strtolower($userName);

        try {
            $password = $this->userService->createUser($user);
        } catch (UserAlreadyExists $alreadyExists) {
            return $this->redirectToRoute('showUserLevel', ['user' => $alreadyExists->getUsername()]);
        }

        return $this->redirectToRoute('showUserLevel', ['user' => $userName, 'password' => $password]);
    }

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $joke = $this->jokeService->getRandom();
        $errorMessage = $request->query->get('errorMessage');

        return $this->render('home.html.twig', ['errorMessage' => $errorMessage, 'joke' => $joke]);
    }
}
