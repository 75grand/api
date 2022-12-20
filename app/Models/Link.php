<?php

namespace App\Models;

use App\Casts\HashId;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Link extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function getTrackingUrl(): string
    {
        return url("/redirect/$this->id");
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}
