<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id 
 * @property string $name 
 * @property string $code 
 * @property string $url 
 * @property \Illuminate\Support\Carbon $created_at 
 * @property \Illuminate\Support\Carbon $updated_at 
 */
class Subject extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
