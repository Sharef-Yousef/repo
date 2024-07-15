<?php

namespace App\Http\Controllers;

use App\Models\crudBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Search extends Controller
{
    // public function searchByNameAuth(Request $request)
    // {
    //     $vali = Validator::make($request->all(), [

    //         'name' => 'required'
    //     ]);
    //     if ($vali->fails()) {
    //         return response()->json([
    //             'status' => 0,
    //             'message' => $vali->errors(),
    //         ]);
    //     } else {
    //         $findBook = crudBook::where('nameAuth', $request->name)->get();
    //         if ($findBook->isNotEmpty()) {
    //             return response()->json([
    //                 'status' => 1,
    //                 'message ' => 'List of Book',
    //                 'data' => $findBook
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => 0,
    //                 'message ' => 'The Book is not Found',
    //             ]);
    //         }
    //     }
    // }

    // public function searchByCategory(Request $request)
    // {
    //     $category = $request->query('category');

    //     if (empty($category)) {
    //         return response()->json([
    //             'status' => 0,
    //             'message' => 'Category is required.',
    //         ]);
    //     }

    //     $category = strtolower($category);

    //     $findBook = crudBook::where('category', 'like', $category . '%')->get();

    //     if ($findBook->isNotEmpty()) {
    //         return response()->json([
    //             'status' => 1,
    //             'message' => 'List of Books',
    //             'data' => $findBook
    //         ]);
    //     } else {
    //         return response()->json([
    //             'status' => 0,
    //             'message' => 'No books found matching this category. Please try another search.'
    //         ]);
    //     }
    // }
    public function search(Request $request)
    {

        $vali = Validator::make($request->all(), [

            'data' => 'required'
        ]);
        if ($vali->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $vali->errors(),
            ]);
        } else {

            $findBook = crudBook::where('nameAuth', 'like', $request->data . '%')
                ->orwhere('nameBook', 'like', $request->data . '%')
                ->orwhere('category', 'like', $request->data . '%')
                ->get();
            // dd($findBook);
            if ($findBook->isEmpty()) {
                return response()->json([
                    'status' => 0,
                    'message ' => 'The Book is not Found',
                ]);
            } else {
                return response()->json([
                    'status' => 1,
                    'message ' => 'List of Book',
                    'data' => $findBook
                ]);
            }
        }
    }
    public function getAllAudioBooks()
    {
        // جلب كل الكتب الصوتية من قاعدة البيانات
        $audioBooks = crudBook::where('bookType', 'audio')->get();
        if ($audioBooks->isNotEmpty()) {
            return response()->json([
                'status' => 1,
                'message' => 'List of Audio Books',
                'data' => $audioBooks
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'No Audio Books Found',
            ]);
        }
    }
}
