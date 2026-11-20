<?php

declare(strict_types=1);

namespace App\Models;

use App\Interfaces\ExportableInterface;
use App\Traits\JsonExportTrait;

class Tour extends AbstractEntity implements ExportableInterface
{
    use JsonExportTrait;

    public function __construct(
        int $id,
        string $createdAt,
        public string $title,
        public string $destination_country,
        public string $start_date,
        public int $duration_days,
        public float $base_price
    ) {
        parent::__construct($id, $createdAt);
    }

    public function getDetails(): string
    {
        return "Тур: {$this->title} ({$this->destination_country}), Тривалість: {$this->duration_days} днів, Ціна: {$this->base_price} грн. Старт: {$this->start_date}";
    }
}
