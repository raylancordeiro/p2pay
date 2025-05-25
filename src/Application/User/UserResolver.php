<?php

namespace App\Application\User;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;

class UserResolver
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function resolve(int $userId): User
    {
        $user = $this->userRepository->find($userId);
        if (!$user) {
            throw new UserNotFoundException('Pagador não encontrado.');
        }

        /* @var User $user */
        return $user;
    }
}
