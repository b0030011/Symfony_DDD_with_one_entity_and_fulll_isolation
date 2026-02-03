<?php

declare(strict_types=1);

namespace App\Presentation\Api\User\ViewModel;

use App\Domain\User\Entity\User;

class UserViewModel
{
    private array $data;

    public function __construct(User $user)
    {
        $this->data = [
            'id' => $user->getId()?->value(),
            'email' => $user->getEmail()->value(),
            'roles' => $user->getRoles()->value(),
            'address' => $user->getAddress()->value(),
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
