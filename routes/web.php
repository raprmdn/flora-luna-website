<?php

use App\Http\Controllers\Account\AccountController;
use App\Http\Controllers\Article\{ArticleController, ArticleCategoryController};
use App\Http\Controllers\{Account\HistoryController,
    Gems\GemsController,
    Gems\RechargeUserGemsController,
    IndexController,
    Order\OrderListController,
    PaymentController,
    Product\ItemController,
    Product\ProductCategoryController,
    Product\ProductController,
    Product\ProductLabelController,
    StoreController,
    UserController};
use App\Http\Controllers\RolePermission\{RoleController,
    PermissionController,
    RolePermissionController,
    UserRoleController};
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('index');
Route::prefix('account')->middleware('auth')->group(function () {
    Route::get('', [AccountController::class, 'index'])->name('account');
    Route::put('', [AccountController::class, 'updateProfile']);
    Route::get('change-password', [AccountController::class, 'changePassword'])->name('change.password');
    Route::put('change-password', [AccountController::class, 'updatePassword']);

    Route::get('history', [HistoryController::class, 'itemHistory'])->name('item.history');
    Route::get('history/table', [HistoryController::class, 'itemHistoryTable'])->name('item.history.table');
    Route::get('history/purchases', [HistoryController::class, 'purchaseHistory'])->name('purchase.history');
    Route::get('history/purchases/table', [HistoryController::class, 'purchaseHistoryTable'])->name('purchase.history.table');
    Route::get('history/invoice/{order:invoice}', [HistoryController::class, 'invoice'])->name('invoice');

    Route::get('payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('payment', [PaymentController::class, 'payment'])->name('payment');
});

Route::prefix('itemshop')->group(function () {
    Route::get('featured', [StoreController::class, 'index'])->name('store');
    Route::get('', [StoreController::class, 'index'])->name('store');
    Route::get('/{category:url}', [StoreController::class, 'showByCategory'])->name('store.category');
    Route::get('view/{product}', [StoreController::class, 'showProduct'])->name('store.detail');
    Route::post('item/purchase', [StoreController::class, 'purchaseItem'])->middleware('auth')->name('item.purchase');
});

Route::prefix('p')->middleware(['auth', 'role:Game Master|Moderator'])->group(function () {
    Route::prefix('orders')->group(function () {
        Route::get('item', [OrderListController::class, 'listItemOrder'])
            ->middleware('permission:list item order')->name('list.item.order');
        Route::get('item/table', [OrderListController::class, 'listItemOrderTable'])
            ->middleware('permission:list item order')->name('list.item.table');
        Route::get('gems', [OrderListController::class, 'listGemsOrder'])
            ->middleware('permission:list gems order')->name('list.gems.order');
        Route::get('gems/table', [OrderListController::class, 'listGemsOrderTable'])
            ->middleware('permission:list gems order')->name('list.gems.table');
    });
    Route::get('user-list', [UserController::class, 'index'])->name('user.list');
    Route::get('user-table', [UserController::class, 'table'])->name('user.table');
    Route::prefix('gems')->middleware(['permission:recharge gems|edit gems price'])->group(function () {
        Route::get('recharge', [RechargeUserGemsController::class, 'index'])->name('recharge.gems');
        Route::post('recharge', [RechargeUserGemsController::class, 'recharge']);

        Route::get('list', [GemsController::class, 'index'])->name('gems.index');
        Route::get('list/table', [GemsController::class, 'table'])->name('gems.table');
        Route::get('create', [GemsController::class, 'create'])->name('gems.create');
        Route::post('create', [GemsController::class, 'store']);
        Route::get('edit/{gem}', [GemsController::class, 'edit'])->name('gems.edit');
        Route::put('edit/{gem}', [GemsController::class, 'update']);
        Route::delete('delete/{gem}', [GemsController::class, 'destroy'])->name('gems.delete');
    });
    Route::prefix('announcement')->middleware(['permission:create post'])->group(function () {
        Route::prefix('category')->group(function () {
            Route::get('', [ArticleCategoryController::class, 'index'])->name('article.category');
            Route::post('', [ArticleCategoryController::class, 'store']);
            Route::get('{category:slug}', [ArticleCategoryController::class, 'edit'])->name('article.category.edit');
            Route::put('{category:slug}', [ArticleCategoryController::class, 'update'])->name('article.category.update');
            Route::delete('{category:slug}', [ArticleCategoryController::class, 'destroy'])->name('article.category.delete');
        });
        Route::get('', [ArticleController::class, 'index'])->name('article.index');
        Route::get('create', [ArticleController::class, 'create'])->name('article.create');
        Route::post('create', [ArticleController::class, 'store']);
        Route::get('{article:slug}', [ArticleController::class, 'edit'])->name('article.edit');
        Route::put('{article:slug}', [ArticleController::class, 'update']);
        Route::delete('{article:slug}', [ArticleController::class, 'destroy'])->name('article.delete');
        Route::post('/image-content/delete', [ArticleController::class, 'deleteImageContent'])->name('delete.content.image');
    });
    Route::prefix('product')->middleware(['permission:create product|create item'])->group(function () {

        Route::get('list', [ProductController::class, 'index'])->name('product.index');
        Route::get('list/table', [ProductController::class, 'table'])->name('product.list.table');
        Route::get('create', [ProductController::class, 'create'])->name('product.create');
        Route::post('create', [ProductController::class, 'store']);
        Route::get('{product:slug}/edit', [ProductController::class, 'edit'])->name('product.edit');
        Route::put('{product:slug}/edit', [ProductController::class, 'update']);
        Route::delete('{product:slug}/delete', [ProductController::class, 'destroy'])->name('product.delete');
        Route::get('{product:slug}/detail', [ProductController::class, 'detail'])->name('product.detail');
        Route::get('{product:slug}/add-item', [ItemController::class, 'addProductItem'])->name('add.product.item');
        Route::post('{product:slug}/add-item', [ItemController::class, 'storeProductItem']);

        Route::prefix('item')->group(function () {
            Route::get('', [ItemController::class, 'index'])->name('item.index');
            Route::get('table', [ItemController::class, 'table'])->name('item.list.table');
            Route::get('create', [ItemController::class, 'create'])->name('item.create');
            Route::post('create', [ItemController::class, 'store']);
            Route::get('{item}/edit', [ItemController::class, 'edit'])->name('item.edit');
            Route::put('{item}/edit', [ItemController::class, 'update']);
            Route::delete('{item}/delete', [ItemController::class, 'destroy'])->name('item.delete');
        });
        Route::prefix('category')->group(function () {
            Route::get('', [ProductCategoryController::class, 'index'])->name('product.category.index');
            Route::post('', [ProductCategoryController::class, 'store'])->name('product.category.store');
            Route::get('{category:slug}/edit', [ProductCategoryController::class, 'edit'])->name('product.category.edit');
            Route::put('{category:slug}/edit', [ProductCategoryController::class, 'update']);
            Route::delete('{category:slug}/delete', [ProductCategoryController::class, 'destroy'])->name('product.category.delete');
        });
        Route::prefix('label')->group(function () {
            Route::get('', [ProductLabelController::class, 'index'])->name('label.index');
            Route::post('', [ProductLabelController::class, 'store'])->name('label.store');
            Route::get('{label:slug}/edit', [ProductLabelController::class, 'edit'])->name('label.edit');
            Route::put('{label:slug}/edit', [ProductLabelController::class, 'update']);
            Route::delete('{label:slug}/delete', [ProductLabelController::class, 'destroy'])->name('label.destroy');
        });
    });
    Route::prefix('role-permission')->middleware(['permission:assign permission'])->group(function () {

        Route::prefix('role')->group(function () {
            Route::get('', [RoleController::class, 'index'])->name('role.index');
            Route::post('', [RoleController::class, 'store'])->name('role.store');
            Route::get('{role}', [RoleController::class, 'edit'])->name('role.update');
            Route::put('{role}', [RoleController::class, 'update']);
            Route::delete('{role}', [RoleController::class, 'destroy'])->name('role.delete');
        });

        Route::prefix('permission')->group(function () {
            Route::get('', [PermissionController::class, 'index'])->name('permission.index');
            Route::post('', [PermissionController::class, 'store'])->name('permission.store');
            Route::get('{permission}', [PermissionController::class, 'edit'])->name('permission.update');
            Route::put('{permission}', [PermissionController::class, 'update']);
            Route::delete('{permission}', [PermissionController::class, 'destroy'])->name('permission.delete');
        });

        Route::get('sync', [RolePermissionController::class, 'index'])->name('sync.index');
        Route::post('sync-role-permissions', [RolePermissionController::class, 'sync'])->name('sync.role.permission');

        Route::get('user-role', [UserRoleController::class, 'index'])->name('user.role');
        Route::post('user-role', [UserRoleController::class, 'sync'])->name('sync.user.role');
    });
});

require __DIR__.'/auth.php';
