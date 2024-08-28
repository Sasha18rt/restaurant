<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Order;
use Laravel\Cashier\Cashier;
use App\Http\Controllers\MainController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\StripeController;
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

Route::get('/',  [MainController::class, 'home'])->name('home');

Route::get('/review', [MainController::class, 'review'])->name('review');

Route::get('/nreview', [MainController::class, 'navigation'])->name('nreview');

Route::post('/review/check', [MainController::class, 'review_check']);

Route::get('/dashboard', [MainController::class, 'home'])->name('dashboard');

Route::post('/reservation', [MainController::class, 'reservation'])->name('reservation');


Route::get('/api', [MainController::class, 'api'])->name('api');


Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => function ($request, $next) {
        if (auth()->user()->usertype != 1) {
            return redirect('/')->with('error', 'You do not have access to this page.');
        }
        return $next($request);
    }], function () {
        Route::get('/deleteuser/{id}',  [AdminController::class, 'delete_user'])->name('delete_user');
        Route::get('/waiter/{id}',  [AdminController::class, 'waiter'])->name('waiter');
        Route::get('/search-users', [AdminController::class, 'searchUsers'])->name('search.users');
        Route::get('/users', [AdminController::class, 'ausers'])->name('admin_users');

        Route::get('/menu',  [AdminController::class, 'amenu'])->name('admin_menu');

        Route::get('/reviews', [AdminController::class, 'areviews'])->name('admmin_reviews');
        Route::delete('/delete_review/{id}', [AdminController::class, 'delete_review'])->name('delete_review');
        Route::get('/search_review', [AdminController::class, 'search_review'])->name('search_review');

        Route::get('/table', [AdminController::class, 'table'])->name('admin_table');

        Route::get('/areservation', [AdminController::class, 'areservation'])->name('admin_reservation');
        Route::delete('/delete_reservation/{id}',  [AdminController::class, 'delete_reservation'])->name('delete_reservation');

        Route::get('/delete-dish/{id}', [AdminController::class, 'deleteDish'])->name('delete_dish');

        Route::get('/delete_menu_item/{id}', [AdminController::class, 'delete_menu_item'])->name('delete_menu_item');

        Route::post('/submit_dish', [AdminController::class, 'submitDish'])->name('submit_dish');

        Route::post('/add-new-dish-type', [AdminController::class, 'addNewDishType'])->name('add_new_dish_type');

        Route::post('/delete-dish-type', [AdminController::class, 'deleteDishType'])->name('delete_dish_type');

        Route::post('/manage-dish-type', [AdminController::class, 'manageDishType'])->name('manage_dish_type');
        Route::post('/reorder-dish-types', [AdminController::class, 'reorderDishTypes'])->name('reorder_dish_types');

        Route::get('/edit_addons/{id}', [AdminController::class, 'editAddOns'])->name('edit_addons');
        Route::post('/edit_addons/{id}', [AdminController::class, 'updateAddOns'])->name('update_addons');
        Route::post('/addons', [AdminController::class, 'store'])->name('addons.store');
        Route::delete('/addons/{id}', [AdminController::class, 'destroy'])->name('addons.destroy');

        Route::get('/edit-menu-item/{id}', [AdminController::class, 'update_menu_item'])->name('edit_menu_item');
        Route::put('/update-dish/{id}', [AdminController::class, 'updateDish'])->name('submit_update_dish');

        Route::post('/update/{id}', [AdminController::class, 'update'])->name('update');


        
Route::get('/orders', [TableController::class, 'orders'])->name('orders.index');
    });
});



Route::get('/tables', [TableController::class, 'index'])->name('tables.index');
Route::get('/tables/{id}', [TableController::class, 'show'])->name('table.show');
Route::get('/tables/{id}/qrcode', [TableController::class, 'generateQRCode'])->name('table.qrcode');
Route::post('/tables/{id}/order', [TableController::class, 'order'])->name('table.order');
Route::post('/tables/{id}/pay', [TableController::class, 'pay'])->name('table.pay');
Route::post('/order/{id}/pay', [TableController::class, 'payItem'])->name('order.pay');
Route::post('/tables/create', [TableController::class, 'store'])->name('table.store');
Route::get('/tables/{id}/qrcode/download', [TableController::class, 'downloadQRCode'])->name('table.qrcode.download');
Route::delete('/tables/delete-latest', [TableController::class, 'destroyLatest'])->name('table.destroy.latest');

Route::middleware(['auth'])->group(function () {
    Route::group(['middleware' => function ($request, $next) {
        if (auth()->user()->usertype != 2) {
            return redirect('/')->with('error', 'You do not have access to this page.');
        }
        return $next($request);
    }], function () {
    Route::get('/select-table', [TableController::class, 'selectTable'])->name('select-table');
    
    Route::get('/order/create', [TableController::class, 'create'])->name('order.create');
    Route::get('/order/select-dish-type', [TableController::class, 'selectDishType'])->name('order.selectDishType');
    Route::get('/order/select-dish', [TableController::class, 'selectDish'])->name('order.selectDish');
    Route::get('/order/add-details', [TableController::class, 'addDetails'])->name('order.addDetails');
    Route::post('/order/add-item', [TableController::class, 'addItem'])->name('order.addItem');
    Route::post('/order/complete', [TableController::class, 'completeOrder'])->name('order.complete');
    Route::delete('/order/remove-item/{id}', [TableController::class, 'removeItem'])->name('order.removeItem');
Route::put('/order/update-quantity/{id}', [TableController::class, 'updateQuantity'])->name('order.updateQuantity');
Route::get('/order/edit/{id}', [TableController::class, 'editOrder'])->name('order.editOrder');
Route::post('/order/update/{id}', [TableController::class, 'updateOrder'])->name('order.updateOrder');


});});
Route::get('/orders/latest', [TableController::class, 'getLatestOrders'])->name('orders.latest');
Route::post('/orders/{id}/complete', [TableController::class, 'complete'])->name('orders.complete');
Route::get('/get-addons/{dish}', [TableController::class, 'getAddons']);
Route::get('/table/{id}/menu', [TableController::class, 'showMenu'])->name('table.menu');


Route::get('/table/{id}/split', [TableController::class, 'showSplitPayment'])->name('table.split');
Route::post('/table/{id}/splitPay', [TableController::class, 'splitPay'])->name('table.splitPay');

Route::post('/table/{id}/payAll', [StripeController::class, 'payAll'])->name('table.payAll');
Route::post('stripe', [StripeController::class, 'stripe'])->name('stripe');
Route::get('/success', [StripeController::class, 'success'])->name('stripe.success');
Route::get('/cancel', [StripeController::class, 'cancel'])->name('stripe.cancel');
Route::get('/table/order', [TableController::class, 'showCheckout'])->name('table.checkout');

Route::post('/stripe/single-item', [StripeController::class, 'paySingleItem'])->name('stripe.singleItem');
Route::get('/stripe/success/single-item', [StripeController::class, 'successSingleItem'])->name('stripe.successSingleItem');
