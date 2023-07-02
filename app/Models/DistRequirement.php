<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistRequirement extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['pivot'];

    public function courses()
    {
        return $this->belongsToMany(Course::class);
    }
}
