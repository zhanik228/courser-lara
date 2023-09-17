<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $appends = ['modules', 'course_author'];

    public function getModulesAttribute() {
        return $this->courseModules;
    }

    public function courseModules() {
        return $this->hasMany(Module::class, 'course_id');
    }

    public function author() {
        return $this->belongsTo(User::class, 'authorId');
    }

    public function getCourseAuthorAttribute() {
        return $this->author;
    }

    // public function getCourseAuthorAttribute() {
    //     return $this->courseAuthor;
    // }
}
