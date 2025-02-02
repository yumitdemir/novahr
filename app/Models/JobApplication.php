<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'application_date',
        'name',
        'surname',
        'cv',
        'email',
        'phone',
        'linkedin',
        'location',
        'current_job_title',
        'current_employer',
        'years_of_experience',
        'university',
        'certifications',
        'technical_skills',
        'soft_skills',
        'languages_spoken',
        'compatibility_rating',
        'job_opening_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'application_date' => 'date',
        'job_opening_id' => 'integer',
    ];

    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }
}
