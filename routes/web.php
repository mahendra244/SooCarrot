<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [UserController::class,'loadDashboard'])->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::post('/save-chat',[UserController::class,'saveChat']);
Route::post('/load-chats',[UserController::class,'loadChats']);
Route::post('/delete-chat',[UserController::class,'deleteChat']);
Route::post('/update-chat',[UserController::class,'updateChat']);

//groups route
Route::get('/groups', [UserController::class,'loadGroups'])->middleware(['auth'])->name('groups');
Route::post('/create-group', [UserController::class,'createGroup'])->middleware(['auth'])->name('createGroup');
Route::post('/get-members', [UserController::class,'getMembers'])->middleware(['auth'])->name('getMembers');
Route::post('/add-member', [UserController::class,'addMembers'])->middleware(['auth'])->name('addMembers');
Route::post('/delete-group', [UserController::class,'deleteGroup'])->middleware(['auth'])->name('deleteGroup');
Route::post('/update-group', [UserController::class,'updateGroup'])->middleware(['auth'])->name('updateGroup');
Route::get('/share-group/{id}', [UserController::class,'shareGroup'])->middleware(['auth'])->name('shareGroup');
Route::post('/join-group', [UserController::class,'joinGroup'])->middleware(['auth'])->name('joinGroup');
Route::get('/group-chats', [UserController::class,'groupChats'])->middleware(['auth'])->name('groupChats');


Route::post('/save-group-chat',[UserController::class,'saveGroupChat']);
Route::post('/load-group-chats',[UserController::class,'loadGroupChats']);
Route::post('/delete-group-chat',[UserController::class,'deleteGroupChat']);
Route::post('/update-group-chat',[UserController::class,'updateGroupChat']);
