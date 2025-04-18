<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
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
        'start_date' => 'date',
        'end_date' => 'date',
        'employee_id' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $user = auth()->user()->load('employee');
            if ($user && !$user->can('update_all_leave::request')) {
                $model->employee_id = $user->employee->id;
            }
            if ($user && !$user->can('create_all_leave::request')) {
                $model->status = 'pending';
            }
        });
    }
}
