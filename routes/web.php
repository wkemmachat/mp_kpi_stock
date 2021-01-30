<?php

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
    return view('welcome');
});

Route::get('/test_blank', function () {

    // $message = "Please verify your email to go to the dashboard";
    // Toastr::success($message, $title = "Email Verification", $options = []);

    return view('test_blank');
});

Auth::routes();

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

// Route::resource('category','CategoryController');

Route::get('/home', 'HomeController@index')->name('home');

// Category
// Route::get('/category', 'CategoryController@index')->name('category');
// Route::post('/category', 'CategoryController@store')->name('category.store');
// Route::get('/category/edit/{id}','CategoryController@edit');
// Route::match(['put', 'patch'], '/category/update/{id}','CategoryController@update')->name('category.update');
// Route::delete('/category/{id}', 'CategoryController@destroy')->name('category.destroy');

// Role
Route::get('user/role', 'RoleController@index')->name('role');
Route::post('user/role', 'RoleController@store')->name('role.store');
Route::match(['put', 'patch'], 'user/role/update/{id}','RoleController@update')->name('role.update');
Route::delete('user/role/{id}', 'RoleController@destroy')->name('role.destroy');

// Role_User
Route::get('user/manageRole_user', 'RoleController@role_user_index')->name('role_user');
Route::post('user/manageRole_user', 'RoleController@role_user_store')->name('role_user.store');
Route::get('user/manageRole_user/edit/{id}','RoleController@role_user_edit')->name('role_user.edit');
Route::match(['put', 'patch'], 'user/manageRole_user/update/{id}','RoleController@role_user_update')->name('role_user.update');

// User
Route::post('/user/manageUser', 'UserController@store')->name('user.store');
Route::get('/user/manageUser', 'UserController@index')->name('user');
Route::get('/user/manageUser/edit/{id}','UserController@edit')->name('user.edit');
Route::match(['put', 'patch'], '/user/manageUser/update/{id}','UserController@update')->name('user.update');
Route::delete('/user/manageUser/{id}', 'UserController@destroy')->name('user.destroy');
Route::get('/user/manageUser/changeStatus/{id}','UserController@changeStatus')->name('user.changeStatus');

// Product
Route::post('/product/manageProduct', 'ProductController@store')->name('product.store');
Route::get('/product/manageProduct', 'ProductController@index')->name('product');
Route::get('/product/manageProduct/edit/{id}','ProductController@edit')->name('product.edit');
Route::match(['put', 'patch'], '/product/manageProduct/update/{id}','ProductController@update')->name('product.update');

// Upload
Route::get('/upload', 'ProductController@upload_index')->name('upload');
Route::post('/upload', 'ProductController@import')->name('product.import');

// Transfer_In_not_Approved
Route::post('/transfer_in_not_approve', 'TransferInOutController@store_in')->name('transfer_in.store_in');
Route::get('/transfer_in_not_approve', 'TransferInOutController@index_in')->name('transfer_in');
Route::delete('/transfer_in_not_approve/{id}', 'TransferInOutController@destroy_in')->name('transfer_in.destroy_in');

// Transfer_In_Approved
Route::post('/transfer_in_approve', 'TransferInOutController@approve_in')->name('transfer_in_approve.approve_in');
Route::get('/transfer_in_approve', 'TransferInOutController@index_in_approve')->name('transfer_in_approve');

// Transfer_Out_not_Approved
Route::post('/transfer_out_not_approve', 'TransferInOutController@store_out')->name('transfer_out.store_out');
Route::get('/transfer_out_not_approve', 'TransferInOutController@index_out')->name('transfer_out');
Route::delete('/transfer_out_not_approve/{id}', 'TransferInOutController@destroy_out')->name('transfer_out.destroy_out');

// Transfer_Out_Approved
Route::post('/transfer_out_approve', 'TransferInOutController@approve_out')->name('transfer_out_approve.approve_out');
Route::get('/transfer_out_approve', 'TransferInOutController@index_out_approve')->name('transfer_out_approve');

// Stock_Real_Time
Route::get('/stock_real_time', 'StockRealTimeController@index')->name('stock_real_time');
Route::post('/stock_real_time_search_by_product_id', 'StockRealTimeController@searchByProductId')->name('stock_real_time.searchByProductId');
Route::post('/stock_real_time_search_by_category_id', 'StockRealTimeController@searchByCategoryId')->name('stock_real_time.searchByCategoryId');
Route::post('/stock_real_time_search_min', 'StockRealTimeController@searchMinProduct')->name('stock_real_time.searchMinProduct');


// Route::get('/transfer_in/edit/{id}','ProductController@edit')->name('transfer_in.edit');
// Route::match(['put', 'patch'], '/product/manageProduct/update/{id}','ProductController@update')->name('product.update');

// product_category
Route::get('/category', 'ProductCategoryController@index')->name('category');
Route::post('/category', 'ProductCategoryController@store')->name('category.store');
Route::get('/category/edit/{id}','ProductCategoryController@edit')->name('category.edit');
Route::match(['put', 'patch'], '/category/update/{id}','ProductCategoryController@update')->name('category.update');


// KPI
Route::get('/kpi_output/{id}','KpiOutputController@index')->name('kpi_output');
Route::post('/kpi_output/{id}','KpiOutputController@store')->name('kpi_output.store');
Route::delete('/kpi_output/{id}','KpiOutputController@destroy')->name('kpi_output.delete');

// Export
Route::post('/exportKPI', 'KpiOutputController@exportKPI')->name('kpi_output.exportKPI');
Route::post('/exportProduct', 'ProductController@exportProduct')->name('product.exportProduct');
Route::post('/exportProductCollection', 'ProductController@exportProductCollection')->name('product.exportProductCollection');
Route::post('/exportProductCollectionQuery', 'ProductController@exportProductCollectionQuery')->name('product.exportProductCollectionQuery');
Route::post('/exportProductView', 'ProductController@exportProductView')->name('product.exportProductView');
Route::post('/exportTransferInOut', 'TransferInOutController@exportTransferInOut')->name('transfer.exportTransferInOut');
Route::post('/exportStock', 'StockRealTimeController@exportStock')->name('stock.exportStock');
Route::post('/exportTestPHP', 'PhpSpreadSheetController@index')->name('phpExport.index');


// KemLogin
Route::post('/login_kem', 'KemLoginController@login')->name('login_kem');


/*
// qc
Route::get('/qc', 'QcController@index')->name('qc');
Route::post('/qc', 'QcController@store')->name('qc.store');

// production
Route::get('/production', 'ProductionController@index')->name('production');
Route::post('/production', 'ProductionController@store')->name('production.store');

// store
Route::get('/store', 'StoreController@index')->name('store');
Route::post('/store', 'StoreController@store')->name('store.store');

// planning
Route::get('/planning', 'PlanningController@index')->name('planning');
Route::post('/planning', 'PlanningController@store')->name('planning.store');

// logistic
Route::get('/logistic', 'LogisticController@index')->name('logistic');
Route::post('/logistic', 'LogisticController@store')->name('logistic.store');
*/
