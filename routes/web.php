<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SlotController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CalendarController;
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
Route::get('/admin-club/occurrence-history/{id}', [App\Http\Controllers\AdminClubController::class, 'occurrenceHistory'])->name('admin.club.occurrenceHistory');
Route::get('/admin-club/cancel-slot/{id}', [App\Http\Controllers\SlotOccurenceCancellationController::class, 'create'])->name('admin.club.cancelSlotOccurenceForm');
Route::post('/admin-club/cancel-slot/{id}', [App\Http\Controllers\SlotOccurenceCancellationController::class, 'store'])->name('admin.club.cancelSlotOccurence');
Route::post('/admin-club/occurence/{slotOccurence}/cancel', [App\Http\Controllers\SlotOccurenceCancellationController::class, 'store'])->name('admin.club.occurence.cancel');
// Affichage de la modal des participants pour une occurrence donnée
Route::get('/admin-club/slots/{slotOccurrence}/participants', [App\Http\Controllers\AdminClubController::class, 'participants'])->name('admin.club.slots.participants');
// Export CSV de la liste des participants pour une occurrence donnée
Route::get('/admin-club/slots/{slotOccurrence}/participants/export', [App\Http\Controllers\AdminClubController::class, 'exportParticipants'])->name('admin.club.slots.participants.export');
// Afficher la modale de whitelist pour un slot donné
Route::get('/admin-club/slots/{slot}/whitelist', [App\Http\Controllers\AdminClubController::class, 'whitelist'])->name('admin.club.slots.whitelist');
// Ajouter un membre à la whitelist pour un slot donné
Route::get('/admin-club/slots/{slot}/whitelist/add/{user}', [App\Http\Controllers\AdminClubController::class, 'addToWhitelist'])->name('admin.club.slots.whitelist.add');
// Export CSV de la whitelist
Route::get('/admin-club/slots/{slot}/whitelist/export', [App\Http\Controllers\AdminClubController::class, 'exportWhitelist'])->name('admin.club.slots.whitelist.export');
Route::get('/admin-club/slots/{slot}/whitelist/remove/{user}', [App\Http\Controllers\AdminClubController::class, 'removeFromWhitelist'])->name('admin.club.slots.whitelist.remove');


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
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/event/{id}', [CalendarController::class, 'eventDetails'])->name('calendar.eventDetails');
Route::get('/calendar/slot/{id}', [CalendarController::class, 'getSlotOccurenceDetail'])->name('calendar.getSlotOccurenceDetail');
Route::post('/user/calendar_view_toggle', [UserController::class, 'toggleCalendarView'])->name('user.calendar_view.toggle');
Route::post('/user/toggle-view', [UserController::class, 'toggleView'])->name('user.toggle_view');

require __DIR__.'/auth.php';
