<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// HOME
Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('home');

Route::post('/slot/register/{slotOccurence}', [UserController::class, 'register'])->name('slot.register');
Route::delete('/slot/unregister/{slotOccurence}', [UserController::class, 'unregister'])->name('slot.unregister');
Route::post('/slot/register/monitor/{slotOccurence}', [UserController::class, 'registerAsMonitor'])->name('slot.register.monitor');
Route::delete('/slot/unregister/monitor/{slotOccurence}', [UserController::class, 'unregisterAsMonitor'])->name('slot.unregister.monitor');

Route::get('/dashboard', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// USER PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// USER INVITATION LINK
Route::get('/register/{token}', [App\Http\Controllers\UserController::class, 'registerFromMail'])->name('user.registerFromMail');
Route::post('/register/{token}', [App\Http\Controllers\UserController::class, 'registerFromMail'])->name('user.registerFromMail');

// SUPER ADMIN
Route::get('/super-admin', [App\Http\Controllers\SuperAdminController::class, 'index'])->name('super-admin.index');
Route::get('/club/create', [App\Http\Controllers\ClubController::class, 'create'])->name('club.create');
Route::post('/club/create', [App\Http\Controllers\ClubController::class, 'create']);

// ADMIN CLUB
Route::get('/admin-club', [App\Http\Controllers\AdminClubController::class, 'index'])->name('admin-club.index');
Route::get('/admin-club/more-occurences', [App\Http\Controllers\AdminClubController::class, 'loadMoreOccurrences'])->name('admin.club.loadMoreOccurrences');

// --- ADMIN CLUB : SLOTS 
Route::get('/admin-club/slots', [App\Http\Controllers\AdminClubController::class, 'slots'])->name('admin-club.slots');
Route::get('/admin-club/slots/new', [App\Http\Controllers\SlotController::class, 'new'])->name('admin-club.slots.new');
Route::get('/admin-club/slots/delete/{id}', [App\Http\Controllers\SlotController::class, 'delete'])->name('admin-club.slots.delete');
Route::get('/admin-club/slots/edit/{id}', [App\Http\Controllers\SlotController::class, 'edit'])->name('admin-club.slots.edit');
Route::post('/admin-club/slots/edit/{id}', [App\Http\Controllers\SlotController::class, 'edit'])->name('admin-club.slots.edit');
Route::post('/admin-club/slots/new', [App\Http\Controllers\SlotController::class, 'new'])->name('admin-club.slots.new');
// --- ADMIN CLUB : MEMBERS 
Route::get('/admin-club/members', [App\Http\Controllers\AdminClubController::class, 'members'])->name('admin-club.members');
Route::get('/admin-club/members/invite', [App\Http\Controllers\AdminClubController::class, 'invite'])->name('admin-club.members.invite');
Route::post('/admin-club/members/invite', [App\Http\Controllers\AdminClubController::class, 'invite'])->name('admin-club.members.invite');

// DASHBOARD USER
Route::get('/clubs', [App\Http\Controllers\ClubController::class, 'index'])->name('club.index');

require __DIR__.'/auth.php';
