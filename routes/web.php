<?php

use App\Http\Controllers\shopify\MappedCategoryController;
use App\Http\Controllers\shopify\ProductCategoryController;
use App\Http\Controllers\shopify\ProductController;
use App\Http\Controllers\shopify\WebhookController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CharitiesController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserRoles;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\PortalSettingsController;
use App\Models\Orders\Orders;
use App\Models\PortalSettings;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
*/
//login routes
Route::prefix(env('ADMIN_PREFIX'))->group(function () {

    Route::get('login', [AdminController::class, 'ShowLogin'])->name('login');
    Route::get('/', [AdminController::class, 'ShowLogin']);
    Route::post('/authenticate', [AdminController::class, 'authenticate']); //verify user on login
    //Forgot Password and reset Routes
    Route::get('/forget-password', function () {
        return view('admin.pages.account.forget-password');
    });
    Route::get('/recover-password', function () {
        return view('admin.pages.account.recover-password');
    });

    // Seller registration route
    Route::get('/seller-register', [AdminController::class, 'ShowSellerRegisterForm']);
    Route::post('/insert-seller-user', [AdminController::class, 'insertNewSellerUser']);

    // Shopify web hooks route
    Route::post('/shopify_webhook', [WebhookController::class, 'index']);

    // Magento web hooks route
    Route::post('/magento_webhook', [WebhookController::class, 'magento_index']);
});



//Password Routes
Route::post('/forgotpassword', [PasswordController::class, 'forgotpassword']);
Route::post('/recoverpassword', [PasswordController::class, 'recoverpassword']);
Route::post('/password-reset', [PasswordController::class, 'sendPasswordResetToken']);
Route::get('/reset-password/', [PasswordController::class, 'showPasswordResetForm']);
Route::post('/submit-password', [PasswordController::class, 'resetPassword']);


//protected routes of admin
Route::group([
    'prefix'     => env('ADMIN_PREFIX'),
    'middleware' => ['auth', 'CheckRole']
], function () {
    //store_connection routes
    Route::get('dashboard', [DashboardController::class, 'index']);

    // shopify connector products route
    Route::get('products', [ProductController::class, 'index']);
    Route::get('sync_products', [ProductController::class, 'syncProducts']);
    Route::get('get_store_products', [ProductController::class, 'getStoreProducts']);
    Route::get('/products/edit/{id}', [ProductController::class, 'editProduct']);

    // shopify connector categories route
    Route::get('product_categories', [ProductCategoryController::class, 'index']);
    Route::get('sync_categories', [ProductCategoryController::class, 'syncCategories']);
    Route::post('map_categories', [MappedCategoryController::class, 'mapCategories']);

    //Route::post('stores', [StoreController::class, 'getUserStores']);
    Route::get('/store_connection', [StoreController::class, 'show']);
    Route::post('/update_store_connection', [StoreController::class, 'update']);

    //Admin Users Routes
    Route::get('/users',  [AdminController::class, 'showusers']); //page index
    //Route::get('users/register',  [AdminController::class, 'registerUser']);//create user
    //Route::post('/insert_user', [AdminController::class, 'insert']);//insert user
    Route::resource('/charities', CharitiesController::class); //create user
    Route::get('/orderdetail/{id}', [OrdersController::class, "orderDetail"]); //create order

    Route::resource('/orders', OrdersController::class); //create order

    Route::resource('/settings', PortalSettingsController::class); //create order

    Route::get('/notificationscount', [NotificationsController::class, "notifications_count"]); //create order

    Route::get('/notifications', [NotificationsController::class, "index"]); //create order
    Route::post('/readnotifications/{id}', [NotificationsController::class, "readnotifications"]); //create order

    Route::get('/ordercomplete/{id}', [OrdersController::class, "orderComplete"]); //create order

    Route::put('/updatesettings/{id}', [PortalSettingsController::class, "update"]); //create order

    //get assigned products for datatable 
    Route::get('/charities/{id}/assigned_products', [CharitiesController::class, 'getAssignedProductsForDataTable']); //create user

    //show assign product form
    Route::get('productToCharity/{id}', [CharitiesController::class, 'assignproducts']);
    Route::get('getProuctsForCharity', [CharitiesController::class, 'getProuctsForCharity']);
    Route::post('thisproductassigntocharity', [CharitiesController::class, 'assignthisproducttocharity']);
    Route::delete('assignedproductdestroy/{id}', [CharitiesController::class, 'assignedproductdestroy'])->name("assignedproductdestroy");

    Route::get('/logout', [AdminController::class, 'logout']); //logout user
    Route::get('/users/edit/{id}', [AdminController::class, 'edituser']);
    Route::post('/update-user-data/{id}', [AdminController::class, 'updateuserdata']);

    Route::post('/update-user-password/{id}', [AdminController::class, 'updateuserpassword']);
    Route::get('/user/delete/{id}', [AdminController::class, 'deleteuser']);
    Route::post('/updateStatus', [AdminController::class, 'updateStatus']);
    //Website Settings Page

});



// Route::get('/{slug}',   [PageController::class, 'showslug']);
// MailTrap Test Code

// Route::get('/sendmail', function (Request $request) {

// 	 Mail::to($email)
//         		->send(new MailTesting($name,$email,$link));
//     return 'A message has been sent to Mailtrap!';
// });

// Mail Trap End
