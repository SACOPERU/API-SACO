<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DespatchController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

    Route::post('saco/register', [RegisterController::class, 'store']);

    Route::post('saco/login', [AuthController::class, 'login']);
    Route::post('saco/logout', [AuthController::class, 'logout']);
    Route::post('saco/refresh', [AuthController::class, 'refresh']);
    Route::post('saco/me', [AuthController::class, 'me']);

    Route::apiResource('saco/companies', CompanyController::class)->middleware('auth:api');

    Route::post('saco/invoices/send', [InvoiceController::class, 'send'])->middleware('auth:api');
    Route::post('saco/invoices/xml', [InvoiceController::class, 'xml'])->middleware('auth:api');
    Route::post('saco/invoices/pdf', [InvoiceController::class, 'pdf'])->middleware('auth:api');

    Route::post('saco/notes/send', [NoteController::class, 'send'])->middleware('auth:api');
    Route::post('saco/notes/xml', [NoteController::class, 'xml'])->middleware('auth:api');
    Route::post('saco/notes/pdf', [NoteController::class, 'pdf'])->middleware('auth:api');

    Route::post('saco/despatches/send', [DespatchController::class, 'send'])->middleware('auth:api');
    Route::post('saco/despatches/xml', [DespatchController::class, 'xml'])->middleware('auth:api');
    Route::post('saco/despatches/pdf', [DespatchController::class, 'pdf'])->middleware('auth:api');

