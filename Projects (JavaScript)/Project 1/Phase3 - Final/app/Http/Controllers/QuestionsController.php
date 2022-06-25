<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Questions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class QuestionsController extends Controller
{
    public function store(StoreAnswersRequest $request)
    {
        //$questions = Questions::getAll();
        //return view('survey.questions', compact('questions'));
    }

    public function show_true()
    {
        $questions = Questions::where('is_active = true');
        return view('survey.questions', compact('questions'));
    }
}
