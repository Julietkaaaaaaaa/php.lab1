<?php

declare(strict_types=1);

/**
 * Задача 1: Знайти індекси елементів, більших за правого сусіда, та їх кількість.
 */
function getIndicesGreaterThanRight(array $numbers): array
{
    $indices = [];
    $count = count($numbers);

    for ($i = 0; $i < $count - 1; $i++) {
        if ($numbers[$i] > $numbers[$i + 1]) {
            $indices[] = $i;
        }
    }

    return [
        'indices' => $indices,
        'count' => count($indices),
    ];
}

/**
 * Задача 2: Поміняти місцями перше і останнє слово у рядку.
 */
function swapFirstAndLastWord(string $text): string
{
    $words = explode(' ', trim($text));

    if (count($words) < 2) {
        return $text;
    }

    $first = array_shift($words);
    $last = array_pop($words);

    array_unshift($words, $last);
    array_push($words, $first);

    return implode(' ', $words);
}

/**
 * Задача 3: Знайти максимальне число у рядку із числами, розділеними пробілами.
 */
function findMaxNumberInString(string $text): int
{
    $numbers = array_map('intval', explode(' ', trim($text)));

    return empty($numbers) ? 0 : max($numbers);
}