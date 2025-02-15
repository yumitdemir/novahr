<?php

    namespace Tests\Unit;

    use App\Models\Employee;
    use PHPUnit\Framework\TestCase;

    class EmployeeTest extends TestCase
    {
        public function testGuardedAttributes()
        {
            $employee = new Employee();
            $this->assertEquals([], $employee->getGuarded());
        }

        public function testCasts()
        {
            $employee = new Employee();
            $this->assertEquals([
                'id' => 'integer',
                'hire_date' => 'date',
                'salary' => 'float',
                'department_id' => 'integer',
            ], $employee->getCasts());
        }
    }
