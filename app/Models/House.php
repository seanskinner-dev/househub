<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $fillable = ['name', 'points', 'colour_hex'];

    public function students()
    {
        return $this->hasMany(Student::class, 'house_name', 'name');
    }
}