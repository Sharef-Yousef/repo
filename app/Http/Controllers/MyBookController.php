<?php

namespace App\Http\Controllers;

use getID3;
use App\Models\myBook;
use App\Models\crudBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyBookController extends Controller
{


    public function addToMyBook($bookId)
    {
        $userId = auth()->user()->id;

        $find = myBook::where([
            ['bookId', '=', $bookId],
            ['userId', '=', $userId]
        ])->first();

        if ($find) {
            return response()->json([
                'status' => 0,
                'message ' => 'You have already added this book to your books!'
            ]);
        } else {
            $book = crudBook::find($bookId);
            $pageRead = 0;

            // Check if the book is audio
            if ($book && $book->bookType == 'audio') {
                $pageRead = 0; // Initialize to 0 for audio books
            }

            $add = myBook::create([
                'userId' => $userId,
                'bookId' => $bookId,
                'status' => 'not_Read',
                'pageRead' => $pageRead
            ]);

            return response()->json([
                'status' => 1,
                'data' => $add
            ]);
        }
    }
    public function updateBookReadStatus(int $bookId, float $minutesOrPagesRead)
    {
        $userId = auth()->user()->id;

        // Retrieve the book instance
        $book = crudBook::find($bookId);

        // Check if the book exists
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }

        // Update the myBook instance
        $myBook = myBook::where([
            ['bookId', '=', $bookId],
            ['userId', '=', $userId]
        ])->first();

        if (!$myBook) {
            return response()->json(['error' => 'User book record not found'], 404);
        }

        if ($book->bookType == 'audio') {
            // Path to the audio file in the public/audio directory
            $audioFilePath = public_path('audio/' . $book->audioFile);

            // Check if the file exists
            if (!file_exists($audioFilePath)) {
                return response()->json(['error' => 'Audio file not found'], 404);
            }

            // Use getID3 library to get the duration of the audio file
            $getID3 = new getID3;
            $fileInfo = $getID3->analyze($audioFilePath);
            $totalDurationSeconds = $fileInfo['playtime_seconds'];

            // Convert minutesRead to seconds
            $minutesReadSeconds = $minutesOrPagesRead * 60;
            $totalMinutesReadSeconds = $myBook->totalMinutesRead * 60 + $minutesReadSeconds;

            // Check if total minutes read exceeds total duration
            if ($totalMinutesReadSeconds >= $totalDurationSeconds) {
                $myBook->totalMinutesRead = $totalDurationSeconds / 60; // Convert back to minutes
                $myBook->status = 'As_Read'; // Set status to As_Read
                $myBook->save();

                return response()->json(['message' => 'You have reached the end of the audiobook']);
            } else if ($totalMinutesReadSeconds > 0) {
                $myBook->totalMinutesRead = $totalMinutesReadSeconds / 60; // Convert back to minutes
                $myBook->status = 'Reading'; // Set status to Reading
                $myBook->save();

                return response()->json(['message' => 'Audiobook read status updated successfully']);
            } else {
                $myBook->status = 'Not_Read'; // Set status to Not_Read
                $myBook->save();

                return response()->json(['message' => 'Audiobook read status updated successfully']);
            }
        } else {
            // For non-audio books, update based on pages read

            $totalPagesRead = $myBook->pageRead + $minutesOrPagesRead;
            // Check if pagesRead exceeds total pages
            if ($totalPagesRead >= $book->numOfPage) {
                $myBook->pageRead = $book->numOfPage;
                $myBook->status = 'As_Read'; // Set status to As_Read
                $myBook->save();

                return response()->json(['message' => 'You have reached the end of the book']);
            } else if ($totalPagesRead > 0) {
                $myBook->pageRead = $totalPagesRead;
                $myBook->status = 'Reading'; // Set status to Reading
                $myBook->save();

                return response()->json(['message' => 'Book read status updated successfully']);
            } else {
                $myBook->status = 'Not_Read'; // Set status to Not_Read
                $myBook->save();

                return response()->json(['message' => 'Book read status updated successfully']);
            }
        }
    }
    public function getStatusBook($bookId)
    {
        $userId = auth()->user()->id;

        // Check if the user has any books
        $userBooks = myBook::where('userId', $userId)->first();

        if (!$userBooks) {
            return response()->json([
                'status' => 0,
                'message' => 'The user has no books'
            ], 404);
        }

        // Check if the specific book exists for the user
        $Book = myBook::where('bookId', $bookId)
            ->where('userId', $userId)->first();
        if (!$Book) {
            return response()->json([
                'status' => 0,
                'message' => 'The book is not found for this user'
            ], 404);
        }
        // If the user has books and the specific book exists, return the book's status
        return response()->json([
            'bookId' => $Book->bookId,
            'status' => $Book->status,
        ]);
    }
    public function getMostCommonBook()
    {
        $mostCommonBooks = MyBook::select('bookId', DB::raw('count(userId) as userCount'))
            ->groupBy('bookId')
            ->orderBy('userCount', 'desc')
            ->get();

        if ($mostCommonBooks->isEmpty()) {
            return response()->json([
                'message' => 'No books found'
            ]);
        } else {
            return response()->json($mostCommonBooks);
        }
    }
    public function getTotalPagesRead()
    {
        $userId = auth()->user()->id;

        $totalPagesRead = 0;
        $totalMinutesRead = 0;

        $myBooks = myBook::where('userId', $userId)
            ->get();

        foreach ($myBooks as $myBook) {
            //  استعلام  لجلب  bookType  من  جدول  books
            $book = crudBook::find($myBook->bookId);

            if ($book) {
                if ($book->bookType === 'text') {
                    $totalPagesRead += $myBook->pageRead;
                } elseif ($book->bookType === 'audio') {
                    $totalMinutesRead += $myBook->totalMinutesRead;
                }
            }
        }
        return response()->json([
            'userId' => $userId,
            'totalPagesRead' => $totalPagesRead,
            'totalMinutesRead' => $totalMinutesRead,
        ]);
    }
    public function getMyBook()
    {

        $userId = auth()->user()->id;

        $find = myBook::where('userId', $userId)->get();

        if ($find->isNotEmpty()) {
            return response()->json([
                'status ' => 1,
                'message ' => $find
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'You dont have any book in MyBook'
            ]);
        }
    }
    public function getAsRead()
    {
        $userId = auth()->user()->id;

        $find = myBook::where('userId', $userId)
            ->where('status', 'As_Read')->get();

        if ($find->isNotEmpty()) {
            return response()->json([
                'status' => 1,
                'messsage ' => 'Detalis!',
                'data : ' => $find
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'messsage ' => 'you dont have any book',
            ]);
        }
    }
    public function getReading()
    {
        $userId = auth()->user()->id;

        $find = myBook::where('userId', $userId)
            ->where('status', 'Reading')->get();

        if ($find->isNotEmpty()) {
            return response()->json([
                'status' => 1,
                'message' => 'Details!',
                'data' => $find
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'You dont have any book currently being read.',
            ]);
        }
    }
    public function getNotRead()
    {
        $userId = auth()->user()->id;

        $find = myBook::where('userId', $userId)
            ->where('status', 'not_Read')->get();

        if ($find->isNotEmpty()) {
            return response()->json([
                'status' => 1,
                'message' => 'Details!',
                'data' => $find
            ]);
        } else {
            return response()->json([
                'status' => 0,
                'message' => 'You dont have any book currently being read.',
            ]);
        }
    }
}
