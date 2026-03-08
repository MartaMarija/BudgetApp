<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Output\ApiResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class AbstractApiController extends AbstractController
{
    protected function respondWithSuccess(mixed $data, string $message = '', int $status = 200, ?array $metadata = null): JsonResponse
    {
        return $this->json(new ApiResponse($data, $message, $metadata), $status);
    }

    protected function respondWithError(string $message, int $status = 400, mixed $data = null): JsonResponse
    {
        return $this->json(new ApiResponse($data, $message), $status);
    }
}
