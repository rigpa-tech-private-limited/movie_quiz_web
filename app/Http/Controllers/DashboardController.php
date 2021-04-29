<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Question;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $categories = Category::whereNotNull('created_at')->get();
        $cCount = count($categories);
        $questions = Question::whereNotNull('created_at')->where('status', '!=', '2')->get();
        $qCount = count($questions);
        $users = User::whereNotNull('created_at')->get();
        $uCount = count($users);

        return view('dashboard', [
            'cCount' => $cCount,
            'qCount' => $qCount,
            'uCount' => $uCount,
        ]);
    }
}
