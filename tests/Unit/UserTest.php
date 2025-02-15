<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testFillableAttributes()
    {
        $user = new User();
        $this->assertEquals(['name', 'email', 'password', 'employee_id'], $user->getFillable());
    }

    public function testHiddenAttributes()
    {
        $user = new User();
        $this->assertEquals(['password', 'remember_token'], $user->getHidden());
    }
}
