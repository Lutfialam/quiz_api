<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CountDataController extends Controller
{
    public function __invoke()
    {
        $cache_time = 15;
        $counting = Cache::remember('user', $cache_time, function () {
            return [
                'user' => User::count(),
                'quiz' => Quiz::count(),
                'category' => Category::count(),
            ];
        });

        return response()->json([
            ...$counting,
            'cache_time' => $cache_time
        ]);
    }
}
