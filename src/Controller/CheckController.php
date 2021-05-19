<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CheckerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckController extends AbstractController
{
    public function __construct(
        private CheckerService $checkerService,
    ) {
    }

    /**
     * @Route("/check/{user}", name="checkLevel", methods={"POST"})
     */
    public function check(string $user): Response
    {
        $params = ['user' => $user];
        $check = $this->checkerService->checkMax($user);

        $currentLevel = $this->checkerService->getCurrentLevel($user);
        $params['level'] = $currentLevel;

        if ($check) {
            $params['successMessage'] = 'Level completed!';
        } else {
            $params['errorMessage'] = 'Level not yet achived';
        }
        return $this->render('user.html.twig', $params);
    }

    /**
     * @Route("/check/{user}", name="renderLevel", methods={"GET"})
     */
    public function renderLevel(string $user, Request $request): Response
    {
        $currentLevel = $this->checkerService->getCurrentLevel($user);

        $params = ['user' => $user, 'level' => $currentLevel];

        if ($currentLevel === 0 && $request->get('password') !== null) {
            $params['successMessage'] = sprintf(
                'User created, password: %s',
                $request->get('password')
            );
        }

        return $this->render('user.html.twig', $params);
    }
}