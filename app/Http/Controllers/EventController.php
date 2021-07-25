<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon\Carbon;
use App\Appointment;
use Calendar;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->user()->role;

        if ($role == 'admin')
        {
            $doctors = User::where('role','doctor')->orderBy('name')->get();

            if (count($request->all())) {
                $doctor_id = $request->doctor;
                $oldValue = $doctor_id;
            
                $events = [];
            
                $data = Appointment::where('status','Confirmada')
                    ->where('doctor_id',$doctor_id)
                    ->get();
                
                if($data->count()) {
                    foreach ($data as $key => $value) {
                        
                        $events[] = Calendar::event(
                            $value->description,
                            false,
                            new \DateTime($value->scheduled_date." ".$value->scheduled_time),
                            new \DateTime($value->scheduled_date." ".$value->scheduled_time. ' +30 minutes'),
                            null,
                            // Add color and link on event
                            [
                                'color' => '#f05050',
                                'url' => '/appointments/'.$value->id,
                            ]
                        );
                    }
                }
                $calendar = Calendar::addEvents($events);
            } else {
                $calendar = 'no_data';
                $oldValue = '';
            }
        } else {
                $doctor_id = auth()->user()->id;
                $oldValue = $doctor_id;
            
                $events = [];
            
                $data = Appointment::where('status','Confirmada')
                    ->where('doctor_id',$doctor_id)
                    ->get();
                
                if($data->count()) {
                    foreach ($data as $key => $value) {
                        
                        $events[] = Calendar::event(
                            $value->description,
                            false,
                            new \DateTime($value->scheduled_date." ".$value->scheduled_time),
                            new \DateTime($value->scheduled_date." ".$value->scheduled_time. ' +30 minutes'),
                            null,
                            // Add color and link on event
                            [
                                'color' => '#f05050',
                                'url' => '/appointments/'.$value->id,
                            ]
                        );
                    }
                }
                $calendar = Calendar::addEvents($events);
        }
        
        return view('fullcalendar', compact('calendar','doctors','oldValue','role'));
    }
}
