<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Questions;

class QuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $questions = [
            ['question_text' => 'Is there too much advertising on our web site?', 'is_active' => 'true'], 
            ['question_text' => 'Do you prefer to buy sweets which are made in your own country? Why or why not?', 'is_active' => 'false'], 
            ['question_text' => 'Mostly, what kinds of sweets do you buy online? What would you rather buy in person?', 'is_active' => 'true'], 
            ['question_text' => 'Do you enjoy visiting the sales?', 'is_active' => 'true'], 
        ];

        foreach($questions as $question) {
            $question_item = Questions::create($question);
            $question_item->save();
        }
    }
}