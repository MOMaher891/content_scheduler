<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\Posts\PostCreate;
use App\Livewire\Posts\PostList;
use App\Livewire\UserPlatforms;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/dashboard', function () {
    return view('livewire.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User Platforms
    Route::get('/platforms', UserPlatforms::class)->name('user.platforms');
});

Route::middleware('auth')->prefix('posts')->group(function () {
    Route::get('/', PostList::class)->name('posts.index');
    Route::get('/create', PostCreate::class)->name('posts.create');
});

require __DIR__ . '/auth.php';
