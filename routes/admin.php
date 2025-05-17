<?php

use Illuminate\Support\Facades\Route;

use App\Admin\Controllers\ProductJaController;

Route::post('product/duplicate', [ProductJaController::class, 'duplicate']);