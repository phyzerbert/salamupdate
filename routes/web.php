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
    return redirect(route('login'));
});

Auth::routes();

Route::get('lang/{locale}', 'VerifyController@lang')->name('lang');
Route::get('/verify', 'VerifyController@show')->name('verify');
Route::post('/verify', 'VerifyController@verify')->name('verify');

Route::any('/home', 'HomeController@index')->name('home');

Route::get('/profile', 'UserController@profile')->name('profile');
Route::post('/updateuser', 'UserController@updateuser')->name('updateuser');
Route::any('/users/index', 'UserController@index')->name('users.index');
Route::post('/user/create', 'UserController@create')->name('user.create');
Route::post('/user/edit', 'UserController@edituser')->name('user.edit');
Route::get('/user/delete/{id}', 'UserController@delete')->name('user.delete');

Route::any('/customer/index', 'CustomerController@index')->name('customer.index');
Route::post('/customer/create', 'CustomerController@create')->name('customer.create');
Route::post('/customer/edit', 'CustomerController@edit')->name('customer.edit');
Route::get('/customer/delete/{id}', 'CustomerController@delete')->name('customer.delete');

Route::any('/supplier/index', 'SupplierController@index')->name('supplier.index');
Route::post('/supplier/create', 'SupplierController@create')->name('supplier.create');
Route::post('/supplier/purchase_create', 'SupplierController@purchase_create')->name('supplier.purchase_create');
Route::post('/supplier/edit', 'SupplierController@edit')->name('supplier.edit');
Route::get('/supplier/delete/{id}', 'SupplierController@delete')->name('supplier.delete');

// ******** Settings *************

Route::any('/company/index', 'CompanyController@index')->name('company.index');
Route::post('/company/create', 'CompanyController@create')->name('company.create');
Route::post('/company/edit', 'CompanyController@edit')->name('company.edit');
Route::get('/company/delete/{id}', 'CompanyController@delete')->name('company.delete');


Route::any('/category/index', 'CategoryController@index')->name('category.index');
Route::post('/category/create', 'CategoryController@create')->name('category.create');
Route::post('/category/edit', 'CategoryController@edit')->name('category.edit');
Route::get('/category/delete/{id}', 'CategoryController@delete')->name('category.delete');


Route::any('/store/index', 'StoreController@index')->name('store.index');
Route::post('/store/create', 'StoreController@create')->name('store.create');
Route::post('/store/edit', 'StoreController@edit')->name('store.edit');
Route::get('/store/delete/{id}', 'StoreController@delete')->name('store.delete');

Route::any('/tax_rate/index', 'TaxRateController@index')->name('tax_rate.index');
Route::post('/tax_rate/create', 'TaxRateController@create')->name('tax_rate.create');
Route::post('/tax_rate/edit', 'TaxRateController@edit')->name('tax_rate.edit');
Route::get('/tax_rate/delete/{id}', 'TaxRateController@delete')->name('tax_rate.delete');


Route::any('/product/index', 'ProductController@index')->name('product.index');
Route::get('/product/create', 'ProductController@create')->name('product.create');
Route::post('/product/save', 'ProductController@save')->name('product.save');
Route::post('/product/ajax_create', 'ProductController@ajax_create')->name('product.ajax_create');
Route::get('/product/edit/{id}', 'ProductController@edit')->name('product.edit');
Route::post('/product/update', 'ProductController@update')->name('product.update');
Route::get('/product/detail/{id}', 'ProductController@detail')->name('product.detail');
Route::get('/product/delete/{id}', 'ProductController@delete')->name('product.delete');

Route::any('/purchase/index', 'PurchaseController@index')->name('purchase.index');
Route::get('/purchase/create', 'PurchaseController@create')->name('purchase.create')->middleware('role:user');
Route::post('/purchase/save', 'PurchaseController@save')->name('purchase.save');
Route::get('/purchase/edit/{id}', 'PurchaseController@edit')->name('purchase.edit');
Route::post('/purchase/update', 'PurchaseController@update')->name('purchase.update');
Route::get('/purchase/detail/{id}', 'PurchaseController@detail')->name('purchase.detail');
Route::get('/purchase/delete/{id}', 'PurchaseController@delete')->name('purchase.delete');

Route::any('/pre_order/index', 'PreOrderController@index')->name('pre_order.index');
Route::get('/pre_order/create', 'PreOrderController@create')->name('pre_order.create');
Route::post('/pre_order/save', 'PreOrderController@save')->name('pre_order.save');
Route::get('/pre_order/edit/{id}', 'PreOrderController@edit')->name('pre_order.edit');
Route::post('/pre_order/update', 'PreOrderController@update')->name('pre_order.update');
Route::get('/pre_order/detail/{id}', 'PreOrderController@detail')->name('pre_order.detail');
Route::get('/pre_order/delete/{id}', 'PreOrderController@delete')->name('pre_order.delete');
Route::get('/pre_order/receive/{id}', 'PreOrderController@receive')->name('pre_order.receive');
Route::post('/pre_order/save_receive', 'PreOrderController@save_receive')->name('pre_order.save_receive');

Route::any('/received_order/index', 'PreOrderController@received_orders')->name('received_order.index');
Route::get('/received_order/edit/{id}', 'PreOrderController@edit_received_order')->name('received_order.edit');
Route::post('/received_order/update', 'PreOrderController@update_received_order')->name('received_order.update');
Route::get('/received_order/detail/{id}', 'PreOrderController@detail_received_order')->name('received_order.detail');
Route::get('/received_order/delete/{id}', 'PreOrderController@delete_received_order')->name('received_order.delete');

Route::any('/sale/index', 'SaleController@index')->name('sale.index');
Route::get('/sale/create', 'SaleController@create')->name('sale.create')->middleware('role:user');
Route::post('/sale/save', 'SaleController@save')->name('sale.save');
Route::get('/sale/edit/{id}', 'SaleController@edit')->name('sale.edit');
Route::post('/sale/update', 'SaleController@update')->name('sale.update');
Route::get('/sale/detail/{id}', 'SaleController@detail')->name('sale.detail');
Route::get('/sale/delete/{id}', 'SaleController@delete')->name('sale.delete');


Route::any('/payment/index/{type}/{id}', 'PaymentController@index')->name('payment.index');
Route::post('/payment/create', 'PaymentController@create')->name('payment.create');
Route::post('/payment/edit', 'PaymentController@edit')->name('payment.edit');
Route::get('/payment/delete/{id}', 'PaymentController@delete')->name('payment.delete');

Route::get('get_products', 'VueController@get_products');
Route::post('get_orders', 'VueController@get_orders');
Route::post('get_product', 'VueController@get_product');
Route::get('get_first_product', 'VueController@get_first_product');
Route::post('get_data', 'VueController@get_data');
Route::post('get_pre_order', 'VueController@get_pre_order');
Route::post('get_received_quantity', 'VueController@get_received_quantity');
Route::post('get_autocomplete_products', 'VueController@get_autocomplete_products');

// ******** Report ********

Route::any('/report/index', 'ReportController@index')->name('report.index');
Route::any('/report/overview_chart', 'ReportController@overview_chart')->name('report.overview_chart');
Route::any('/report/company_chart', 'ReportController@company_chart')->name('report.company_chart');
Route::any('/report/store_chart', 'ReportController@store_chart')->name('report.store_chart');
Route::any('/report/product_quantity_alert', 'ReportController@product_quantity_alert')->name('report.product_quantity_alert');
Route::any('/report/product_expiry_alert', 'ReportController@product_expiry_alert')->name('report.product_expiry_alert');
Route::any('/report/expired_purchases_report', 'ReportController@expired_purchases_report')->name('report.expired_purchases_report');
Route::any('/report/products_report', 'ReportController@products_report')->name('report.products_report');
Route::any('/report/categories_report', 'ReportController@categories_report')->name('report.categories_report');
Route::any('/report/sales_report', 'ReportController@sales_report')->name('report.sales_report');
Route::any('/report/purchases_report', 'ReportController@purchases_report')->name('report.purchases_report');
Route::any('/report/daily_sales', 'ReportController@daily_sales')->name('report.daily_sales');
Route::any('/report/monthly_sales', 'ReportController@monthly_sales')->name('report.monthly_sales');
Route::any('/report/payments_report', 'ReportController@payments_report')->name('report.payments_report');
Route::any('/report/income_report', 'ReportController@income_report')->name('report.income_report');
Route::any('/report/customers_report', 'ReportController@customers_report')->name('report.customers_report');
Route::any('/report/customers_report/sales/{id}', 'ReportController@customer_sales')->name('report.customers_report.sales');
Route::any('/report/customers_report/payments/{id}', 'ReportController@customer_payments')->name('report.customers_report.payments');
Route::any('/report/suppliers_report', 'ReportController@suppliers_report')->name('report.suppliers_report');
Route::any('/report/suppliers_report/purchases/{id}', 'ReportController@supplier_purchases')->name('report.suppliers_report.purchases');
Route::any('/report/suppliers_report/payments/{id}', 'ReportController@supplier_payments')->name('report.suppliers_report.payments');
Route::any('/report/users_report', 'ReportController@users_report')->name('report.users_report');
Route::any('/report/users_report/purchases/{id}', 'ReportController@user_purchases')->name('report.users_report.purchases');
Route::any('/report/users_report/sales/{id}', 'ReportController@user_sales')->name('report.users_report.sales');
Route::any('/report/users_report/payments/{id}', 'ReportController@user_payments')->name('report.users_report.payments');


Route::post('/set_pagesize', 'HomeController@set_pagesize')->name('set_pagesize');
