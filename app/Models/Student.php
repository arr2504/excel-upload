<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $appends = ['classroom'];

    function getClassroomAttribute()
    {
        if ($this->classroom_id) {
            return Classroom::whereId($this->classroom_id)->first()->name;
        }
    }
}
