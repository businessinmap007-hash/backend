



<?php


use Illuminate\Http\Request;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

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




Route::get('lang/{language}', ['as' => 'lang.switch', 'uses' => 'App\Http\Controllers\LanguageController@switchLang']);


Route::get('/', 'App\Http\Controllers\HomeController@index')->name('user.home');
Route::get('/home', 'App\Http\Controllers\HomeController@index')->name('user.home');


Route::get('/user/login', 'App\Http\Controllers\LoginController@showLogin')->name('get.user.login');

Route::get('/user/register', 'App\Http\Controllers\RegistrationController@showRegister')->name('get.user.register');
Route::post('/user/auth/login', 'App\Http\Controllers\LoginController@login')->name('user.login');
Route::get('user/auth/logout', 'App\Http\Controllers\LoginController@logout')->name('user.auth.logout');
Route::post('user/signup', 'App\Http\Controllers\RegistrationController@signup')->name('user.signup');
Route::post('user/col/check', 'App\Http\Controllers\RegistrationController@checkIsColExist')->name('user.col.check');
Route::post('activation/account', 'App\Http\Controllers\ActivationController@postActivationCode')->name('activation.account');
Route::post('forgot/password', 'App\Http\Controllers\ForgotPasswordController@sendCode')->name('user.forgot.password');
Route::post('check/reset/code', 'App\Http\Controllers\ResetPasswordController@checkCode')->name('check.reset.code');
Route::post('reset/password', 'App\Http\Controllers\ResetPasswordController@reset')->name('reset.password');
Route::post('resend/activation/password', 'App\Http\Controllers\ResetPasswordController@resendActivationCode')->name('resend.activation.code');

Route::get('shopping/cart', 'App\Http\Controllers\CartController@index')->name('cart');
Route::get('category/products', 'App\Http\Controllers\ProductController@index')->name('category.products');

Route::get('products/{product}/details', 'App\Http\Controllers\ProductController@details')->name('product.details');

Route::get('categories', 'App\Http\Controllers\CategoryController@index')->name('categories');

Route::get("order/payment", "App\Http\Controllers\OrdersController@orderPayment")->name('order.payment');
Route::get("aboutus", "App\Http\Controllers\PageController@aboutUs")->name('aboutus');


Route::get('get/search', "App\Http\Controllers\SearchController@index")->name('get.search');
Route::post('search', "App\Http\Controllers\SearchController@searchPost")->name('search.post');


Route::get('contactus', "App\Http\Controllers\SupportController@index")->name('contactus');
Route::post('contactus', "App\Http\Controllers\SupportController@contactMessage")->name('contactus.post');


Route::post('/get/all/cities/by/country', 'App\Http\Controllers\LocationController@getCities')->name('get.all.selected.cities');




Route::post('add/product/to/cart', "App\Http\Controllers\CartController@addToCart")->name('add.to.cart');
Route::post('delete/item/cart', 'App\Http\Controllers\CartController@deleteCartItem')->name('delete.item.cart');
Route::post('update/item/cart', 'App\Http\Controllers\CartController@updateCartQty')->name('update.item.cart');
Route::post('add/to/wishlist', "App\Http\Controllers\WishlistController@addToWishList")->name('add.to.wishlist');
Route::post('rate/comments', "App\Http\Controllers\RateController@postRate")->name('rate.comments');


Route::get('wishlist', "App\Http\Controllers\WishlistController@index")->name('wishlist');


Route::post('check/column/exist', function (Request $request) {

    $model = $request->model;
    $item = $request->item;
    $column = $request->column;

    $itemId = $request->itemId;


    $myModel = $model::where($column, $item)->first();
    if (isset($itemId) && $itemId != 'undefined') {
        $myModel = $model::where([$column => $item])->where('id', '!=', $itemId)->first();
    }

    if (!empty($myModel)) {
        return response()->json([
            'status' => true,
            'data' => $myModel,
            'message' => "هذا الرقم مسجل من قبل"

        ]);
    } else {
        return response()->json([
            'status' => false,
        ]);

    }


})->name('check.column.exist');



Route::get('activationcode/{phone}', function ($phone) {

    $user = User::wherePhone($phone)->first();

    if ($user) {
        return "Activation Code Is: " . $user->action_code;
    } else {
        return "User Not Found";
    }

});


Route::post('upload/file', "App\Http\Controllers\FilesController@uploadFile")->name('upload.file');





Route::get('/user/profile', 'App\Http\Controllers\ProfileController@profile')->name('profile');
Route::get('/user/addresses', 'App\Http\Controllers\ProfileController@userAddresses')->name('addresses');
Route::resource('addresses', 'App\Http\Controllers\AddressController');
Route::post('addresses/update/primary', 'App\Http\Controllers\AddressController@updatePrimaryAddress')->name('update.primary.address');
Route::post('/profile/update', 'App\Http\Controllers\ProfileController@profileUpdateUser')->name('profile.update');





Route::get("terms-and-conditions", "App\Http\Controllers\PageController@termsAndConditions")->name('terms');
Route::get("privacy-and-policy", "App\Http\Controllers\PageController@privacy")->name('privacy');
Route::get("ask/orders", "App\Http\Controllers\OrdsController@askOrder")->name('ask.order');
Route::get("faqs", "App\Http\Controllers\ListsController@faqs")->name('faqs');

Route::get("offers", "App\Http\Controllers\OfferController@index")->name('offers.index');
Route::get("offer/{id}/details", "App\Http\Controllers\OrdsController@offerDetails")->name('present.offer.details');
Route::get("ask/order/{id}/details", "App\Http\Controllers\OrdsController@orderDetails")->name('ask.order.details');

Route::post('/newsletter/user/subscription', 'App\Http\Controllers\HomeController@subscription')->name('subscription.newsletter');




Route::resource('connections', 'App\Http\Controllers\ConnectionsController');


Route::get('/operation/success', function () {
    return view('thanks');
})->name('success.operation');







Auth::routes();

Route::prefix('administrator')->middleware(['auth:admin'])->group(function () {



    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard');

    // MENU
    Route::resource('menu-items', App\Http\Controllers\Admin\MenuItemController::class);
    Route::resource('menu-categories', App\Http\Controllers\Admin\MenuCategoryController::class);
    Route::resource('menu-extras', App\Http\Controllers\Admin\MenuItemExtraController::class);
    Route::resource('menu-sizes', App\Http\Controllers\Admin\MenuItemSizeController::class);

    // ORDERS
    Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])
        ->name('admin.orders.index');
    Route::get('/orders/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])
        ->name('admin.orders.show');
    Route::post('/orders/{id}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])
        ->name('admin.orders.status');

    // BUSINESSES
    Route::resource('businesses', App\Http\Controllers\Admin\BusinessController::class);

    // BRANCHES
    Route::resource('branches', App\Http\Controllers\Admin\BranchController::class);
});











