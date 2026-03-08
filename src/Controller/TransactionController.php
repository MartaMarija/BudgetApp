<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\Input\Transaction\TransactionFilterInput;
use App\DTO\Input\Transaction\TransactionInput;
use App\DTO\Output\Transaction\TransactionOutput;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Service\TransactionService;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/transactions', 'transactions_')]
class TransactionController extends AbstractApiController
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
        private readonly TransactionService $transactionService,
        private readonly PaginatorInterface $paginator,
    ) {}

    #[Route('', 'list', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Paginated list of transactions',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(
                            property: 'items',
                            type: 'array',
                            items: new OA\Items(ref: new Model(type: TransactionOutput::class)),
                        ),
                        new OA\Property(
                            property: 'amountStats',
                            properties: [
                                new OA\Property(
                                    property: 'income',
                                    properties: [
                                        new OA\Property(property: 'total', type: 'string', example: '1500.00'),
                                        new OA\Property(property: 'min', type: 'string', example: '50.00', nullable: true),
                                        new OA\Property(property: 'max', type: 'string', example: '3000.00', nullable: true),
                                    ],
                                    type: 'object',
                                ),
                                new OA\Property(
                                    property: 'expense',
                                    properties: [
                                        new OA\Property(property: 'total', type: 'string', example: '1000.00'),
                                        new OA\Property(property: 'min', type: 'string', example: '10.00', nullable: true),
                                        new OA\Property(property: 'max', type: 'string', example: '850.00', nullable: true),
                                    ],
                                    type: 'object',
                                ),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
                new OA\Property(
                    property: 'metadata',
                    properties: [
                        new OA\Property(
                            property: 'pagination',
                            properties: [
                                new OA\Property(property: 'page', type: 'integer', example: 1),
                                new OA\Property(property: 'limit', type: 'integer', example: 20),
                                new OA\Property(property: 'total', type: 'integer', example: 100),
                                new OA\Property(property: 'pages', type: 'integer', example: 5),
                            ],
                        ),
                    ],
                ),
            ],
        ),
    )]
    #[OA\Tag(name: 'Transaction')]
    public function list(#[MapQueryString] TransactionFilterInput $input): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $qb = $this->transactionRepository->getFilterQuery(
            $user,
            $input->getFrom(),
            $input->getTo(),
            $input->getTransactionType(),
            $input->categoryId,
            $input->sortField,
            $input->sortDirection,
        );

        $amountStats = $this->transactionRepository->getAmountStats(
            $user,
            $input->getFrom(),
            $input->getTo(),
            $input->getTransactionType(),
            $input->categoryId,
        );

        $pagination = $this->paginator->paginate($qb, $input->page, $input->limit);
        $transactions = $pagination->getItems();

        $totalPages = (int) ceil($pagination->getTotalItemCount() / $input->limit);

        return $this->respondWithSuccess(
            data: [
                'transactions'  => TransactionOutput::list($transactions),
                'amountStats' => $amountStats,
            ],
            metadata: [
                'pagination' => [
                    'page'  => $pagination->getCurrentPageNumber(),
                    'limit' => $pagination->getItemNumberPerPage(),
                    'total' => $pagination->getTotalItemCount(),
                    'pages' => $totalPages,
                ],
            ]
        );
    }

    #[Route('/{transaction}', 'get', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns Transaction details',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(
                            property: 'item',
                            ref: new Model(type: TransactionOutput::class),
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                ),
            ],
        ),
    )]
    #[OA\Tag(name: 'Transaction')]
    public function get(Transaction $transaction): JsonResponse
    {
        return $this->respondWithSuccess(['item' => TransactionOutput::serialize($transaction)]);
    }

    #[Route('', 'create', methods: ['POST'])]
    #[OA\RequestBody(
        required: true,
        content: new Model(type: TransactionInput::class)
    )]
    #[OA\Response(
        response: 201,
        description: 'Creates Transaction',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(
                            property: 'item',
                            ref: new Model(type: TransactionOutput::class),
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                ),
            ],
        ),
    )]
    #[OA\Tag(name: 'Transaction')]
    public function create(#[MapRequestPayload] TransactionInput $input): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $transaction = $this->transactionService->create($input, $user);

        return $this->respondWithSuccess(['item' => TransactionOutput::serialize($transaction)], status: 201);
    }

    #[Route('/{transaction}', 'update', methods: ['PATCH'])]
    #[OA\RequestBody(required: true, content: new Model(type: TransactionInput::class))]
    #[OA\Response(
        response: 200,
        description: 'Transaction updated',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    properties: [
                        new OA\Property(
                            property: 'item',
                            ref: new Model(type: TransactionOutput::class),
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                ),
            ],
        ),
    )]
    #[OA\Tag(name: 'Transaction')]
    public function update(Transaction $transaction, #[MapRequestPayload] TransactionInput $input): JsonResponse
    {
        $transaction = $this->transactionService->update($transaction, $input);

        return $this->respondWithSuccess(['item' => TransactionOutput::serialize($transaction)]);
    }

    #[Route('/{transaction}', 'delete', methods: ['DELETE'])]
    #[OA\Response(response: 204, description: 'Transaction deleted')]
    #[OA\Tag(name: 'Transaction')]
    public function delete(Transaction $transaction): JsonResponse
    {
        $this->transactionRepository->delete($transaction);

        return $this->respondWithSuccess(null, status: 204);
    }
}
