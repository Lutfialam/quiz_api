<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Quiz;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    private function store_image(Request $request)
    {
        $image_name = 'default.png';
        if ($request->has('image')) {
            $image = $request->file('image');
            $image_name = "QP-" . Carbon::now()->timestamp . Str::random(8) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $image_name);
        }
        return $image_name;
    }

    private function quiz_value(Request $request)
    {
        return [
            'name' => $request->name,
            'time' => (int) $request->time,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'user_id' => Auth::user()->id,
            'slug' => Str::slug($request->name),
            'image' => $this->store_image($request),
        ];
    }

    private function question_value($question)
    {
        return [
            'answer' => $question->answer,
            'question' => $question->question,
            'first_choice' => $question->first_choice,
            'second_choice' => $question->second_choice,
            'third_choice' => $question->third_choice,
            'fourth_choice' => $question->fourth_choice,
        ];
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $quizzes = Quiz::when($request->search, function ($query, $search) {
            return $query
                ->where('name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%")
                ->orWhereHas('categories', function ($query) use ($search) {
                    return $query->where('name', 'like', "%$search%");
                });
        })->with(['users', 'categories', 'questions'])->orderBy('created_at', 'DESC')->paginate(10);

        return response()->json($quizzes);
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
     * @param  \App\Http\Requests\StoreQuizRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreQuizRequest $request)
    {
        DB::transaction(function () use ($request) {
            $quiz = Quiz::create($this->quiz_value($request));

            $questions = collect(json_decode($request->questions));
            $questions->map(function ($question) use ($quiz) {
                $quiz->questions()->create($this->question_value($question));
            });
        });

        return response()->json([
            'status' => 'success',
            'message' => 'quiz created successfully'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function show(Quiz $quiz)
    {
        return response()->json($quiz->load(['users', 'categories', 'questions']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function edit(Quiz $quiz)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateQuizRequest  $request
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        DB::transaction(function () use ($request, $quiz) {
            info($this->quiz_value($request));
            $quiz->update($this->quiz_value($request));

            $questions = collect(json_decode($request->questions));

            $questions->map(function ($question) use ($quiz) {
                $quiz->questions()->updateOrCreate(
                    ['id' => $question->id ?? 0],
                    $this->question_value($question)
                );
            });

            if ($request->has('deleted_question')) {
                $deleted = collect(json_decode($request->deleted_question));
                $deleted->map(function ($delete) {
                    Question::where('id', $delete)->delete();
                });
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'quiz created successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Quiz  $quiz
     * @return \Illuminate\Http\Response
     */
    public function destroy(Quiz $quiz)
    {
        DB::transaction(function () use ($quiz) {
            $quiz->questions()->delete();
            $quiz->delete();
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Quiz deleted successfully'
        ]);
    }

    public function listHiddenAnswer()
    {
        $quiz = Quiz::with(['categories', 'questions' => function ($question) {
            return $question->select([
                'id',
                'quiz_id',
                'question',
                'first_choice',
                'second_choice',
                'third_choice',
                'fourth_choice'
            ]);
        }])->paginate(10);

        return response()->json($quiz);
    }

    public function hiddenAnswer(Request $request)
    {
        $quiz = Quiz::with(['questions' => function ($question) {
            return $question->select([
                'id',
                'quiz_id',
                'question',
                'first_choice',
                'second_choice',
                'third_choice',
                'fourth_choice'
            ]);
        }])->findOrFail($request->quiz_id);

        return response()->json($quiz);
    }
}
