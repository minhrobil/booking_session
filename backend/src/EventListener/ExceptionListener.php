<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ExceptionListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $this->logger->error($exception->getMessage(), [
            'exception' => $exception
        ]);

        $statusCode = 500;
        $message = 'Internal Server Error';

        if ($exception instanceof \InvalidArgumentException) {
            $statusCode = 400;
            $message = $exception->getMessage();
        } elseif ($exception instanceof NotFoundHttpException) {
            $statusCode = 404;
            $message = 'Resource not found';
        } elseif ($exception instanceof AccessDeniedHttpException) {
            $statusCode = 403;
            $message = 'Access denied';
        } elseif ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $message = $exception->getMessage() ?: JsonResponse::$statusTexts[$statusCode] ?? 'Error';
        }

        $response = new JsonResponse([
            'error' => $message
        ], $statusCode);

        $event->setResponse($response);
    }
}
