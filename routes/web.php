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
    // return view('welcome');
    return redirect(route('login'));
});

Auth::routes();

//JADI INI GROUPING ROUTE, SEHINGGA SEMUA ROUTE YANG ADA DIDALAMNYA
//SECARA OTOMATIS AKAN DIAWALI DENGAN administrator
//CONTOH: /administrator/category ATAU /administrator/product, DAN SEBAGAINYA
Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home'); //JADI ROUTING INI SUDAH ADA DARI ARTIKEL SEBELUMNYA TAPI KITA PINDAHKAN KEDALAM GROUPING


    Route::group(['middleware' => ['permission:category-show']], function() {
        Route::resource('category', 'CategoryController')->except(['create', 'show']);
    });

    Route::group(['middleware' => ['permission:product-show']], function() {
        Route::resource('product', 'ProductController'); //BAGIAN INI KITA TAMBAHKAN EXCETP KARENA METHOD SHOW TIDAK DIGUNAKAN
        Route::post('/product/bulk', 'ProductController@massUpload')->name('product.saveBulk');
    });


    //Route yang berada dalam group ini hanya dapat diakses oleh user
    //yang memiliki role admin
    Route::group(['middleware' => ['role:Admin|super-admin']], function () {

        //route yang berada dalam group ini, hanya bisa diakses oleh user
        //yang memiliki permission yang telah disebutkan dibawah
        
        // App setting
        Route::group(['middleware' => ['permission:apps-show']], function() {
            Route::resource('apps', 'AppsController');
        });

        //ROLE
        Route::group(['middleware' => ['permission:role-show']], function() {
            Route::resource('roles', 'RolesController')->except([
                'create', 'show', 'edit', 'update'
            ]);
        });

        // ROLE PERMISSION
        Route::group(['middleware' => ['permission:role permission-show']], function() {
            Route::post('/users/permission', 'UserController@addPermission')->name('users.add_permission');
            Route::get('/users/role-permission', 'UserController@rolePermission')->name('users.roles_permission');
            Route::put('/users/permission/{role}', 'UserController@setRolePermission')->name('users.setRolePermission');
        });

        // USER
        Route::group(['middleware' => ['permission:users-show']], function() {
            Route::resource('users', 'UserController')->except([
                'show'
            ]);
            Route::get('/users/roles/{id}', 'UserController@roles')->name('users.roles');
            Route::put('/users/roles/{id}', 'UserController@setRole')->name('users.set_role');
        });
    });
    // SETTING User
    Route::get('/app-setting{id}', 'UserController@editUser')->name('user_setting');
    Route::put('/app-setting{id}', 'UserController@updateUser')->name('update_user');
});

