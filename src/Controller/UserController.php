<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Input\RegisterUserInput;
use App\DTO\Output\RegisterUserOutput;
use App\Service\UserService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/users', 'users_')]
class UserController extends AbstractApiController
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    #[Route('/register', 'register', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: RegisterUserInput::class)
    )]
    #[OA\Response(
        response: 201,
        description: 'User registered successfully',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(
                            property: 'userId',
                            type: 'int',
                            example: 1
                        ),
                    ],
                    type: 'object',
                ),
                new OA\Property(property: 'message', type: 'string', example: 'User registered successfully'),
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: 'Validation failed',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'detail', type: 'string', example: 'email: This value is not a valid email address.'),
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'User registration failed',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'data'),
                new OA\Property(property: 'message', type: 'string', example: 'Email already in use'),
            ]
        )
    )]
    #[OA\Tag(name: 'User')]
    public function register(#[MapRequestPayload] RegisterUserInput $input): JsonResponse
    {
        try {
            $user = $this->userService->register($input);
        } catch (BadRequestHttpException $exception) {
            return $this->respondWithError($exception->getMessage(), $exception->getStatusCode());
        }

        return $this->respondWithSuccess(['userId' => $user->getId()], 'User registered successfully', 201);
    }
}
