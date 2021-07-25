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

use Illuminate\Http\Resources\Json\Resource;

Route::get('/', function () {
    //return view('welcome');
    return redirect('/login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth','admin'])->namespace('Admin')->group(function(){
	// Specialty routes
	Route::get('/specialties','SpecialtyController@index');
	Route::get('/specialties/create','SpecialtyController@create'); //form registro
	Route::get('/specialties/{specialty}/edit','SpecialtyController@edit');

	Route::post('/specialties','SpecialtyController@store'); //envío del form
	Route::put('/specialties/{specialty}','SpecialtyController@update');
	Route::delete('/specialties/{specialty}','SpecialtyController@destroy');

	// Doctors
	Route::resource('doctors','DoctorController');

	// Schedule Admin
	Route::get('scheduleAdmin','ScheduleAdminController@index');
	Route::post('scheduleAdmin','ScheduleAdminController@edit');
	Route::post('scheduleAdminStore','ScheduleAdminController@store');

	// Patients
	Route::resource('patients','PatientController');

	// Charts
	Route::get('/charts/appointments/line','ChartController@appointments');
	Route::get('/charts/doctor/column','ChartController@doctors');
	Route::get('/charts/doctor/column/data','ChartController@doctorsJson');

	// FCM
	Route::post('/fcm/send','FirebaseController@sendAll');	
});

Route::middleware(['auth','doctor'])->namespace('Doctor')->group(function(){
	Route::get('/schedule','ScheduleController@edit');
	Route::post('/schedule','ScheduleController@store');	
});

Route::middleware('auth')->group(function(){	
	Route::get('/profile','UserController@edit');
	Route::post('/profile','UserController@update');

	Route::middleware('phone')->group(function(){
		Route::get('/appointments/create','AppointmentController@create'); // <- before
		Route::post('/appointments','AppointmentController@store');
	});

	Route::get('/appointments','AppointmentController@index')->name('appointments.index');
	Route::get('/confirm','AppointmentController@getAppointmentsConfirmed')->name('appointments.confirm');
	Route::get('/history','AppointmentController@getHistoricalAppointments')->name('appointments.history');
	Route::get('/appointments/{appointment}','AppointmentController@show');	// <- after

	Route::get('/appointments/{appointment}/cancel','AppointmentController@showCancelForm');
	Route::post('/appointments/{appointment}/cancel','AppointmentController@postCancel');

	// confirmar cita
	Route::post('/appointments/{appointment}/confirm','AppointmentController@postConfirm');	

	// cita atendida
	Route::post('/appointments/attended','AppointmentController@postAttended');


	Route::get('/patientList','AppointmentController@patientList');

	// myPatients
	Route::get('/myPatients','AppointmentController@myPatients')->name('doctor.patients');

	// events
	Route::get('events', 'EventController@index');
	Route::post('events', 'EventController@index');

	// upload CKEditor
	Route::post('ckeditor/image_upload', 'CKEditorController@upload')->name('upload');

	Route::resource('type-of-service','TypeOfServiceController');

	Route::get('getTypeOfServices','TypeOfServiceController@getTypeOfServices')->name('type-of-service.getTypeOfServices');
});




