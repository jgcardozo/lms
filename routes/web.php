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


/**
 * ROUTE FOR TESTING
 */
Route::get('test', [
    'as' => 'test',
    'uses' => 'HomeController@test'
]);

Route::get('test/fb/callback', [
    'as' => 'testcallback',
    'uses' => 'HomeController@callback'
]);

Route::get('test/fb/post', [
    'as' => 'posttofb',
    'uses' => 'HomeController@posttofb'
]);

// Logout route
Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');

/*
|--------------------------------------------------------------------------
| Front-end routes
|--------------------------------------------------------------------------
*/
Route::get('/', [
	'as' => 'home',
	'uses' => 'HomeController@index'
]);

Route::group(['middleware' => ['infusionsoft_access', 'auth']], function() {
	Route::get('course/{course}', [
		'as' => 'single.course',
		'uses' => 'CourseController@index'
	]);

	Route::get('course/{course}/intro', [
		'as' => 'single.course.intro',
		'uses' => 'CourseController@starter_videos'
	]);

	Route::get('module/{module}', [
		'as' => 'single.module',
		'uses' => 'ModuleController@index'
	]);

	Route::get('lesson/{lesson}', [
		'as' => 'single.lesson',
		'uses' => 'LessonController@index'
	]);

	// Mark session as completed
	Route::get('session/{session}/completed', [
		'as' => 'session.completed',
		'uses' => 'SessionController@complete'
	]);
});

/**
 * User routes
 */
Route::group(['prefix' => 'user', 'middleware' => 'auth'], function()
{
    Route::get('profile', [
        'as' => 'user.profile',
        'uses' => 'UserController@profile'
    ]);

    Route::post('profile', [
        'uses' => 'UserController@store'
    ]);

    Route::get('settings', [
        'as' => 'user.settings',
        'uses' => 'UserController@settings'
    ]);

    Route::post('settings', [
        'uses' => 'UserController@settingsStore'
    ]);
});

Route::get('user/register', 'UserController@register');


/**
 * Infusionsoft route
 */
Route::get('is/sync', [
    'uses' => 'InfusionsoftController@sync'
]);

Route::get('is/sign', [
    'uses' => 'InfusionsoftController@signin'
]);

Route::get('is/callback', [
    'uses' => 'InfusionsoftController@callback'
]);

/*
|--------------------------------------------------------------------------
| Auto login route
|--------------------------------------------------------------------------
*/
Route::get('/auto-login', [
	'uses' => 'UserController@autologin'
]);

/*
|--------------------------------------------------------------------------
| Backpack admin panel routes
|--------------------------------------------------------------------------
*/
Route::get('admin/', [
	'uses' => '\Backpack\Base\app\Http\Controllers\AdminController@redirect',
	'middleware' => ['role:Administrator']
]);

Route::get('admin/dashboard', function() {
	return view('backpack::dashboard', ['title' => trans('backpack::base.dashboard')]);
});

Route::group(['prefix' => 'admin', 'middleware' => ['role:Administrator']], function()
{
    CRUD::resource('course', 'Admin\CourseCrudController');
    CRUD::resource('module', 'Admin\ModuleCrudController');
    CRUD::resource('lesson', 'Admin\LessonCrudController');
    CRUD::resource('session', 'Admin\SessionCrudController');
    CRUD::resource('resource', 'Admin\ResourceCrudController');
    CRUD::resource('coachingcall', 'Admin\CoachingCallsCrudController');
    CRUD::resource('event', 'Admin\EventCrudController');

    Route::get('settings', [
		'uses' => 'Admin\SettingsController@index'
	]);

	Route::post('settings', [
		'uses' => 'Admin\SettingsController@save'
	]);
});

Auth::routes();