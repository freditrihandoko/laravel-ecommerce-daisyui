<?php

use App\Livewire\UserOrder;
use App\Livewire\OrderDetail;
use App\Livewire\ProductList;
use App\Livewire\UserAddress;
use App\Livewire\ShoppingCart;
use App\Livewire\DashboardUser;
use App\Livewire\ProductDetail;
use App\Livewire\TestComponent;
use App\Livewire\DashboardAdmin;
use App\Livewire\OrderManagement;
use App\Livewire\ProductManagement;
use App\Livewire\CategoryManagement;
use App\Livewire\CustomerManagement;
use App\Livewire\DiscountManagement;
use App\Livewire\ShippingManagement;
use App\Livewire\HeroSlideManagement;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderPrintController;
use App\Livewire\PaymentShippingOrderSettings;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('product', ProductList::class)->name('product-list');

// Route::get('product-detail', ProductDetail::class)->name('product-detail');
Route::get('/product/{productSlug}', ProductDetail::class)->name('product-detail');

Route::middleware(['auth'])->group(function () {
    Route::get('dashboard-user', DashboardUser::class)->name('dashboard-user');
    Route::get('cart', ShoppingCart::class)->name('cart');
    Route::get('manage-address', UserAddress::class)->name('manage-address');
    Route::get('user-order', UserOrder::class)->name('user-order.index');
    Route::get('/order-detail/{orderId}', OrderDetail::class)->name('order-detail');
});


Route::middleware(['auth', 'verified', 'superadmin'])->group(function () {

    Route::get('dashboard-admin', DashboardAdmin::class)->name('dashboard');

    Route::get('category-management', CategoryManagement::class)->name('category-management');

    Route::get('product-management', ProductManagement::class)->name('product-management');

    Route::get('order-management', OrderManagement::class)->name('order-management');

    Route::get('shipping-management', ShippingManagement::class)->name('shipping-management');

    Route::get('/order/{order}/print', [OrderPrintController::class, 'show'])->name('order.print');

    Route::get('hero-slide-management', HeroSlideManagement::class)->name('hero-slide-management');

    Route::get('customer-management', CustomerManagement::class)->name('customer-management');

    Route::get('discount-management', DiscountManagement::class)->name('discount-management');

    Route::get('settings', PaymentShippingOrderSettings::class)->name('settings');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// Route::get('test-component', TestComponent::class)->name('test-component');

require __DIR__ . '/auth.php';
