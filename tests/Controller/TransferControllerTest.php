<?php

namespace App\Tests\Controller;

use App\Entity\Transfer;
use App\Entity\User;
use App\Enum\UserRole;
use App\Service\TransferService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TransferControllerTest extends WebTestCase
{
    private Generator $faker;
    private TransferService $transferService;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->faker = Factory::create();

        $connection = $this->createMock(Connection::class);
        $connection->method('transactional')->willReturnCallback(fn ($callback) => $callback());

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getConnection')->willReturn($connection);

        $em->expects($this->any())->method('persist');
        $em->expects($this->any())->method('flush');

        $this->transferService = new TransferService($em);
    }

    public function testSuccessfulTransfer(): void
    {
        $payer = $this->createFakeUser(UserRole::PERSON, 1000);
        $payee = $this->createFakeUser(UserRole::PERSON, 500);

        $transfer = $this->transferService->execute(200, $payer, $payee);

        $this->assertInstanceOf(Transfer::class, $transfer);
        $this->assertEquals(800, $payer->getBalance());
        $this->assertEquals(700, $payee->getBalance());
        $this->assertEquals(200, $transfer->getAmount());
        $this->assertSame($payer, $transfer->getPayer());
        $this->assertSame($payee, $transfer->getPayee());
    }

    public function testTransferFailsWithInsufficientBalance(): void
    {
        $payer = $this->createFakeUser(UserRole::PERSON, 50);
        $payee = $this->createFakeUser(UserRole::PERSON, 100);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Saldo insuficiente para transferência.');

        $this->transferService->execute(100, $payer, $payee);
    }

    public function testTransferFailsWithEmptyValue(): void
    {
        $client = static::createClient();

        $payerId = 99999;
        $payeeId = 99998;

        $client->request('POST', '/transfer', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'value' => '',
            'payer' => $payerId,
            'payee' => $payeeId,
        ]));

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());

        $json = json_decode($response->getContent(), true);

        $this->assertArrayHasKey('errors', $json);

        $this->assertArrayHasKey('value', $json['errors']);
        $this->assertEquals('This value should not be blank.', $json['errors']['value']);

        $this->assertArrayHasKey('payer', $json['errors']);
        $this->assertEquals("O usuário com ID {$payerId} não existe.", $json['errors']['payer']);

        $this->assertArrayHasKey('payee', $json['errors']);
        $this->assertEquals("O usuário com ID {$payeeId} não existe.", $json['errors']['payee']);
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
