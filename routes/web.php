<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceAttachmentsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
    return redirect()->route('login');
});

require __DIR__.'/auth.php';
Route::get('/dashboard',[DashboardController::class,'dashboard'])->middleware(['auth'])->name('dashboard');
Route::get('/mark_as_read',[DashboardController::class,'markAsRead'])->middleware(['auth'])->name('mark_as_read');

Route::resource('sections', SectionController::class)->middleware(['auth']);
Route::resource('products', ProductController::class)->middleware(['auth']);

Route::resource('invoices', InvoiceController::class)->middleware(['auth']);
Route::put('invoices_restore', [InvoiceController::class,'restore'])->middleware(['auth'])->name('invoice_restore');
Route::get('{invoice}/status_show', [InvoiceController::class,'statusShow'])->middleware(['auth'])->name('status_show');
Route::put('status_update', [InvoiceController::class,'statusUpdate'])->middleware(['auth'])->name('status_update');
Route::delete('invoices_forceDelete', [InvoiceController::class,'forceDelete'])->middleware(['auth'])->name('invoice_force_Delete');
Route::get('invoices_archive', [InvoiceController::class,'archive'])->middleware(['auth'])->name('invoice_archive');
Route::get('paid_invoices', [InvoiceController::class,'paid'])->middleware(['auth'])->name('paid_invoices');
Route::get('unpaid_invoices', [InvoiceController::class,'unpaid'])->middleware(['auth'])->name('unpaid_invoices');
Route::get('part_invoices', [InvoiceController::class,'part'])->middleware(['auth'])->name('part_invoices');
Route::get('print_invoices/{id}', [InvoiceController::class,'print'])->middleware(['auth'])->name('print_invoice');
Route::get('productsofsection', [InvoiceController::class,'productsOfSection'])->middleware(['auth'])->name('product.section');
Route::get('export', [InvoiceController::class,'export'])->middleware(['auth'])->name('export_invoice');
Route::get('invoices_report',[InvoiceController::class,'showReport'])->name('invoices_report');
Route::post('search_invoices',[InvoiceController::class,'search'])->name('search_invoices');

Route::post('add_attachment', [InvoiceAttachmentsController::class,'add'])->middleware(['auth'])->name('add-attachment');
Route::delete('delete_attachment', [InvoiceAttachmentsController::class,'delete'])->middleware(['auth'])->name('delete-attachment');
Route::get('download_attachment/{id}', [InvoiceAttachmentsController::class,'download'])->middleware(['auth'])->name('download-attachment');
Route::get('show_attachment/{id}', [InvoiceAttachmentsController::class,'show'])->middleware(['auth'])->name('show-attachment');

Route::resource('roles', RolesController::class);
Route::resource('permissions', PermissionsController::class);
Route::resource('users', UsersController::class);
Route::get('customers_report',[UsersController::class,'showReport'])->name('customers_report');
Route::post('search_customers',[UsersController::class,'search'])->name('search_customers');

Route::get('/{page}', [AdminController::class,'index'])->middleware(['auth']);
