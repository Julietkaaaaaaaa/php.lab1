<?php

declare(strict_types=1);

namespace App\Tasks;

class Group
{
    private array $students = [];

    public function __construct(
        public int $group_id,
        public string $name
    ) {}

    public function addStudent(Student $student): void
    {
        $this->students[$student->id] = $student;
    }

    public function removeStudent(int $id): void
    {
        unset($this->students[$id]);
    }

    public function searchStudent(string $surname): ?Student
    {
        foreach ($this->students as $student) {
            if ($student->surname === $surname) {
                return $student;
            }
        }

        return null;
    }

    public function getStudents(): array
    {
        return $this->students;
    }
}
