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

// Route::get('getUserDetailsByURL', 'CronController@getUserDetailsByURL');
// Route::get('getUserDetails', 'CronController@getUserDetails');
// Route::get('testmail', 'CronController@testmail');
// Route::get('insertEmails', 'CronController@insertEmails');
// Route::get('insertTemplateMails', 'CronController@insertTemplateMails');
// Route::get('sendRegularEmails', 'CronController@sendRegularEmails');
// Route::get('reportFailedEmails', 'CronController@reportFailedEmails');

// Route::get('generateView', 'CronController@generateView');

// To View Mail Templates
// Route::get('newJoineeDetails', 'CronController@newJoineeDetails');
// Route::get('privacyPolicy', 'CronController@privacyPolicy');
// Route::get('welcomeJoinee', 'CronController@welcomeJoinee');
// Route::get('joineeLinkAdd', 'CronController@joineeLinkAdd');
// Route::get('joineeLinkUpdate', 'CronController@joineeLinkUpdate');
// Route::get('assignLead', 'CronController@assignLead');
// Route::get('empDetailsToTechnicalLead', 'CronController@empDetailsToTechnicalLead');
// Route::get('systemReqToSA', 'CronController@systemReqToSA');
// Route::get('trainingConsultantDetailsToRecruiter', 'CronController@trainingConsultantDetailsToRecruiter');
Route::get('/', function () { 
    // print phpinfo();   
    return view('welcome');
});
Route::get('dailyCron','DailyCronController@dailyMail')->name('dailyCron');
Route::get('sendMail','DailyCronController@sendMail')->name('sendMail');
Route::get('get-user-data','UserDetailsController@index');
