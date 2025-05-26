<?php

namespace App\Service;

use App\Entity\Transfer;
use App\Entity\User;
use App\Enum\UserRole;
use App\Message\NotifyMessage;
use App\Service\Integration\AuthorizationIntegrationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\MessageBusInterface;

class TransferService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly AuthorizationIntegrationService $authorizationService,
        private readonly MessageBusInterface $bus,
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function execute(int $value, User $payer, User $payee): Transfer
    {
        $connection = $this->em->getConnection();

        return $connection->transactional(function () use ($value, $payer, $payee) {
            $payer->setBalance($payer->getBalance() - $value);
            $payee->setBalance($payee->getBalance() + $value);

            $transfer = new Transfer();
            $transfer->setAmount($value);
            $transfer->setPayer($payer);
            $transfer->setPayee($payee);

            $this->transferValidator($transfer);

            $this->em->persist($transfer);
            $this->em->flush();

            $this->bus->dispatch(new NotifyMessage(
                value: $value,
                payeeName: $transfer->getPayee()->getName()
            ));

            return $transfer;
        });
    }

    private function transferValidator($transfer): void
    {
        if ($transfer->getPayer()->getBalance() < $transfer->getAmount()) {
            throw new \RuntimeException('Saldo insuficiente para transferência.');
        }

        if (UserRole::SHOPKEEPER == $transfer->getPayer()->getRole()) {
            throw new AccessDeniedHttpException('Usuário do tipo Logista não pode realizar transferências');
        }

        $this->authorizationService->authorize();
    }
}
