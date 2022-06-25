<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Questions extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'question_text',
        'is_active'
    ];

    public function answer_questions()
    {
        return $this->hasMany(Answers::class);
    }

    public static function getTrue()
    {
       // return Questions::whereNull('parent_category_id')->with('children')->get();
    }
}