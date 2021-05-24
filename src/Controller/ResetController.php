<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DemoDataService;
use App\Service\LevelService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    public function __construct(
        private UserService $userService,
        private LevelService $levelService,
        private DemoDataService $demoDataService,
    ) {
    }

    /**
     * @Route("/reset/{user}", name="resetInstance", methods={"POST"})
     */
    public function resetInstance(Request $request, string $user): Response
    {
        $password = $request->get('password');
        $isValid = $this->userService->isValid($user, $password);

        if (!$isValid) {
            $level = $this->levelService->getCurrentLevel($user);

            return $this->redirectToRoute(
                'showUserLevel',
                [
                    'user' => $user,
                    'level' => $level->getLevel(),
                    'errorMessage' => 'Invalid Password, instance not reverted',
                ]
            );
        }

        $this->userService->looseLevels($user);
        $this->userService->createInstance($user, $password);

        return $this->redirectToRoute('showUserLevel', ['user' => $user, 'password' => $password]);
    }

    /**
     * @Route("/reset/{user}/level", name="resetLevel", methods={"POST"})
     */
    public function resetLevel(string $user): Response
    {
        $level = $this->levelService->getCurrentLevel($user);
        $this->demoDataService->resetLevel($user, $level->getLevel());

        return $this->render(
            'user.html.twig',
            ['user' => $user, 'level' => $level->getLevel(), 'successMessage' => 'Level reverted']
        );
    }
}
