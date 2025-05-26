<?php

namespace App\Controller;

use App\Application\User\UserResolver;
use App\Form\TransferType;
use App\Service\TransferService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TransferController extends BaseController
{
    #[Route('/transfer', name: 'app_transfer', methods: ['POST'])]
    public function transfer(
        Request $request,
        FormFactoryInterface $formFactory,
        TransferService $transferService,
        UserResolver $userResolver,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $form = $formFactory->create(TransferType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->getErrors($form);
        }

        $payer = $userResolver->resolve($form->get('payer')->getData());
        $payee = $userResolver->resolve($form->get('payee')->getData());

        try {
            $transfer = $transferService->execute($form->get('value')->getData(), $payer, $payee);
        } catch (\Throwable $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['message'=> 'Transferência realizada com sucesso!', 'data' => [
            'id'     => $transfer->getId(),
            'amount' => $transfer->getAmount(),
            'payer'  => $transfer->getPayer()->getName(),
            'payee'  => $transfer->getPayee()->getName(),
        ]], Response::HTTP_CREATED);
    }
}
