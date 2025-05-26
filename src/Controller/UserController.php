<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Controller responsável pelo CRUD de usuários.
 */
final class UserController extends BaseController
{
    #[Route('/user', name: 'app_user_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory,
    ): JsonResponse {
        $user = new User();

        $form = $formFactory->create(UserType::class, $user);
        $form->submit(json_decode($request->getContent(), true));

        if (!$form->isValid()) {
            return $this->getErrors($form);
        }

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'id'      => $user->getId(),
            'name'    => $user->getName(),
            'email'   => $user->getEmail(),
            'cpf'     => $user->getCpf(),
            'role'    => $user->getRole()->value,
            'balance' => $user->getBalance(),
        ], Response::HTTP_CREATED);
    }

    /**
     * @throws InvalidArgumentException
     */
    #[Route('/user/balance/{id}', name: 'app_user_balance', methods: ['GET'])]
    public function showBalance(
        int $id,
        EntityManagerInterface $em,
        CacheInterface $cache,
    ): JsonResponse {
        $saldo = $cache->get('user_balance_'.$id, function (ItemInterface $item) use ($id, $em) {
            $item->expiresAfter(300);

            $user = $em->getRepository(User::class)->find($id);

            if (!$user) {
                throw $this->createNotFoundException("Usuário com ID {$id} não encontrado.");
            }

            return $user->getBalance();
        });

        return $this->json([
            'id'     => $id,
            'saldo'  => $saldo,
        ]);
    }
}
