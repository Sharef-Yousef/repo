<?php

namespace App\Http\Controllers;

use App\Models\BookShelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Shelf_Books;

class BookShelfController extends Controller
{
    public function newShelf(Request $request)
    {
        $userId = auth()->user()->id;

        $reg = Validator::make($request->all(), [
            'nameShelf' => 'required|string',
        ]);

        if ($reg->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $reg->errors()
            ]);
        }

        // Check if the user already has a shelf with the same name
        $existingShelf = BookShelf::where('nameShelf', $request->nameShelf)
            ->where('userId', $userId)
            ->first();

        if ($existingShelf) {
            return response()->json([
                "status" => 0,
                "message" => "You already have a shelf with this name"
            ]);
        }

        $shelf = new BookShelf;
        $shelf->nameShelf = $request->nameShelf;
        $shelf->userId = $userId;
        $shelf->save();

        return response()->json([
            'status' => 1,
            'message' => 'Shelf created successfully',
            'data' => $shelf
        ]);
    }
    public function addBookToShelf($shelfId, $bookId)
    {
        $userId = auth()->user()->id;

        $shelf = BookShelf::where('id', $shelfId)
            ->where('userId', $userId)
            ->first();

        if (!$shelf) {
            return response()->json([
                "status" => 0,
                "message" => "Shelf not found"
            ]);
        }

        // Check if the book is already in the shelf
        $existingBookInShelf = Shelf_Books::where('shelfId', $shelfId)
            ->where('bookId', $bookId)
            ->first();

        if ($existingBookInShelf) {
            return response()->json([
                "status" => 0,
                "message" => "Book already exists in this shelf"
            ]);
        }

        $bookInShelf = new Shelf_Books;
        $bookInShelf->shelfId = $shelfId;
        $bookInShelf->bookId = $bookId;
        $bookInShelf->save();

        return response()->json([
            'status' => 1,
            'message' => 'Book added to shelf successfully'
        ]);
    }
    public function getUserShelves()
    {
        $userId = auth()->user()->id;

        $shelves = BookShelf::where('userId', $userId)->get();

        return response()->json([
            'status' => 1,
            'message' => 'User shelves retrieved successfully',
            'data' => $shelves
        ]);
    }
    public function getBooksInShelf($shelfId)
    {
        $userId = auth()->user()->id;

        $shelf = BookShelf::where('id', $shelfId)
            ->where('userId', $userId)
            ->first();

        if (!$shelf) {
            return response()->json([
                "status" => 0,
                "message" => "Shelf not found"
            ]);
        }

        $books = Shelf_Books::where('shelfId', $shelfId)
            ->join('crud_books', 'shelf_books.bookId', '=', 'crud_books.id')
            ->get();

        return response()->json([
            'status' => 1,
            'message' => 'Books in shelf retrieved successfully',
            'data' => $books
        ]);
    }
    public function updateShelf(Request $request, $shelfId)
    {
        $userId = auth()->user()->id;

        $shelf = BookShelf::where('id', $shelfId)
            ->where('userId', $userId)
            ->first();

        if (!$shelf) {
            return response()->json([
                "status" => 0,
                "message" => "Shelf not found"
            ]);
        }

        $reg = Validator::make($request->all(), [
            'nameShelf' => 'required|string',
        ]);

        if ($reg->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $reg->errors()
            ]);
        }

        $shelf->nameShelf = $request->nameShelf;
        $shelf->save();

        return response()->json([
            'status' => 1,
            'message' => 'Shelf updated successfully',
            'data' => $shelf
        ]);
    }
    public function deletBookFromShelf($shelfId, $bookId)
    {
        $userId = auth()->user()->id;

        $shelf = BookShelf::where('id', $shelfId)
            ->where('userId', $userId)
            ->first();

        if (!$shelf) {
            return response()->json([
                "status" => 0,
                "message" => "Shelf not found"
            ]);
        }

        $bookInShelf = Shelf_Books::where('shelfId', $shelfId)
            ->where('bookId', $bookId)
            ->first();

        if (!$bookInShelf) {
            return response()->json([
                "status" => 0,
                "message" => "Book not found in this shelf"
            ]);
        }

        $bookInShelf->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Book removed from shelf successfully'
        ]);
    }
    public function deleteShelf($shelfId)
    {
        $userId = auth()->user()->id;

        $shelf = BookShelf::where('id', $shelfId)
            ->where('userId', $userId)
            ->first();

        if (!$shelf) {
            return response()->json([
                "status" => 0,
                "message" => "Shelf not found"
            ]);
        }

        // Delete books in the shelf first
        Shelf_Books::where('shelfId', $shelfId)->delete();

        // Then delete the shelf
        $shelf->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Shelf deleted successfully'
        ]);
    }
}
