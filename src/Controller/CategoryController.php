<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Input\CategoryInput;
use App\DTO\Output\CategoryOutput;
use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/categories', 'categories_')]
class CategoryController extends AbstractApiController
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly CategoryService $categoryService,
    ) {}

    #[Route('', 'list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: "List of user's categories",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(
                            property: 'items',
                            type: 'array',
                            items: new OA\Items(ref: new Model(type: CategoryOutput::class)),
                        ),
                    ],
                    type: 'object',
                ),
            ],
        ),
    )]
    #[OA\Tag(name: 'Category')]
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $categories = $this->categoryRepository->getCategoriesByUser($user);

        return $this->respondWithSuccess(['items' => CategoryOutput::list($categories)]);
    }

    #[Route('/{category}', 'get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns Category details',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(
                            property: 'item',
                            ref: new Model(type: CategoryOutput::class),
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                ),
            ],
        ),
    )]
    #[OA\Tag(name: 'Category')]
    public function get(Category $category): JsonResponse
    {
        return $this->respondWithSuccess(['item' => CategoryOutput::serialize($category)]);
    }

    #[Route('', 'create', methods: ['POST'])]
    #[OA\RequestBody(required: true, content: new Model(type: CategoryInput::class))]
    #[OA\Response(
        response: 201,
        description: 'Creates Category',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(
                            property: 'item',
                            ref: new Model(type: CategoryOutput::class),
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                ),
            ],
        ),
    )]
    #[OA\Tag(name: 'Category')]
    public function create(#[MapRequestPayload] CategoryInput $input): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $category = $this->categoryService->create($input, $user);

        return $this->respondWithSuccess(['item' => CategoryOutput::serialize($category)], status: 201);
    }

    #[Route('/{category}', 'update', methods: ['PATCH'])]
    #[OA\RequestBody(required: true, content: new Model(type: CategoryInput::class))]
    #[OA\Response(
        response: 200,
        description: 'Category updated',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(
                            property: 'item',
                            ref: new Model(type: CategoryOutput::class),
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                ),
            ],
        ),
    )]
    #[OA\Tag(name: 'Category')]
    public function update(Category $category, #[MapRequestPayload] CategoryInput $input): JsonResponse
    {
        $category = $this->categoryService->update($category, $input);

        return $this->respondWithSuccess(['item' => CategoryOutput::serialize($category)]);
    }

    #[Route('/{category}', 'delete', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Category deleted')]
    #[OA\Tag(name: 'Category')]
    public function delete(Category $category): JsonResponse
    {
        $this->categoryRepository->delete($category);

        return $this->respondWithSuccess(null, status: 204);
    }
}
