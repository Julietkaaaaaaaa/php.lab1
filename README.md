# php.lab1

[![PHP CI Application](https://github.com/Julietkaaaaaaaa/php.lab1/actions/workflows/php-ci.yml/badge.svg)](https://github.com/Julietkaaaaaaaa/php.lab1/actions/workflows/php-ci.yml)

Цей репозиторій містить базовий PHP-додаток (калькулятор) із налаштованим CI/CD пайплайном за допомогою GitHub Actions.

Функціонал:
-Виконання базових математичних операцій (додавання та віднімання).
-Автоматична перевірка стилю коду за допомогою лінтера Laravel Pint.
-Автоматичний запуск модульних тестів за допомогою PHPUnit при кожному оновленні коду.

Bash
composer install
./vendor/bin/phpunit tests/Unit