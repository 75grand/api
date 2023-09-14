<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $hidden = ['pivot'];

    protected $casts = [
        'prerequisites' => 'array',
        'days' => 'array',
        'attendance_required' => 'boolean',
    ];

    public function distRequirements()
    {
        return $this->belongsToMany(DistRequirement::class);
    }

    public function professor()
    {
        return $this->belongsTo(Professor::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function crossListings()
    {
        return $this->belongsToMany(
            self::class,
            'cross_listings',
            'course_a',
            'course_b'
        );
    }

    /**
     * Get the lab courses associated with this course
     */
    public function labs()
    {
        return $this->belongsToMany(
            self::class,
            'lab_courses',
            'main_course_id',
            'lab_course_id'
        );
    }

    /**
     * Get the courses that require this one as a lab
     */
    public function labFor()
    {
        return $this->belongsToMany(
            self::class,
            'lab_courses',
            'lab_course_id',
            'main_course_id'
        );
    }
}
