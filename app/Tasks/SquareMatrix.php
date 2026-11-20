<?php

declare(strict_types=1);

namespace App\Tasks;

use InvalidArgumentException;

class SquareMatrix
{
    public function __construct(
        public readonly array $data
    ) {
        if (count($data) !== 3 || count($data[0]) !== 3) {
            throw new InvalidArgumentException("Матриця має бути 3x3");
        }
    }

    public function getMainDiagonalSum(): float|int
    {
        return $this->data[0][0] + $this->data[1][1] + $this->data[2][2];
    }

    public function getDeterminant(): float|int
    {
        $m = $this->data;
        return 
            $m[0][0] * ($m[1][1] * $m[2][2] - $m[1][2] * $m[2][1]) -
            $m[0][1] * ($m[1][0] * $m[2][2] - $m[1][2] * $m[2][0]) +
            $m[0][2] * ($m[1][0] * $m[2][1] - $m[1][1] * $m[2][0]);
    }
}