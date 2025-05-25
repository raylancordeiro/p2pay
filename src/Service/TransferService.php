<?php

namespace App\Service;

use App\Entity\Transfer;
use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class TransferService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @throws \Throwable
     */
    public function execute(int $value, User $payer, User $payee): Transfer
    {
        $connection = $this->em->getConnection();

        return $connection->transactional(function () use ($value, $payer, $payee) {
            if ($payer->getBalance() < $value) {
                throw new \RuntimeException('Saldo insuficiente para transferência.');
            }

            if (UserRole::SHOPKEEPER == $payer->getRole()) {
                throw new AccessDeniedHttpException('Usuário do tipo Logista não pode realizar transferências');
            }

            $payer->setBalance($payer->getBalance() - $value);
            $payee->setBalance($payee->getBalance() + $value);

            $transfer = new Transfer();
            $transfer->setAmount($value);
            $transfer->setPayer($payer);
            $transfer->setPayee($payee);

            $this->em->persist($transfer);
            $this->em->flush();

            return $transfer;
        });
    }
}
