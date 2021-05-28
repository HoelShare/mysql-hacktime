<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DemoData\ViewCompareLevel;
use App\Service\LevelService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckController extends AbstractController
{
    private const TRY_THRESHOLD = 3;

    public function __construct(
        private LevelService $levelService,
    ) {
    }

    /**
     * @Route("/check/{user}", name="checkLevel", methods={"POST"})
     */
    public function check(string $user, Request $request): Response
    {
        $user = strtolower($user);
        $check = $this->levelService->checkMax($user);

        $currentLevel = $this->levelService->getCurrentLevel($user);
        $request->request->set('level', $currentLevel);

        if ($check === null) {
            $request->request->set('successMessage', 'Level completed!');
        } else {
            $request->request->set('errorMessage', 'Level not yet achieved');
            $request->request->set('check', $check);
        }

        return $this->showUserPage($user, $request);
    }

    /**
     * @Route("/check/{user}", name="showUserLevel", methods={"GET"})
     */
    public function showUserPage(string $user, Request $request): Response
    {
        $user = strtolower($user);
        $currentLevel = $this->levelService->getCurrentLevel($user);

        $params = ['user' => $user, 'level' => $currentLevel->getLevel()];

        if ($currentLevel->getLevel() === 0 && $request->get('password') !== null) {
            $params['successMessage'] = sprintf(
                'User created, password: %s',
                $request->get('password')
            );
        }

        $try = $this->levelService->getLevelTry($user, $currentLevel->getLevel());
        if ($try >= self::TRY_THRESHOLD) {
            $params['hintMessage'] = $request->get('check');
        }
        if ($currentLevel instanceof ViewCompareLevel) {
            $params['levelData'] = $currentLevel->getPreviewData();
        }

        return $this->render(
            'user.html.twig',
            array_merge(
                $request->query->all(),
                $request->request->all(),
                $params
            )
        );
    }
}
