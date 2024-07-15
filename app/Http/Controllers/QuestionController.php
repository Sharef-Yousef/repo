<?php

namespace App\Http\Controllers;

use App\Models\crudBook;
use App\Models\question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function addQuestion(Request $request, $id)
    {
        $findBook = crudBook::find($id);

        if (!$findBook) {
            return response()->json([
                'status' => 0,
                'message' => 'Book with id ' . $id . ' not found.'
            ]);
        }

        $userID = auth()->user()->id;

        $validator = Validator::make($request->all(), [
            'question' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $validator->errors()
            ]);
        }

        $newQuestion = question::create([
            'userId' => $userID,
            'bookId' => $findBook->id,
            'question' => $request->question,
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Question added successfully',
            'data' => $newQuestion
        ]);
    }
    public function editQusetion(Request $request, $id)
    {
        $findQues = question::find($id);
        if (!$findQues) {
            return response()->json([
                'status ' => 0,
                'message ' => 'The Question is not found'
            ]);
        } else {
            $findQues->question = !empty($request->question) ? $request->question : $findQues->question;

            $findQues->save();

            return response()->json([
                'status ' => 1,
                'message ' => 'Edit for your question is successfully'
            ]);
        }
    }
    public function getQuestion($id)
    {

        $findQue = question::find($id)->first();
        if (!$findQue) {
            return response()->json([
                'status' => 0,
                'message' => 'The question is not found'
            ]);
        } else {
            return response()->json([
                'staus' => 1,
                'message' => 'Question information : ',
                'data ' => $findQue
            ]);
        }
    }
    public function getAllQues()
    {
        $getAll = question::get();
        if ($getAll->isEmpty()) {
            return response()->json([[
                'status ' => 0,
                'message ' => 'dose not have any Question '
            ]]);
        } else {
            return response()->json([
                'status' => 1,
                'message' => 'Question Detalis!',
                'data' => $getAll
            ]);
        }
    }
    public function getQuesBook($id)
    {

        $getBook = question::where('bookId', $id)->get();
        if (!$getBook) {
            return response()->json([
                'status ' => 0,
                'message ' => 'The book dons not have any Question'
            ]);
        } else {
            return response()->json([
                'status ' => 1,
                'message ' => 'The Question for this book',
                'data' => $getBook
            ]);
        }
    }
}
