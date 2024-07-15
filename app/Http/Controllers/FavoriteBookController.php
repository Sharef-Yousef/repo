<?php

namespace App\Http\Controllers;

use App\Models\crudBook;
use App\Models\favoriteBook;
use Illuminate\Http\Request;

class FavoriteBookController extends Controller
{

    public function favoriteBook($id)
    {

        $userId = auth()->user()->id;

        //find The Book
        $findBook = crudBook::where('id', $id)->first();

        if (!$findBook) {
            return response()->json([
                'status' => 0,
                'message' => 'Could not find the book'
            ]);
        }
        //insert the book to favorite list
        $favBook = favoriteBook::create([
            'userId' => $userId,
            'bookId' => $findBook->id
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'The Book was added to Favorite',
            'data' => $favBook
        ]);
    }
    public function getFavBook()
    {

        $userId = auth()->user()->id;
        // find the user to get your favorite list
        $getBooks = favoriteBook::where('userId', $userId)->get();

        if (!$getBooks) {
            return response()->json([
                'status ' => 0,
                'message ' => 'you dont have any book'
            ]);
        } else {
            // add the book to array list of favorite book
            $favIds = [];
            foreach ($getBooks as $book) {
                $favIds[] = $book->bookId;
            }
            if ($favIds == null) {
                return response()->json([
                    'status ' => 0,
                    'message ' => 'You dont Have any favorite Book',

                ]);
            } else {
                return response()->json([
                    'status ' => 1,
                    'message ' => 'list of favorite Book',
                    'data' =>  $favIds
                ]);
            }
        }
    }
    public function deleteBookFav($id)
    {
        // الحصول على معرف المستخدم الحالي
        $userId = auth()->user()->id;

        // البحث عن الكتاب في المفضلة للمستخدم الحالي
        $findBook = favoriteBook::where('id', $id)
            ->where('userId', $userId)->first();

        // التحقق مما إذا كان الكتاب موجودًا في المفضلة
        if ($findBook) {
            // حذف الكتاب من المفضلة
            $findBook->delete();

            // إعادة استجابة ناجحة
            return response()->json([
                'status' => 1,
                'message' => 'The book was deleted from favorites successfully'
            ]);
        } else {
            // إعادة استجابة توضح أن الكتاب غير موجود
            return response()->json([
                'status' => 0,
                'message' => 'Book not found'
            ]);
        }
    }
}
