<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'hire_date' => 'date',
        'salary' => 'float',
        'department_id' => 'integer',
    ];

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function address(): HasOne
    {
        return $this->hasOne(Address::class);
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employeeChangeLogs(): HasMany
    {
        return $this->hasMany(EmployeeChangeLog::class);
    }


    protected static function boot()
    {
        parent::boot();

        static::updated(function ($employee) {
            $changes = $employee->getChanges();
            $original = $employee->getOriginal();

            foreach ($changes as $key => $value) {
                if ($key !== 'updated_at') {
                    EmployeeChangeLog::create([
                        'employee_id' => $employee->id,
                        'change_type' => $key,
                        'old_value' => $original[$key] ?? null,
                        'new_value' => $value,
                        'changed_at' => now(),
                    ]);
                }
            }
        });
    }
}
