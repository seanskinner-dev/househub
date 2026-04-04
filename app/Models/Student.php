<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['first_name', 'last_name', 'house_name', 'colour_hex', 'year_level', 'points'])]
class Student extends Model
{
    use HasFactory;

    /**
     * Get the student's full name.
     */
    public function getNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}