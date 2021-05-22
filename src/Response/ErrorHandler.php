<?php

declare(strict_types=1);

namespace App\Response;

use App\Exception\LevelNotFoundException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class ErrorHandler implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::EXCEPTION => 'onKernelException'];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $templateName = match (true) {
            $exception instanceof LevelNotFoundException => 'finished.html.twig',
            default => 'home.html.twig',
        };

        $content = $this->twig->render($templateName, ['errorMessage' => $exception->getMessage()]);

        $response = new Response($content);
        $event->setResponse($response);
    }
}
