<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        // if (Gate::denies('manage-categories')) {
        //     abort(403);
        // }

        return view('pages.questions.index');
    }
}
