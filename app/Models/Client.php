<?php

declare(strict_types=1);

namespace App\Models;

class Client extends AbstractEntity
{
    public function __construct(
        int $id,
        string $createdAt,
        public int $user_id,
        public string $birth_date,
        public string $gender
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getDetails(): string
    {
        return "Клієнт (User ID: {$this->user_id}), Дата народження: {$this->birth_date}, Стать: {$this->gender}";
    }
}
