<?php

namespace App\Http\Controllers;

use App\Models\crudBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CrudBookController extends Controller
{
    public function storeBook(Request $request)
    {
        $reg = Validator::make($request->all(), [
            'nameBook' => 'required|unique:crud_books,nameBook',
            'nameAuth' => 'required',
            'numOfPage' => 'required_if:bookType,text|numeric|nullable',
            'aboutTheBook' => 'required',
            'category' => 'required',
            'bookType' => 'required|in:text,audio',
            'bookImage' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // validate the image
            'bookFile' => 'required_if:bookType,text|file|mimes:pdf,epub,mobi,azw3,cbr,cbz,txt|max:1024000',
            'audioFile' => 'required_if:bookType,audio|file|mimes:mp3,ogg,wav,flac|max:1024000'
        ]);
        if ($reg->fails()) {
            return response()->json([
                "status : " => 0,
                "message : " => $reg->errors()
            ]);
        } else {
            $imageName = null;
            $fileName = null;
            $audioFileName = null;

            if ($request->hasFile('bookImage')) {
                $imageName = time() . '.' . $request->bookImage->extension();
                $request->bookImage->move(public_path('images'), $imageName);
            }
            if ($request->hasFile('bookFile')) {
                $fileName = time() . '.' . $request->bookFile->extension();
                $request->bookFile->move(public_path('files'), $fileName);
            }
            if ($request->hasFile('audioFile')) {
                $audioFileName = time() . '.' . $request->audioFile->extension();
                $request->audioFile->move(public_path('audio'), $audioFileName);
            }
            $newBook = crudBook::create([
                'nameBook' => $request->nameBook,
                'nameAuth' => $request->nameAuth,
                'numOfPage' => $request->numOfPage,
                'aboutTheBook' => $request->aboutTheBook,
                'category' => $request->category,
                'bookType' => $request->bookType,
                'bookImage' => $imageName,
                'bookFile' => $fileName,
                'audioFile' => $audioFileName
            ]);
        }
        return response()->json([
            'status' => 1,
            'message' => 'The Book was added',
            'data' => $newBook,
        ]);
    }
    public function deleteBook($id)
    {

        $findBook = crudBook::find($id);

        if ($findBook) {
            $findBook->delete();

            return response()->json([
                'status' => 1,
                'message' => 'The Book was deleted successfully'
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'Book not found'
            ]);
        }
    }
    public function updateBook(Request $request, $id)
    {
        if (crudBook::where("id", $id)->exists()) {
            $editBook = crudBook::find($id);
            $editBook->nameBook = !empty($request->nameBook) ? $request->nameBook : $editBook->nameBook;
            $editBook->nameAuth = !empty($request->nameAuth) ? $request->nameAuth : $editBook->nameAuth;
            $editBook->numOfPage = !empty($request->numOfPage) ? $request->numOfPage : $editBook->numOfPage;
            $editBook->aboutTheBook = !empty($request->aboutTheBook) ? $request->aboutTheBook : $editBook->aboutTheBook;
            $editBook->category = !empty($request->category) ? $request->category : $editBook->category;
            $editBook->bookType = !empty($request->bookType) ? $request->bookType : $editBook->bookType;
            if ($request->hasFile('bookImage')) {
                $imageName = time() . '.' . $request->bookImage->extension();
                $newImagePath = public_path('images') . '/' . $imageName;
                // Delete the current image if it exists
                if (file_exists(public_path('images') . '/' . $editBook->bookImage)) {
                    unlink(public_path('images') . '/' . $editBook->bookImage);
                }
                // Save the new image
                $request->bookImage->move(public_path('images'), $imageName);
                $editBook->bookImage = $imageName;
            }
            if ($request->hasFile('bookFile') && $editBook->bookType == 'text') {
                $fileName = time() . '.' . $request->bookFile->extension();
                // Delete the current file if it exists
                if ($editBook->bookFile && file_exists(public_path('files') . '/' . $editBook->bookFile)) {
                    unlink(public_path('files') . '/' . $editBook->bookFile);
                }
                // Save the new file            $request->bookFile->move(public_path('files'), $fileName);
                $editBook->bookFile = $fileName;
            }
            if ($request->hasFile('audioFile') && $editBook->bookType == 'audio') {
                $audioFileName = time() . '.' . $request->audioFile->extension();
                // Delete the current audio file if it exists
                if ($editBook->audioFile && file_exists(public_path('audio') . '/' . $editBook->audioFile)) {
                    unlink(public_path('audio') . '/' . $editBook->audioFile);
                }
                // Save the new audio file
                $request->audioFile->move(public_path('audio'), $audioFileName);
                $editBook->audioFile = $audioFileName;
            }
            $editBook->save();
            return response()->json([
                "status" => 1,
                "message" => "Book updated successfully"
            ]);
        } else {
            return response()->json([
                "status" => 0,
                "message" => "Book not found"
            ], 404);
        }
    }
    public function getBook($id)
    {

        $Book = crudBook::find($id);
        if ($Book) {
            return response()->json([
                'status' => 1,
                'message' => 'Book details found!',
                'data' => $Book,

            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'The book is not found!'
            ]);
        }
    }
    public function getAll()
    {
        $Book = crudBook::get();
        if ($Book) {
            return response()->json([
                'status' => 1,
                'message' => 'Book details found!',
                'data' => $Book
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'The book is not found!'
            ]);
        }
    }
    public function showImage($imageName)
    {
        $path = public_path('images/' . $imageName);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
    public function showImage1($imageName)
    {
        $path = public_path('images/' . $imageName);

        if (!file_exists($path)) {
            abort(404);
        }

        return asset('images/' . $imageName);
    }
    public function showBookFile($id)
    {
        $book = crudBook::find($id);

        if (!$book) {
            return response()->json([
                'status' => 0,
                'message' => 'Book not found',
            ]);
        }

        $pathToFile = public_path('files/' . $book->bookFile);
        return response()->download($pathToFile);
    }


}
