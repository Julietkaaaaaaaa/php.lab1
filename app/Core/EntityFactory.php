<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Tour;
use App\Models\Client;
use Exception;

class EntityFactory
{
   public function createEntity(string $type, array $data)
    {
        return match ($type) {
            'tour' => new Tour(
                $data['id'], $data['createdAt'], $data['title'], 
                $data['destination_country'], $data['start_date'], 
                $data['duration_days'], $data['base_price']
            ),
            'client' => new Client(
                $data['id'], $data['createdAt'], $data['user_id'], 
                $data['birth_date'], $data['gender']
            ),
            default => throw new \Exception("Невідомий тип сутності"),
        };
    }
}