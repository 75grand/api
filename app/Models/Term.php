<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id 
 * @property string $name 
 * @property string $code 
 * @property \Illuminate\Support\Carbon $start_date 
 * @property \Illuminate\Support\Carbon $end_date 
 * @property \Illuminate\Support\Carbon $created_at 
 * @property \Illuminate\Support\Carbon $updated_at 
 */
class Term extends Model
{
    use HasFactory;

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}
