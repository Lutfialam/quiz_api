<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\History;
use Illuminate\Http\Request;
use App\Http\Requests\StoreHistoryRequest;
use App\Http\Requests\UpdateHistoryRequest;

class HistoryController extends Controller
{
    protected $score = 0;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = History::when($request->search, function ($query, $q) {
            return $query->whereHas('quiz', function ($quiz) use ($q) {
                return $quiz
                    ->where('name', 'like', '%' . $q . '%')
                    ->whereHas('categories', function ($category) use ($q) {
                        return $category->where('name', 'like', '%' . $q . '%');
                    });
            });
        })->where(['user_id' => $request->user()->id])->with('quiz')->paginate(10);

        return response()->json($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreHistoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHistoryRequest $request)
    {
        $quiz = Quiz::findOrFail($request->quiz_id);
        $userAnswerList = collect($request->questions);

        $quiz->questions->map(function ($question) use ($userAnswerList) {
            $userAnswer = $userAnswerList->where('id', $question->id)->first();
            if ($question->answer == $userAnswer['answer']) {
                $this->score++;
            }
        });

        $history = History::create([
            'score' => $this->score,
            'time' => $request->time,
            'user_id' => $request->user()->id,
            'quiz_id' => $request->quiz_id,
        ]);

        $this->score = 0;
        return response()->json([
            'status' => 'success',
            'history' => $history,
            'message' => 'history created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\History  $history
     * @return \Illuminate\Http\Response
     */
    public function show(History $history)
    {
        $data = $history->load(['user', 'quiz' => function ($quiz) {
            return $quiz->with('questions');
        }]);

        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\History  $history
     * @return \Illuminate\Http\Response
     */
    public function edit(History $history)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateHistoryRequest  $request
     * @param  \App\Models\History  $history
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHistoryRequest $request, History $history)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\History  $history
     * @return \Illuminate\Http\Response
     */
    public function destroy(History $history)
    {
        $history->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'history deleted successfully'
        ]);
    }
}
