<?php

namespace App\Http\Controllers;

use getID3;
use App\Models\crudBook;
use App\Models\timeTable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class TimeTableController extends Controller
{


    public function createTableTime(Request $request, $id)
    {
        // Get the authenticated user's ID
        $usersId = auth()->user()->id;

        // Validate the request data
        $vali = Validator::make($request->all(), [
            'timePerMinut' => 'required|numeric|min:1',
            'startDate' => 'required|date|after_or_equal:today|date_format:Y-m-d'
        ]);

        // If validation fails, return an error response
        if ($vali->fails()) {
            return response()->json([
                "status" => 0,
                "message" => $vali->errors()
            ]);
        }

        // Find the book with the given ID
        $getBook = crudBook::where('id', $id)->first();

        // If the book is not found, return an error response
        if (!$getBook) {
            return response()->json([
                'status' => 0,
                'message' => 'The Book is not found'
            ]);
        }

        // Check if a timetable already exists for this user and book
        $existingTimeTable = timeTable::where('userId', $usersId)
            ->where('bookId', $id)
            ->first();

        if ($existingTimeTable) {
            return response()->json([
                'status' => 0,
                'message' => 'A timetable for this book already exists'
            ]);
        }

        // Determine the total reading time based on whether the book is an audiobook
        if ($getBook->bookType == 'audio') {
            // Path to the audio file in the public/audio directory
            $audioFilePath = public_path('audio/' . $getBook->audioFile);

            // Check if the file exists
            if (!file_exists($audioFilePath)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Audio file not found at path: ' . $audioFilePath
                ]);
            }

            // Initialize getID3 engine
            $getID3 = new getID3;

            // Analyze file and store returned data in $fileInfo
            $fileInfo = $getID3->analyze($audioFilePath);

            // Check for errors in file analysis
            if (isset($fileInfo['error'])) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Error analyzing audio file: ' . implode(', ', $fileInfo['error'])
                ]);
            }

            if (isset($fileInfo['playtime_seconds'])) {
                $totalMinutes = $fileInfo['playtime_seconds'] / 60; // Convert seconds to minutes
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Could not determine the duration of the audiobook'
                ]);
            }
        } else {
            if (empty($getBook->numOfPage)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'The number of pages is not provided'
                ]);
            }
            $totalMinutes = $getBook->numOfPage; // Total reading time for regular books
        }

        // Calculate the total reading days based on the time per minute provided by the user
        $totalDays = ceil($totalMinutes / $request->timePerMinut);

        // Get the start date from the request
        $startDate = Carbon::createFromFormat('Y-m-d', $request->startDate);

        // Calculate the end date based on the total reading days
        $endDate = $startDate->copy()->addDays($totalDays);

        // Create a new timetable
        $newTimeTable = timeTable::create([
            'userId' => $usersId,
            'timePerMinut' => $request->timePerMinut,
            'bookId' => $id,
            'bookSize' => $totalMinutes, // Store the appropriate size
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // Return a success response with the new timetable and total reading days
        return response()->json([
            'status' => 1,
            'message' => 'Time Table was created',
            'data' => $newTimeTable,
            'endDate' => $endDate
        ]);
    }
    public function updateTimeTable(Request $request, $id)
    {
        // Get the authenticated user ID
        $userId = auth()->user()->id;

        // Find the timetable with the given ID
        $getTimeTable = timeTable::where('id', $id)
            ->where('userId', $userId)->first();

        // If the timetable is not found, return an error response
        if (!$getTimeTable) {
            return response()->json([
                'status' => 0,
                'message' => 'The timetable is not found'
            ]);
        } else if ($getTimeTable->userId != $userId) {
            // If the timetable does not belong to the authenticated user, return an error response
            return response()->json([
                'status' => 0,
                'message' => 'You are not authorized to update this timetable'
            ]);
        } else {
            // Validate the request data
            $vali = Validator::make($request->all(), [
                'timePerMinut' => 'required|numeric|min:1',
                'startDate' => 'required|date|after_or_equal:today|date_format:Y-m-d'
            ]);

            // If validation fails, return an error response
            if ($vali->fails()) {
                return response()->json([
                    "status" => 0,
                    "message" => $vali->errors()
                ]);
            }

            // Find the book associated with the timetable
            $getBook = crudBook::where('id', $getTimeTable->bookId)->first();

            // Determine the total reading time based on whether the book is an audiobook
            if ($getBook->bookType == 'audio') {
                // Path to the audio file in the public/audio directory
                $audioFilePath = public_path('audio/' . $getBook->audioFile);

                // Check if the file exists
                if (!file_exists($audioFilePath)) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Audio file not found at path: ' . $audioFilePath
                    ]);
                }

                // Initialize getID3 engine
                $getID3 = new getID3;

                // Analyze file and store returned data in $fileInfo
                $fileInfo = $getID3->analyze($audioFilePath);

                // Check for errors in file analysis
                if (isset($fileInfo['error'])) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Error analyzing audio file: ' . implode(', ', $fileInfo['error'])
                    ]);
                }

                if (isset($fileInfo['playtime_seconds'])) {
                    $totalMinutes = $fileInfo['playtime_seconds'] / 60; // Convert seconds to minutes
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'Could not determine the duration of the audiobook'
                    ]);
                }
            } else {
                if (empty($getBook->numOfPage)) {
                    return response()->json([
                        'status' => 0,
                        'message' => 'The number of pages is not provided'
                    ]);
                }
                $totalMinutes = $getBook->numOfPage; // Total reading time for regular books
            }

            // Calculate the total reading days based on the time per minute provided by the user
            $totalDays = ceil($totalMinutes / $request->timePerMinut);

            // Get the start date from the request
            $startDate = Carbon::createFromFormat('Y-m-d', $request->startDate);

            // Calculate the end date based on the total reading days
            $endDate = $startDate->copy()->addDays($totalDays);

            // Update the timetable with the new data
            $getTimeTable->update([
                'timePerMinut' => $request->timePerMinut,
                'bookSize' => $totalMinutes,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            // Return a success response
            return response()->json([
                'status' => 1,
                'message' => 'Timetable was updated',
                'data' => $getTimeTable,
                'endDate' => $endDate
            ]);
        }
    } // public function createTableTime(Request $request, $id)
    // {
    //     // Get the authenticated user's ID
    //     $usersId  = auth()->user()->id;

    //     // Validate the request data
    //     $vali = Validator::make($request->all(), [
    //         'timePerMinut' => 'required|numeric|min:1',
    //         'startDate' => 'required|date|after_or_equal:today|date_format:Y-m-d'
    //     ]);

    //     // If validation fails, return an error response
    //     if ($vali->fails()) {
    //         return response()->json([
    //             "status : " => 0,
    //             "message : " => $vali->errors()
    //         ]);
    //     } else {
    //         // Find the book with the given ID
    //         $getBook = crudBook::where('id', $id)->first();

    //         // If the book is not found, return an error response
    //         if (!$getBook) {
    //             return response()->json([
    //                 'status ' => 0,
    //                 'message ' => 'The Book is not found'
    //             ]);
    //         } else {
    //             // Check if a timetable already exists for this user and book
    //             $existingTimeTable = timeTable::where('userId', $usersId)
    //                 ->where('bookId', $id)
    //                 ->first();
    //             if ($existingTimeTable) {
    //                 return response()->json([
    //                     'status ' => 0,
    //                     'message ' => 'A timetable for this book already exists'
    //                 ]);
    //             } else {
    //                 // Get the number of pages in the book
    //                 $SizeBook = $getBook->numOfPage;

    //                 // Calculate the total reading time in minutes
    //                 $totalMinutes = ($SizeBook);
    //                 // Calculate the total reading days based on the time per minute provided by the user
    //                 $totalDays = ceil($totalMinutes / $request->timePerMinut);
    //                 // Get the current date and time
    //                 $startDate = Carbon::createFromFormat('Y-m-d', $request->startDate);
    //                 // Calculate the end date based on the total reading days
    //                 $endDate = Carbon::now()->addDays($totalDays);

    //                 // Create a new timetable
    //                 $newTimeTable = timeTable::create([
    //                     'userId' => $usersId,
    //                     'timePerMinut' => $request->timePerMinut,
    //                     'bookId' => $id,
    //                     'bookSize' => $SizeBook,
    //                     'start_date' => $startDate,
    //                     'end_date' => $endDate,
    //                 ]);

    //                 // Return a success response with the new timetable and total reading days
    //                 return response()->json([
    //                     'status  ' => 1,
    //                     'message ' => 'Time Table was created',
    //                     'data ' => $newTimeTable,
    //                     'endDate' => $endDate
    //                 ]);
    //             }
    //         }
    //     }
    // }

    public function getTimeTable($id)
    {

        $userId = auth()->user()->id;

        $getTimeTable = timeTable::where('id', $id)
            ->where('userId', $userId)->first();

        if ($getTimeTable->isEmpty) {
            return response()->json([
                'status' => 0,
                'message' => 'You Cant get this TimeTable'
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'message ' => 'Your Time Table Detalis!',
                'data' => $getTimeTable
            ]);
        }
    }
    public function getAllTable()
    {

        $userId = auth()->user()->id;

        $findAll = timeTable::where('userId', $userId)->get();
        if ($findAll->isEmpty()) {
            return response()->json([
                'status' => 0,
                'message ' => 'You dont have any Time Table'
            ]);
        } else {
            return response()->json([
                'status' => 1,
                'message' => 'Your TimeTables :',
                'data : ' => $findAll
            ]);
        }
    }
}
 // public function createTableTime(Request $request, $id)
    // {
    //     // Get the authenticated user's ID
    //     $usersId  = auth()->user()->id;

    //     // Validate the request data
    //     $vali = Validator::make($request->all(), [
    //         'timePerMinut' => 'required|numeric|min:1'
    //     ]);

    //     // If validation fails, return an error response
    //     if ($vali->fails()) {
    //         return response()->json([
    //             "status : " => 0,
    //             "message : " => $vali->errors()
    //         ]);
    //     } else {
    //         // Find the book with the given ID
    //         $getBook = crudBook::where('id', $id)->first();

    //         // If the book is not found, return an error response
    //         if (!$getBook) {
    //             return response()->json([
    //                 'status ' => 0,
    //                 'message ' => 'The Book is not found'
    //             ]);
    //         } else {
    //             // Check if a timetable already exists for this user and book
    //             $existingTimeTable = timeTable::where('userId', $usersId)
    //                 ->where('bookId', $id)
    //                 ->first();
    //             if ($existingTimeTable) {
    //                 return response()->json([
    //                     'status ' => 0,
    //                     'message ' => 'A timetable for this book already exists'
    //                 ]);
    //             } else {
    //                 // Get the number of pages in the book
    //                 $SizeBook = $getBook->numOfPage;

    //                 // Calculate the total reading time in minutes
    //                 $totalMinutes = ($SizeBook);
    //                 // Calculate the total reading days based on the time per minute provided by the user
    //                 $totalDays = ceil($totalMinutes / $request->timePerMinut);
    //                 // Get the current date and time
    //                 $startDate = Carbon::now();
    //                 // Calculate the end date based on the total reading days
    //                 $endDate = Carbon::now()->addDays($totalDays);

    //                 // Create a new timetable
    //                 $newTimeTable = timeTable::create([
    //                     'userId' => $usersId,
    //                     'timePerMinut' => $request->timePerMinut,
    //                     'bookId' => $id,
    //                     'bookSize' => $SizeBook,
    //                     'start_date' => $startDate,
    //                     'end_date' => $endDate,
    //                 ]);

    //                 // Return a success response with the new timetable and total reading days
    //                 return response()->json([
    //                     'status  ' => 1,
    //                     'message ' => 'Time Table was created',
    //                     'data ' => $newTimeTable,
    //                     'endDate' => $endDate
    //                 ]);
    //             }
    //         }
    //     }
    // }
