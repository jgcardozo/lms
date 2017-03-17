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
		'as' => 'single.course.starter',
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

Route::post('user/register', 'UserController@register');


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
Route::group(['prefix' => 'admin', 'middleware' => ['role:Administrator']], function()
{
    CRUD::resource('course', 'Admin\CourseCrudController');
    CRUD::resource('module', 'Admin\ModuleCrudController');
    CRUD::resource('lesson', 'Admin\LessonCrudController');
    CRUD::resource('session', 'Admin\SessionCrudController');
    CRUD::resource('resource', 'Admin\ResourceCrudController');
    CRUD::resource('coachingcall', 'Admin\CoachingCallsCrudController');
    CRUD::resource('event', 'Admin\EventCrudController');

	Route::get('/', [
		'uses' => '\Backpack\Base\app\Http\Controllers\AdminController@redirect'
	]);

	Route::get('dashboard', function() {
		return view('backpack::dashboard', ['title' => trans('backpack::base.dashboard')]);
	});

	// Settings
	Route::group(['prefix' => 'settings'], function() {
		Route::get('/', [
			'uses' => 'Admin\SettingsController@index'
		]);

		Route::get('is/callback', [
			'uses' => 'Admin\SettingsController@InfusionsoftCallback'
		]);

		Route::post('/', [
			'uses' => 'Admin\SettingsController@save'
		]);
	});

	Route::get('log', [
		'uses' => 'Admin\LoglistController@index'
	]);
});

Auth::routes();