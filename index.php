<?php

declare(strict_types=1);

require_once 'vendor/autoload.php';

use App\Core\Database;
use App\Core\EntityFactory;
use App\Services\ConsoleLogger;
use App\Services\EntityService;
use App\Tasks\Student;
use App\Tasks\Group;
use App\Tasks\Worker;
use App\Tasks\EnterpriseManager;
use App\Tasks\SquareMatrix;

echo "<h2>Завдання 3: Патерн Singleton</h2>";
$db1 = Database::getInstance();
$db2 = Database::getInstance(); 

echo "<h2>Завдання 1 та 3: Фабрика та створення об'єктів</h2>";
$factory = new EntityFactory();

$tour = $factory->createEntity('tour', [
    'id' => 1,
    'createdAt' => date('Y-m-d H:i:s'),
    'title' => 'Морська пригода',
    'destination_country' => 'Туреччина',
    'start_date' => '2024-07-15',
    'duration_days' => 7,
    'base_price' => 15000.00
]);

$client = $factory->createEntity('client', [
    'id' => 101,
    'createdAt' => date('Y-m-d H:i:s'),
    'user_id' => 42,
    'birth_date' => '1995-08-24',
    'gender' => 'Жіноча'
]);

echo $tour->getDetails() . "<br>";
echo $client->getDetails() . "<br>";

echo "<h2>Завдання 2: Трейт та Інтерфейс (JSON Експорт)</h2>";
echo $tour->exportToJson() . "<br>";

echo "<h2>Завдання 4: Dependency Injection</h2>";
$logger = new ConsoleLogger();
$service = new EntityService($logger);

$service->processEntity($tour);
$service->processEntity($client);

echo "<h2>Завдання 6: Індивідуальні завдання (Варіант 18)</h2>";

echo "<h3>1. Композиція (Студенти та Група)</h3>";
$group = new Group(1, "КН-21");
$group->addStudent(new Student(1, "Олександр", "Петренко", "2005-04-12"));
$group->addStudent(new Student(2, "Марія", "Іваненко", "2006-01-22"));

$searched = $group->searchStudent("Іваненко");
echo "Знайдено студента: {$searched->name} {$searched->surname} <br>";

echo "<h3>2. Ієрархія працівників</h3>";
$worker = new Worker(15000);
echo "Зарплата робітника: " . $worker->calculateSalary() . " грн<br>";

// 15000 (база) + 5000 (цех) + 7000 (відділ) + 12000 (підприємство)
$director = new EnterpriseManager(15000, 5000, 7000, 12000);
echo "Зарплата керівника підприємства: " . $director->calculateSalary() . " грн<br>";

echo "<h3>3. Інкапсуляція (Матриця 3x3)</h3>";
$matrixData = [
    [2, -1, 3],
    [4, 1, 5],
    [-2, 3, 1]
];
$matrix = new SquareMatrix($matrixData);
echo "Сума головної діагоналі: " . $matrix->getMainDiagonalSum() . "<br>";
echo "Детермінант матриці: " . $matrix->getDeterminant() . "<br>";