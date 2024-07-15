<?php

namespace App\Http\Controllers;

use App\Models\crudBook;
use App\Models\noteBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NoteBookController extends Controller
{
    public function addNote(Request $request, $id)
    {

        $findBook = crudBook::find($id);

        if (!$findBook) {
            return response()->json([
                'status ' => 0,
                'message ' => 'Book not Found '
            ]);
        } else {
            $userID = auth()->user()->id;
            $reg = Validator::make($request->all(), [
                'note' => 'required',
            ]);

            if ($reg->fails()) {
                return response()->json([
                    "status : " => 0,
                    "message : " => $reg->errors()
                ]);
            } else {
                $newNote = noteBook::create([
                    'userId' => $userID,
                    'bookId' => $findBook->id,
                    'note' => $request->note,
                ]);
            }
            return response()->json([
                'status ' => 1,
                'data ' => $newNote
            ]);
        }
    }
    public function editNote(Request $request, $id)
    {

        $vali = validator::make($request->all(), [
            'note' => 'required'
        ]);
        if ($vali->fails()) {
            return response()->json([

                'status ' => 0,
                'message' => 'blease enter the data it want edit'
            ]);
        } else {


            $userId = auth()->user()->id;

            $findNote = noteBook::where('userId', $userId)
                ->where('bookId', $id)->first();

            if ($findNote) {

                $findNote->note = !empty($request->note) ? $request->note : $findNote->note;

                $findNote->save();

                return response()->json([
                    'status ' => 1,
                    'message ' => 'Edit for your Note is successfully'
                ]);
            } else {
                return response()->json([
                    'status ' => 0,
                    'message ' => 'the Note is not found'
                ]);
            }
        }
    }
    public function getNoteBook($id)
    {
        $getAllNotes = noteBook::where('bookId', $id)->get();

        if ($getAllNotes->isEmpty()) {
            return response()->json([[
                'status' => 0,
                'message' => 'There are no notes for this book.'
            ]]);
        } else {
            return response()->json([
                'status' => 1,
                'message' => 'All notes for the specified book!',
                'data' => $getAllNotes
            ]);
        }
    }
    public function deletNoteBook($id)
    {

        $userId = auth()->user()->id;

        $getNoteBook = NoteBook::where('userId', $userId)
            ->where('bookId', $id)->first();

        if (!$getNoteBook) {
            return response()->json([
                'status' => 0,
                'message' => 'You dont have any Note for delet'
            ], 404);
        } else {
            $getNoteBook->delete();
            return response()->json([
                'status' => 1,
                'message' => 'delete Note is Successfully'
            ], 200);
        }
    }
    public function noteForBook($id)
    {

        $getNoteBook = noteBook::where('bookId', $id)->get();
        if ($getNoteBook->isEmpty()) {
            return response()->json([
                'status' => 0,
                'message' => 'this Book does not have any Note'
            ]);
        } else {

            return response()->json([
                'status' => 1,
                'message' => ' Note for this Book!',
                'data' => $getNoteBook
            ]);
        }
    }
}
