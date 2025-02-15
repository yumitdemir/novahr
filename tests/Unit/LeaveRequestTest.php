<?php

namespace Tests\Unit;

use App\Models\LeaveRequest;
use PHPUnit\Framework\TestCase;

class LeaveRequestTest extends TestCase
{
    public function testGuardedAttributes()
    {
        $leaveRequest = new LeaveRequest();
        $this->assertEquals([], $leaveRequest->getGuarded());
    }

    public function testCasts()
    {
        $leaveRequest = new LeaveRequest();
        $this->assertEquals([
            'id' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'employee_id' => 'integer',
        ], $leaveRequest->getCasts());
    }
}
