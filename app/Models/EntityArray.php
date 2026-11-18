<?php

$tourCategories = [
    'Екскурсійний тур',
    'Пляжний відпочинок',
    'Гірськолижний тур',
    'Оздоровчий тур',
    'Круїз',
];

// Сортування за алфавітом
sort($tourCategories);

$tourItem = [
    'id' => 101,
    'title' => 'Морська пригода в Анталії',
    'destination' => 'Туреччина',
    'price' => 750.50,
    'durationDays' => 7,
];
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Завдання 1: Масиви та класи</title>
</head>
<body>
    <h2>Відсортовані категорії турів:</h2>
    <ul>
        <?php foreach ($tourCategories as $category) { ?>
            <li><?= htmlspecialchars($category) ?></li>
        <?php } ?>
    </ul>

    <h2>Інформація про тур:</h2>
    <ul>
        <?php foreach ($tourItem as $key => $value) { ?>
            <li><strong><?= htmlspecialchars((string) $key) ?>:</strong> <?= htmlspecialchars((string) $value) ?></li>
        <?php } ?>
    </ul>
</body>
</html>