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

Route::group(['middleware' => ['infusionsoft_access', 'auth']], function () {
    Route::get('course/{course}', [
        'as' => 'single.course',
        'uses' => 'CourseController@index'
    ]);

    // Check If course survey is finished
    Route::group(['middleware' => ['survey']], function () {
        Route::get('course/{course}/intro', [
            'as' => 'single.course.starter',
            'uses' => 'CourseController@starter_videos'
        ]);

        Route::get('course/{course}/coaching-calls', [
            'as' => 'single.course.coaching-call',
            'uses' => 'CoachingCallController@index'
        ]);

        Route::get('module/{module}', [
            'as' => 'single.module',
            'uses' => 'ModuleController@index'
        ]);

        Route::get('lesson/{lesson}', [
            'as' => 'single.lesson',
            'uses' => 'LessonController@index'
        ]);

        Route::get('lesson/{lesson}/result', [
            'as' => 'single.lesson.assessment.results',
            'uses' => 'LessonController@assessmentResult'
        ]);
    });

    Route::get('calendar', [
        'as' => 'calendar',
        'uses' => 'EventsController@index'
    ]);

    Route::get('notifications', [
        'as' => 'notifications',
        'uses' => 'UserController@notifications'
    ]);

    Route::post('notifications/mark-as-read', [
        'as' => 'notifications.markAsRead',
        'uses' => 'UserController@notificationsMarkAsRead'
    ]);
});

Route::group(['middleware' => ['onlyajax', 'auth']], function () {
    Route::get('session/{session}', [
        'as' => 'session.show',
        'uses' => 'SessionController@show'
    ]);

    // Mark session as completed
    Route::get('session/{session}/completed', [
        'as' => 'session.completed',
        'uses' => 'SessionController@complete'
    ]);

    Route::post('session/{session}/videoprogress', [
        'as' => 'session.videoprogress',
        'uses' => 'SessionController@videoprogress'
    ]);

    Route::get('course/{course}/coaching-calls/{coachingcall}', [
        'as' => 'coachingcall.show',
        'uses' => 'CoachingCallController@show'
    ]);

    Route::get('course/{course}/coaching-calls/{coachingcall}/completed', [
        'as' => 'coachingcall.completed',
        'uses' => 'CoachingCallController@complete'
    ]);

    Route::get('viewalert/{key}', [
        'as' => 'alert.view',
        'uses' => 'UserController@viewAlert'
    ]);

    Route::get('calendar/course', [
        'as' => 'calendar.filter.course',
        'uses' => 'EventsController@filterCourse'
    ]);

    Route::get('calendar/date', [
        'as' => 'calendar.filter.date',
        'uses' => 'EventsController@filterDate'
    ]);
});

Route::post('lesson/{lesson}/post-to-facebook', [
    'as' => 'lesson.postToFacebook',
    'uses' => 'LessonController@postToFb',
    'middleware' => 'auth'
]);

Route::post('lesson/{lesson}/answer-question', [
    'as' => 'lesson.answerQuestion',
    'uses' => 'LessonController@answerQuestion',
    'middleware' => 'auth'
]);

Route::post('lesson/assessment-check', [
    'as' => 'single.lesson.assessment.check',
    'uses' => 'LessonController@assessmentCheck'
]);

Route::post('lesson/{lesson}/testPopUpHtml', [
    'as' => 'lesson.testPopup',
    'uses' => 'LessonController@testPopup'
]);

Route::post('class-marker/webhook/result', 'LessonController@classMarkerResults');

Route::get('calendar/{event}', [
    'as' => 'event.show',
    'uses' => 'EventsController@show',
    'middleware' => ['auth', 'onlyajax']
]);

Route::get('lesson-question/{question}/viewResultsVideoPopup', [
    'as' => 'single.lesson.viewResultsVideoPopup',
    'uses' => 'LessonController@viewResultsVideoPopup'
]);

/**
 * Survey forms
 */
Route::post('survey/store', [
    'as' => 'survey.store',
    'uses' => 'SurveyController@storeSurvey'
]);

Route::delete('survey/{id}/delete', [
    'as' => 'survey.delete',
    'uses' => 'SurveyController@deleteSurvey'
]);

/**
 * User routes
 */
Route::group(['prefix' => 'user', 'middleware' => 'auth'], function () {
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

    Route::get('billing', [
        'as' => 'user.billing',
        'uses' => 'UserController@billing'
    ]);

    Route::post('billing/changeccard/{invoice_id}', [
        'as' => 'user.billing.changecard',
        'uses' => 'UserController@changeCreditCard',
        'middleware' => 'onlyajax'
    ]);
});

Route::post('user/register', 'UserController@register');

Route::post('user/sync', 'UserController@syncUserTags');

Route::get('user/register/activate/{uuid}', [
    'as' => 'user.activate.show',
    'uses' => 'UserController@activateShow'
]);

Route::post('user/register/activate/{uuid}', [
    'as' => 'user.activate.do',
    'uses' => 'UserController@activateIt'
]);

/*
|--------------------------------------------------------------------------
| Auto login route
|--------------------------------------------------------------------------
*/
Route::get('/auto-login', [
    'uses' => 'UserController@autologin',
    'as' => 'user.autologin'
]);

/*
|--------------------------------------------------------------------------
| Support and Contact pages
|--------------------------------------------------------------------------
*/
Route::get('support', [
    'as' => 'page.support',
    'uses' => 'PageController@support'
]);

Route::get('contact', [
    'as' => 'page.contact',
    'uses' => 'PageController@contact'
]);

/*
|--------------------------------------------------------------------------
| Backpack admin panel routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin', 'middleware' => ['role:Administrator,Editor']], function () {
    CRUD::resource('course', 'Admin\CourseCrudController');
    CRUD::resource('module', 'Admin\ModuleCrudController');
    CRUD::resource('lesson', 'Admin\LessonCrudController');
    CRUD::resource('session', 'Admin\SessionCrudController');
    CRUD::resource('resource', 'Admin\ResourceCrudController');
    CRUD::resource('coachingcall', 'Admin\CoachingCallsCrudController');
    CRUD::resource('training', 'Admin\TrainingCrudController');
    CRUD::resource('event', 'Admin\EventCrudController');
    CRUD::resource('user', 'Admin\UserCrudController');
    CRUD::resource('lessonquestion', 'Admin\LessonQuestionCrudController');

    Route::get('/', [
        'uses' => '\Backpack\Base\app\Http\Controllers\AdminController@redirect'
    ]);

    Route::get('reports', 'Admin\ReportController@index');
    Route::get('graduation-report/fail-test', 'Admin\GraduationReportController@failTest');
    Route::get('graduation-report/finished-course', 'Admin\GraduationReportController@finishedCourse');

    Route::get('dashboard', function () {
        $courses = \App\Models\Course::get();

        $lessonsFinished = getBasicLessonsStats();

        return view('backpack::dashboard', ['courses' => $courses, 'score' => $lessonsFinished]);
    });

    // Settings
    Route::group(['prefix' => 'settings'], function () {
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

    Route::get('user-logins', [
        'uses' => 'Admin\UserLoginsController@index'
    ]);

    Route::get('notify', [
        'uses' => 'Admin\NotifyController@index'
    ]);

    Route::post('notify', [
        'as' => 'notify.send',
        'uses' => 'Admin\NotifyController@notify'
    ]);

    Route::get('survey', [
        'uses' => 'SurveyController@table'
    ]);
});

Auth::routes();
