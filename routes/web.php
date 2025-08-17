<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Product;

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

Route::redirect('/', '/products');

Route::middleware(['auth'])->group(function () {
    // Index
    Route::get('/products', function () {
        return view('products.index');
    })->name('products.index');

    // Create
    Route::get('/products/create', function () {
        return view('products.create');
    })->name('products.create');

    // Edit
    Route::get('/products/{product}/edit', function (Product $product) {
        return view('products.edit', compact('product'));
    })->name('products.edit')
      ->middleware('can:update,product');

    // Trash
    Route::get('/products-trash', function () {
        return view('products.trash');
    })->name('products.trash');

    Route::get('/products/{product}', function (Product $product) {
        return view('products.show', compact('product'));
    })->name('products.show');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
