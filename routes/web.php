<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;

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
Route::get('/news', [\App\Http\Livewire\Newspaper::class, 'render'])->name('news');

Route::get('/auth/redirect', [GoogleAuthController::class, 'redirectToProvider'])->name('google_auth');
Route::get('/auth/callback', [GoogleAuthController::class, 'handleProviderCallback']);

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('profile', [UserController::class, 'showAdminPanel'])->name('admin.profile');
});

Route::post('video/upload', [VideoController::class, 'upload'])->name('upload.video');
Route::get('video/encode', [VideoController::class, 'encodeVideo']);

Route::get('/comments/info', [CommentController::class, 'checkAddComment']);
Route::post('/like/post', [LikeController::class, 'likePost']);
Route::post('/like/comment', [LikeController::class, 'likeComment']);

Route::get('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::resource('/posts', PostController::class);
Route::resource('/comments', CommentController::class);
Route::resource('/images', ImageController::class);


Route::get('/profile', [UserController::class, 'show'])->middleware(['auth'])->name('profile');
Route::post('change-password', [UserController::class, 'changePassword'])->name('change.password');
Route::post('change-name', [UserController::class, 'changeUserNAme'])->name('change.user.name');
Route::get('/user/change/status', [UserController::class, 'changeStatus'])->name('change-status');
Route::get('/search', [SearchController::class, 'index'])->name('search.index');

Route::get('/', function () {
    return redirect('/posts');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

require __DIR__ . '/auth.php';
