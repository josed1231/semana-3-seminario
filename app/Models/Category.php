<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    /**@use HasFactory<CategoryFactory> */
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    public function tasks() {
        return $this->hasMany(Task::class);
    }

}
