<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\StoreAppointment;
use App\Http\Controllers\Controller;
use Auth;
use App\Appointment;

class AppointmentController extends Controller
{
	/*
	"id",
    "description",
    "specialty_id",
    "doctor_id",
    "patient_id",
    "scheduled_date",
    "scheduled_time",
    "type",
    "created_at",
    "updated_at",
    "status",
	*/
    public function index()
    {
    	$user = Auth::guard('api')->user();
    	$appointments = $user->asPatientAppointments()
	    		->with([
	    			'specialty' => function($query) {
	    				$query->select('id','name');
	    			}
	    			,'doctor' => function($query) {
	    				$query->select('id','name');
					}
					,'cancellation' => function($query) {
	    				$query->select('appointment_id','justification','updated_at');
					}
	    		])
	    		->get([
		    		"id",
				    "description",
				    "specialty_id",
				    "doctor_id",		    
				    "scheduled_date",
				    "scheduled_time",
				    "type",
				    "created_at",		    
				    "status"
	    	]);

    	return $appointments;
    }

    public function store(StoreAppointment $request)
    {
    	$patientId = Auth::guard('api')->id();
    	$appointment = Appointment::createForPatient($request, $patientId);
    	if($appointment)
    		$success = true;
    	else
    		$success = false;
    	
    	return compact('success');
    }
}
