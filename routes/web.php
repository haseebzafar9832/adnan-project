<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DiscountController as AdminDiscountController;
use App\Http\Controllers\Admin\GroupController as AdminGroupController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BulkGroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
Route::get('/groups', [BulkGroupController::class, 'index'])->name('groups.index');
Route::get('/pricing/quote', [PricingController::class, 'quote'])->name('pricing.quote');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'desk'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'password'])->name('profile.password');

    Route::get('/groups/create', [BulkGroupController::class, 'create'])->name('groups.create');
    Route::post('/groups', [BulkGroupController::class, 'store'])->name('groups.store');
    Route::get('/my-groups', [BulkGroupController::class, 'mine'])->name('groups.mine');
    Route::post('/groups/{group}/join', [BulkGroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/close', [BulkGroupController::class, 'close'])->name('groups.close');

    Route::get('/groups/{group}/pay', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/groups/{group}/pay', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotificationController::class, 'count'])->name('notifications.count');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::get('/notifications/{notification}', [NotificationController::class, 'open'])->name('notifications.open');

    Route::get('/groups/{group}/messages', [MessageController::class, 'poll'])->name('messages.poll');
    Route::post('/groups/{group}/messages', [MessageController::class, 'store'])->name('messages.store');
});

Route::get('/groups/{group}', [BulkGroupController::class, 'show'])->name('groups.show');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('products', AdminProductController::class)->except(['show']);

    Route::get('/discounts', [AdminDiscountController::class, 'index'])->name('discounts.index');
    Route::post('/discounts', [AdminDiscountController::class, 'store'])->name('discounts.store');
    Route::delete('/discounts/{discount}', [AdminDiscountController::class, 'destroy'])->name('discounts.destroy');

    Route::get('/groups', [AdminGroupController::class, 'index'])->name('groups.index');
    Route::post('/groups/{group}/close', [AdminGroupController::class, 'close'])->name('groups.close');
    Route::post('/groups/{group}/cancel', [AdminGroupController::class, 'cancel'])->name('groups.cancel');

    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/{payment}', [AdminTransactionController::class, 'update'])->name('transactions.update');
});
