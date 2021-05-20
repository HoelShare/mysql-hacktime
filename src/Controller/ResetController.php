<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
    public function __construct(
        private UserService $userService,
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
            return $this->redirectToRoute('home', ['errorMessage' => 'Invalid Password, create new instance']);
        }

        $this->userService->looseLevels($user);
        $this->userService->createInstance($user, $password);

        return $this->redirectToRoute('renderLevel', ['user' => $user, 'password' => $password]);
    }
}