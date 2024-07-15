<?php

namespace App\Http\Controllers;

use App\Models\myNotes;
use Illuminate\Http\Request;

class MyNotesController extends Controller
{
    public function newNote(Request $request)
    {
        $userId = auth()->user()->id;

        // validate for information
        $request->validate([
            'note' => 'required|string',
        ]);
        // add new note for user
        $note = myNotes::create([
            'note' => $request->note,
            'userId' => $userId
        ]);
        return response()->json([
            'data' => $note,
        ], 201);
    }
    public function editNote(Request $request, $id)
    {
        $userId  = auth()->user()->id;

        //find the note you want edit
        $findNote = myNotes::where('userId', $userId)
            ->where('id', $id)->first();

        if ($findNote) {
            // edit note
            $findNote->note = !empty($request->note) ? $request->note : $findNote->note;
            //save edit
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
    public function getMyNote()
    {
        $userId = auth()->user()->id;

        $getNote = myNotes::where('userId', $userId)->get();
        if (!$getNote) {
            return response()->json([
                'status ' => 0,
                'message ' => 'You dont have any Note'
            ]);
        } else {
            return response()->json([
                'status ' => 1,
                'message' => 'Your note !',
                'data ' => $getNote
            ]);
        }
    }
}
