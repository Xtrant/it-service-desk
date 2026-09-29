<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketInspectionController;
use App\Http\Controllers\TicketActionController;
use App\Http\Controllers\TicketApprovalController;
use App\Http\Controllers\TicketLifecycleController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\TicketSignatureController;
use App\Http\Controllers\ITDashboardController;
use App\Http\Controllers\DashboardController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->name('login.store');
});

Route::middleware('auth')->group(function () {

    Route::middleware('role:employee')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
    });

    Route::middleware('role:it_staff,admin')->group(function () {
        Route::get('/it/dashboard', [ITDashboardController::class, 'index'])
            ->name('it.dashboard');
    });

    Route::get('/tickets', [TicketController::class, 'index'])
        ->name('tickets.index');

    Route::get('/tickets/create', [TicketController::class, 'create'])
        ->name('tickets.create');

    Route::post('/tickets', [TicketController::class, 'store'])
        ->name('tickets.store');

    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
        ->name('tickets.show');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    Route::post(
        '/tickets/{ticket}/take',
        [TicketController::class, 'take']
    )->name('tickets.take');

    Route::post(
        '/tickets/{ticket}/start',
        [TicketController::class, 'start']
    )->name('tickets.start');

    Route::post(
        '/tickets/{ticket}/comments',
        [TicketCommentController::class, 'store']
    )->name('tickets.comments.store');

    Route::post(
        '/tickets/{ticket}/inspections',
        [TicketInspectionController::class, 'store']
    )->name('tickets.inspections.store');

    Route::post(
        '/tickets/{ticket}/actions',
        [TicketActionController::class, 'store']
    )->name('tickets.actions.store');

    Route::post(
        '/tickets/{ticket}/approvals',
        [TicketApprovalController::class, 'store']
    )->name('tickets.approvals.store');

    Route::post(
        '/tickets/{ticket}/approvals/{approval}/approve',
        [TicketApprovalController::class, 'approve']
    )->name('tickets.approvals.approve');

    Route::post(
        '/tickets/{ticket}/approvals/{approval}/reject',
        [TicketApprovalController::class, 'reject']
    )->name('tickets.approvals.reject');

    Route::post(
        '/tickets/{ticket}/resolve',
        [TicketLifecycleController::class, 'resolve']
    )->name('tickets.resolve');

    Route::post(
        '/tickets/{ticket}/close',
        [TicketLifecycleController::class, 'close']
    )->name('tickets.close');

    Route::post(
        '/tickets/{ticket}/reopen',
        [TicketLifecycleController::class, 'reopen']
    )->name('tickets.reopen');

    Route::post(
        '/tickets/{ticket}/attachments',
        [TicketAttachmentController::class, 'store']
    )->name('tickets.attachments.store');

    Route::delete(
        '/tickets/{ticket}/attachments/{attachment}',
        [TicketAttachmentController::class, 'destroy']
    )->name('tickets.attachments.destroy');

    Route::get(
        '/tickets/{ticket}/attachments/{attachment}',
        [TicketAttachmentController::class, 'show']
    )->name('tickets.attachments.show');

    Route::get(
        '/tickets/{ticket}/requester-signature',
        [TicketSignatureController::class, 'requester']
    )->name('tickets.signatures.requester');

    Route::get(
        '/tickets/{ticket}/inspections/{inspection}/signature',
        [TicketSignatureController::class, 'inspection']
    )->name('tickets.signatures.inspection');

    Route::get(
        '/tickets/{ticket}/approvals/{approval}/signature',
        [TicketSignatureController::class, 'approval']
    )->name('tickets.signatures.approval');
});
