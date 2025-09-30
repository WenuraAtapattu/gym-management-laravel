<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FitnessClass extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'instructor_id',
        'schedule',
        'duration_minutes',
        'max_capacity',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'schedule' => 'datetime',
    ];

    /**
     * Get the instructor that teaches the fitness class.
     */
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Get the attendances for the fitness class.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_id');
    }
}
