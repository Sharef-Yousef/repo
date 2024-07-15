<?php

namespace App\Http\Controllers;

use App\Models\myBook;
use App\Models\crudBook;
use App\Models\evalBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class EvalBookController extends Controller
{
    //add evaluat to The book
    public function addEval(Request $request, $id)
    {

        $userID = auth()->user()->id;

        //check if user end the book

        $check = myBook::where([
            ['bookId', '=', $id],
            ['userId', '=', $userID],
            ['status', '=', 'As_Read']
        ])->first();

        if ($check) {
            //Find The Book
            $findBook = crudBook::find($id);
            if (!$findBook) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Book with id ' . $id . ' not found.'
                ]);
            }


            //check if user already evaluated
            $previousEval = evalBook::where('userId', $userID)
                ->where('bookId', $id)->first();
            if ($previousEval) {
                return response()->json([
                    'status :' => 0,
                    'message :' => 'You have already evaluated this book.'
                ]);
            }
            // evaluat the Book
            $validator = Validator::make($request->all(), [
                'evalBook' => 'required|integer|between:1,5',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    "status" => 0,
                    "message" => $validator->errors()
                ]);
            }
            // save evaluat

            $newEval = evalBook::create([
                'userId' => $userID,
                'bookId' => $findBook->id,
                'evalBook' => $request->evalBook,

            ]);

            return response()->json([
                'status' => 1,
                'message' => 'Rating added successfully',
                'data' => $newEval
            ]);
        } else {
            return response()->json([
                'status :' => 0,
                'message : ' => 'You cant Eval This book Because dosent Finish read!'
            ]);
        }
    }
    //إرجاع التقييم الاكثر لكتاب معين
    public function getMostEval($id)
    {
        $findBook = crudBook::find($id);

        if (!$findBook) {
            return response()->json([
                'status' => 0,
                'message' => 'Book with id ' . $id . ' not found.'
            ]);
        }

        //get the most evaluat Book
        $mostEvalBook = evalBook::where('bookId', $id)
            ->select('evalBook', DB::raw('count(*) as total'))
            ->groupBy('evalBook')
            ->orderBy('total', 'desc')
            ->first();

        return response()->json([
            'status' => 1,
            'mostCommonRating' => $mostEvalBook->evalBook
        ]);
    }
    //get The Most Rated Book
    public function getMostRatedBook()
    {
        // Get the book with the most evaluations
        $mostRatedBook = evalBook::select('bookId', DB::raw('count(*) as total'))
            ->groupBy('bookId')
            ->orderBy('total', 'desc')
            ->first();

        if (!$mostRatedBook) {
            return response()->json([
                'status' => 0,
                'message' => 'No book evaluations found.'
            ]);
        }

        // Find the book in the database
        $book = crudBook::find($mostRatedBook->bookId);

        if (!$book) {
            return response()->json([
                'status' => 0,
                'message' => 'Book with id ' . $mostRatedBook->bookId . ' not found.'
            ]);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Most rated book found!',
            'book' => $book
        ]);
    }
    //delet Eval 
    public function deletEval($id)
    {
        $userId = auth()->user()->id;

        $findEval = evalBook::where('id', $id)
            ->where('userId', $userId)->first();

        if (!$findEval) {
            return response()->json([
                'status' => 0,
                'message ' => 'you dont have any Eval for This book'
            ]);
        } else {
            $findEval->delete();

            return response()->json([
                'status ' => 1,
                'message' => 'you delet eval for this book !'
            ]);
        }
    }
    //Edit Eval
    public function editEval(Request $request, $id)
    {

        $vali = validator::make($request->all(), [
            'evalBook' => 'required|integer|between:1,5'
        ]);
        if ($vali->fails()) {
            return response()->json([

                'status ' => 0,
                'message' => $vali->errors()
            ]);
        } else {


            $userId = auth()->user()->id;

            $findEval = evalBook::where('userId', $userId)
                ->where('bookId', $id)->first();

            if ($findEval) {

                $findEval->evalBook = !empty($request->evalBook) ? $request->evalBook : $findEval->evalBook;

                $findEval->save();

                return response()->json([
                    'status ' => 1,
                    'message ' => 'Edit for your Eval is successfully'
                ]);
            } else {
                return response()->json([
                    'status ' => 0,
                    'message ' => 'the Eval is not found'
                ]);
            }
        }
    }
}
