<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Transfer;
use App\Entity\User;
use App\Enum\UserRole;
use App\Service\TransferService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TransferServiceTest extends TestCase
{
    private TransferService $service;

    /** @var EntityManagerInterface&MockObject */
    private $em;

    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create('pt_BR');

        /** @var EntityManagerInterface&MockObject $em */
        $em = $this->createMock(EntityManagerInterface::class);

        $connection = $this->createMock(Connection::class);
        $em->method('getConnection')->willReturn($connection);
        $connection->method('transactional')->willReturnCallback(fn (callable $cb) => $cb());

        $this->em      = $em;
        $this->service = new TransferService($this->em);
    }

    public function testSuccessfulTransfer(): void
    {
        $payer = $this->createFakeUser(UserRole::PERSON, 100);
        $payee = $this->createFakeUser(UserRole::SHOPKEEPER, 0);

        $this->em->expects($this->once())->method('persist')->with($this->isInstanceOf(Transfer::class));
        $this->em->expects($this->once())->method('flush');

        $result = $this->service->execute(90, $payer, $payee);

        $this->assertEquals(10, $payer->getBalance());
        $this->assertEquals(90, $payee->getBalance());
        $this->assertInstanceOf(Transfer::class, $result);
    }

    public function testTransferThrowsExceptionWhenBalanceIsInsufficient(): void
    {
        $payer = $this->createFakeUser(UserRole::PERSON, 50);
        $payee = $this->createFakeUser(UserRole::SHOPKEEPER, 0);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Saldo insuficiente para transferência.');

        $this->service->execute(100, $payer, $payee);
    }

    public function testTransferFailsIfPayerIsNotPerson(): void
    {
        $payer = $this->createFakeUser(UserRole::SHOPKEEPER, 100);
        $payee = $this->createFakeUser(UserRole::PERSON, 0);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Usuário do tipo Logista não pode realizar transferências');

        $this->service->execute(50, $payer, $payee);
    }

    private function createFakeUser(UserRole $role, int $balance): User
    {
        $user = new User();
        $user->setName($this->faker->name());
        $user->setCpf($this->faker->numerify('###########'));
        $user->setEmail($this->faker->unique()->safeEmail());
        $user->setPassword($this->faker->password(8));
        $user->setRole($role);
        $user->setBalance($balance);

        return $user;
    }
}
