<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\WorkDay;
use Carbon\Carbon;
use App\User;

class ScheduleAdminController extends Controller
{
    private $days = [
        'Lunes','Martes','Miércoles',
        'Jueves','Viernes','Sábado','Domingo'
    ];

    public function index()
    {
        $role = auth()->user()->role;

        $doctors = User::where('role','doctor')->orderBy('name')->get();        
        
        return view('admin.schedule',compact('doctors','role'));

    }

    public function edit(Request $request)
    {
        $role = auth()->user()->role;
        
        $doctor_id = $request->doctor;
        $oldValue = $doctor_id;

        $doctors = User::where('role','doctor')->orderBy('name')->get();
        
        $workDays = WorkDay::where('user_id', $doctor_id)->get();

        if(count($workDays) > 0) {
            $workDays->map(function($workDay){
                $workDay->morning_start = (new Carbon($workDay->morning_start))->format('g:i A');
                $workDay->morning_end = (new Carbon($workDay->morning_end))->format('g:i A');
                $workDay->afternoon_start = (new Carbon($workDay->afternoon_start))->format('g:i A');
                $workDay->afternoon_end = (new Carbon($workDay->afternoon_end))->format('g:i A');
                return $workDay;
            });
        } else {
            $workDays = collect();
            for ($i=0; $i<7; ++$i)
                $workDays->push(new WorkDay());
        }

        //dd($workDays->toArray());
        $days = $this->days;

        return view('admin.schedule',compact('days','workDays','doctors','role','oldValue'));
    }

    public function store(Request $request)
    {
        //dd($request->all());
        $doctor_id = $request->doctorId;
        $active = $request->input('active') ?: [];
        $morning_start = $request->input('morning_start');
        $morning_end = $request->input('morning_end');
        $afternoon_start = $request->input('afternoon_start');
        $afternoon_end = $request->input('afternoon_end');

        $errors = [];

        for ($i = 0; $i < 7; ++$i) {
            if($morning_start[$i] > $morning_end[$i]) {
                $errors [] = "Las horas del turno mañana son inconsistentes para el día " .$days = $this->days[$i] . ".";
            }

            if($afternoon_start[$i] > $afternoon_end[$i]) {
                $errors [] = "Las horas del turno tarde son inconsistentes para el día " .$days = $this->days[$i] . ".";
            }

            WorkDay::updateOrCreate(
                [
                    'day' => $i ,
                    'user_id' => $doctor_id
                ],
                [
                    'active' => in_array($i, $active),
                    'morning_start' => $morning_start[$i],
                    'morning_end' => $morning_end[$i],
                    'afternoon_start' => $afternoon_start[$i],
                    'afternoon_end' => $afternoon_end[$i]
                ]
            );
        }

        if(count($errors) > 0)
            return back()->with(compact('errors'));

        $notification = "Los cambios se han guardado correctamente";
        return back()->with(compact('notification'));
    }
}
