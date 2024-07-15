<?php

use Illuminate\Http\Request;

use App\Http\Controllers\Search;
use App\Http\Controllers\Register;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MyBookController;
use App\Http\Controllers\MyNotesController;
use App\Http\Controllers\CrudBookController;
use App\Http\Controllers\EvalBookController;
use App\Http\Controllers\NoteBookController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\BookShelfController;
use App\Http\Controllers\TimeTableController;
use App\Http\Controllers\FavoriteBookController;
use App\Models\crudBook;
use App\Models\noteBook;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [Register::class, 'register']);
Route::post('register/admin', [Register::class, 'registerAdmin']);
Route::post('login', [Register::class, 'Login']);

Route::group(["middleware" => ["auth:sanctum"]], function () {
    //register
    Route::get('logout', [Register::class, 'Logout']);
    Route::get('profile', [Register::class, 'profile']);
    Route::get('deletUser/{id}', [Register::class, 'deletUser'])->middleware('is_admin');
    Route::post('editProfile', [Register::class, 'editProfile']);

    //crud_Book
    Route::post('addBook', [CrudBookController::class, 'storeBook'])->middleware('is_admin');
    Route::delete('deletebook/{id}', [CrudBookController::class, 'deleteBook'])->middleware('is_admin');
    Route::post('editBook/{id}', [CrudBookController::class, 'updateBook'])->middleware('is_admin');
    Route::get('getBooks', [CrudBookController::class, 'getAll']);
    Route::get('/images/{imageName}', [CrudBookController::class, 'showImage']);
    Route::get('fileBook/{id}', [CrudBookController::class, 'showBookFile']);
    Route::get('showBookFile/{id}', [CrudBookController::class, 'showBookFile']);
    Route::get('getBook/{id}', [CrudBookController::class, 'getBook']);
    Route::get('users', [Register::class, 'showUsers'])->middleware('is_admin');

    //Search
    Route::post('search', [Search::class, 'search']);
    Route::get('/audio-books', [Search::class, 'getAllAudioBooks']);

    //Favorite
    Route::get('favBook/{id}', [FavoriteBookController::class, 'favoriteBook']);
    Route::get('getFav', [FavoriteBookController::class, 'getFavBook']);
    Route::delete('deleteFav/{id}', [FavoriteBookController::class, 'deleteBookFav']);

    //NotesBook
    Route::post('addNote/{id}', [NoteBookController::class, 'addNote']);
    Route::put('editnote/{id}', [NoteBookController::class, 'editNote']);
    Route::get('getNoteBook/{id}', [NoteBookController::class, 'getNoteBook'])->middleware('is_admin');
    Route::delete('deletNoteBook/{id}', [NoteBookController::class, 'deletNoteBook']);
    Route::get('noteBook/{id}', [NoteBookController::class, 'noteForBook'])->middleware('is_admin');

    //Eval
    Route::post('evalBook/{id}', [EvalBookController::class, 'addEval']);
    Route::get('mostEval/{id}', [EvalBookController::class, 'getMostEval'])->middleware('is_admin');
    Route::delete('deletEval/{id}', [EvalBookController::class, 'deletEval']);
    Route::get('mostRate', [EvalBookController::class, 'getMostRatedBook'])->middleware('is_admin');
    Route::post('editEval/{id}', [EvalBookController::class, 'editEval']);

    //Question
    Route::post('question/{id}', [QuestionController::class, 'addQuestion']);
    Route::post('editQues/{id}', [QuestionController::class, 'editQusetion']);
    Route::get('showQues/{id}', [QuestionController::class, 'getQuestion'])->middleware('is_admin');
    Route::get('allQues', [QuestionController::class, 'getAllQues'])->middleware('is_admin');
    Route::get('quesBook/{id}', [QuestionController::class, 'getQuesBook'])->middleware('is_admin');

    //TimeTable
    Route::post('timeTable/{id}', [TimeTableController::class, 'createTableTime']);
    Route::post('updateTimeTable/{id}', [TimeTableController::class, 'updateTimeTable']);
    Route::get('getTimeTable/{id}', [TimeTableController::class, 'getTimeTable']);
    Route::get('allTimeTable', [TimeTableController::class, 'getAllTable']);


    // My Notes
    Route::post('newNote', [MyNotesController::class, 'newNote']);
    Route::post('editMyNote/{id}', [MyNotesController::class, 'editNote']);

    //MyBook
    Route::get('addMyBook/{bookId}', [MyBookController::class, 'addToMyBook']);
    Route::get('myBook/{bookId}/{pageRead}', [MyBookController::class, 'updateBookReadStatus']);
    Route::get('mostBook', [MyBookController::class, 'getMostCommonBook'])->middleware('is_admin');
    Route::get('statusBook/{bookId}', [MyBookController::class, 'getStatusBook']);
    Route::get('getMyBook', [MyBookController::class, 'getMyBook']);
    Route::get('pageRead', [MyBookController::class, 'getTotalPagesRead']);
    Route::get('getAsRead', [MyBookController::class, 'getAsRead']);
    Route::get('notRead', [MyBookController::class, 'getNotRead']);
    Route::get('reading', [MyBookController::class, 'getReading']);

    //Book Shelf
    Route::post('newShelf', [BookShelfController::class, 'newShelf']);
    Route::get('addBookToShelf/{shelfId}/{bookId}', [BookShelfController::class, 'addBookToShelf']);
    Route::get('userShelf', [BookShelfController::class, 'getUserShelves']);
    Route::get('bookInShelf/{id}', [BookShelfController::class, 'getBooksInShelf']);
    Route::post('updateShelf/{id}', [BookShelfController::class, 'updateShelf']);
    Route::get('deletShelf/{shelfId}', [BookShelfController::class, 'deleteShelf']);
    Route::get('deletbookInShelf/{shelfId}/{bookId}', [BookShelfController::class, 'deletBookFromShelf']);
});
