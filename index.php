<?php

use Faker\Factory;

require_once 'vendor/autoload.php';

require_once 'app/Services/EntityService.php';
require_once 'app/Services/DataProcessor.php';

echo '<h3>Завдання 2: Робота користувацької функції</h3>';
$totalPrice = calculateTotalPrice(1500.00, 2);
echo 'Загальна ціна туру для 2 осіб: '.$totalPrice.'<br><br>';

echo '<h3>Завдання 3: Анонімні функції та генерація даних</h3>';
$faker = Factory::create();

$prices = [];
for ($i = 0; $i < 10; $i++) {
    $prices[] = $faker->randomFloat(2, 100, 2000);
}

$minThreshold = 500.0;

$filteredPrices = array_filter($prices, function ($price) use ($minThreshold) {
    return $price > $minThreshold;
});

echo 'Згенеровані ціни: '.implode(', ', $prices).'<br>';
echo "Відфільтровані ціни (більше $minThreshold): ".implode(', ', $filteredPrices).'<br><br>';

echo '<h3>Завдання 4: Обробка винятків</h3>';
try {
    echo 'Спроба застосувати знижку 150%...<br>';
    $discounted = applyDiscount(1000.0, 150.0);
    echo 'Ціна зі знижкою: '.$discounted.'<br>';
} catch (Exception $e) {
    echo 'Помилка: '.$e->getMessage().'<br>';
} finally {
    echo 'Завершення спроби розрахунку.<br><br>';
}

echo '<h3>Завдання 5: Індивідуальне алгоритмічне завдання (Варіант 18)</h3>';

echo '<h4>Задача 1: Індекси елементів, більших за правого сусіда</h4>';
$numbersArray = [5, 2, 8, 9, 3, 4, 1];
echo 'Масив: '.implode(', ', $numbersArray).'<br>';
$result1 = getIndicesGreaterThanRight($numbersArray);
echo 'Індекси таких елементів: '.implode(', ', $result1['indices']).'<br>';
echo 'Кількість таких елементів: '.$result1['count'].'<br>';

echo '<h4>Задача 2: Поміняти місцями перше і останнє слово</h4>';
$text = 'PHP це чудова мова програмування';
echo 'Початковий рядок: '.$text.'<br>';
echo 'Результат: '.swapFirstAndLastWord($text).'<br>';

echo '<h4>Задача 3: Максимальне число у рядку</h4>';
$numberString = '10 45 3 99 21 7';
echo 'Рядок чисел: '.$numberString.'<br>';
echo 'Максимальне число: '.findMaxNumberInString($numberString).'<br>';
