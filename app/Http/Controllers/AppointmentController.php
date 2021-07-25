<?php

namespace App\Http\Controllers;

use App\User;
use App\Specialty;
use Carbon\Carbon;
use App\Appointment;
use App\CancelledAppointment;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAppointment;
use App\Interfaces\ScheduleServiceInterface;
use Validator;
use DataTables;
use DB;
use Illuminate\Http\JsonResponse;

class AppointmentController extends Controller
{
    private $role;
    
    public function index(Request $request)
    {
        $this->role = auth()->user()->role;

        // admin
        if($this->role == 'admin') 
        {
            $pendingAppointments = Appointment::with('doctor')
            ->with('specialty')
            ->with('cancellation')
            ->with('patient')
            ->where('status','Reservada')
            ->get();

        } elseif($this->role == 'doctor') { //doctor
            
            $pendingAppointments = Appointment::with('doctor')
                ->with('specialty')
                ->with('cancellation')
                ->with('patient')
                ->where('status','Reservada')
                ->where('doctor_id', auth()->id())
                ->get();            

        } elseif ($this->role == 'patient') {
            // patient
            $pendingAppointments = Appointment::with('doctor')
                ->with('specialty')
                ->with('cancellation')
                ->with('patient')
                ->where('status','Reservada')
                ->where('patient_id', auth()->id())
                ->get();
                //->paginate(10);            
        }
        
        /*
        $query = $pendingAppointments

        $response = Datatables::of($query)
            ->with([
                'role' => $this->role
            ])
            ->addIndexColumn()
            ->addColumn('action', function($row){                   

                    $btn = '<a href="javascript:void(0)" class="edit btn btn-primary btn-sm">'. $row->id .'</a>';

                    return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);

            */
        //return $response;

       
        
        if ($request->ajax()) {

            $query = $pendingAppointments;

            return Datatables::of($query)
                ->with([
                    'role' => $this->role
                ])
                ->addIndexColumn()
                ->addColumn('action', function($row){         
                        
                        if($this->role == 'admin' || $this->role == 'doctor') {                            
                            $btn = '<a href="/appointments/'.$row->id.'" class="edit btn btn-primary btn-sm" title="Ver cita"><i class="ni ni-zoom-split-in"></i></a>';
                            $btn.= '<form action="/appointments/'.$row->id.'/confirm" method="POST" class="d-inline-block"> '.csrf_field().'
                                        <button type="submit" class="btn btn-sm btn-success" data-toggle="tooltip" title="Confirmar cita">
                                            <i class="ni ni-check-bold"></i>
                                        </button>
                                    </form> ';
                            $btn .= '<form action="/appointments/'.$row->id.'/cancel" method="POST" class="d-inline-block"> '.csrf_field().'
                                        <button type="submit" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Cancelar cita">
                                            <i class="ni ni-fat-delete"></i>
                                        </button>
                                    </form> ';    
                        } else {
                            $btn = '<a href="/appointments/'.$row->id.'" class="edit btn btn-primary btn-sm">Ver</a>';
                            $btn .= '<form action="/appointments/'.$row->id.'/cancel" method="POST" class="d-inline-block"> '.csrf_field().'
                                        <button type="submit" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Cancelar cita">
                                            <i class="ni ni-fat-delete"></i>
                                        </button>
                                    </form> ';                
                        }
                      
                        return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);

        }    
        
                
        /*
        return view('appointments.index',
            compact('pendingAppointments',
                'confirmedAppointments',
                'oldAppointments',
                'role'
            )
        );*/

        
        $role = $this->role;

        //dd($items);
        return view('appointments.index', compact('items','role','pendingAppointments','confirmedAppointments','oldAppointments'));
        //return view('appointments.index', compact('role'));
    }

    public function getAppointmentsConfirmed(Request $request)
    {
        $this->role = auth()->user()->role;

        // admin
        if($this->role == 'admin') 
        {
            $confirmedAppointments = Appointment::with('doctor')
                ->with('specialty')
                ->with('cancellation')
                ->with('patient')
                ->where('status','Confirmada')
                ->get();           


        } elseif($this->role == 'doctor') { //doctor
                 

            $confirmedAppointments = Appointment::with('doctor')
                ->with('specialty')
                ->with('cancellation')
                ->with('patient')
                ->where('status','Confirmada')
                ->where('doctor_id', auth()->id())
                ->get();                
       

        } elseif ($this->role == 'patient') {
                           
            $confirmedAppointments = Appointment::with('doctor')
                ->with('specialty')
                ->with('cancellation')
                ->with('patient')
                ->where('status','Confirmada')
                ->where('patient_id', auth()->id())
                ->get();
                
        }
       
        
        if ($request->ajax()) {

            $query = $confirmedAppointments;


            return Datatables::of($query)
                ->with([
                    'role' => $this->role
                ])
                ->addIndexColumn()
                ->addColumn('action', function($row){         
                        
                        if($this->role == 'admin' || $this->role == 'doctor') {                            
                            $btn = '<a href="/appointments/'.$row->id.'" class="edit btn btn-primary btn-sm" title="Ver cita"><i class="ni ni-zoom-split-in"></i></a>';
                            /*$btn.= '<form action="/appointments/'.$row->id.'/attended" method="POST" class="d-inline-block"> '.csrf_field().'
                                        <button type="submit" class="btn btn-sm btn-success" data-toggle="tooltip" title="Cita atendida">
                                            Atendida
                                        </button>
                                    </form> ';*/
                            $btn.= '<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#exampleModal" data-appointment-id="'.$row->id.'" title="Cita atendida">
                                        <i class="ni ni-cart"></i>
                                    </button>';
                            $btn .= '<a href="/appointments/'.$row->id.'/cancel" class="edit btn btn-danger btn-sm" title="Cancelar cita"><i class="ni ni-fat-delete"></i></a>';
                        } else {
                            $btn = '<a href="/appointments/'.$row->id.'" class="edit btn btn-primary btn-sm" title="Ver cita"><i class="ni ni-zoom-split-in"></a>';
                        }
                      
                        return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);

        }    
    }

    public function getHistoricalAppointments(Request $request)
    {
        $this->role = auth()->user()->role;

        // admin
        if($this->role == 'admin') 
        {
           $oldAppointments = Appointment::with('doctor')
                ->with('specialty')
                ->with('cancellation')
                ->with('patient')
                ->whereIn('status',['Atendida','Cancelada'])
                ->get();
            

        } elseif($this->role == 'doctor') { //doctor
            $oldAppointments = Appointment::with('doctor')
                ->with('specialty')
                ->with('cancellation')
                ->with('patient')
                ->whereIn('status',['Atendida','Cancelada'])
                ->where('doctor_id', auth()->id())
                ->get();                

        } elseif ($this->role == 'patient') {
            $oldAppointments = Appointment::with('doctor')
                ->with('specialty')
                ->with('cancellation')
                ->with('patient')
                ->whereIn('status',['Atendida','Cancelada'])
                ->where('patient_id', auth()->id())
                ->get();
                
        }
       
        
        if ($request->ajax()) {

            $query = $oldAppointments;

            return Datatables::of($query)
                ->with([
                    'role' => $this->role
                ])
                ->addIndexColumn()
                ->addColumn('action', function($row){         
                    $btn = '<a href="/appointments/'.$row->id.'" class="edit btn btn-primary btn-sm" title="Ver cita"><i class="ni ni-zoom-split-in"></a>';                    
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);

        }
    }

    public function show(Appointment $appointment)
    {
        $role = auth()->user()->role;

        return view('appointments.show', compact('appointment','role'));
    }

    public function create(ScheduleServiceInterface $scheduleService)
    {
    	$specialties = Specialty::all();

        $specialtyId = old('specialty_id');

        if($specialtyId) {
            $specialty = Specialty::find($specialtyId);
            $doctors = $specialty->users;
        } else {
            $doctors = collect();
        }

        
        $date = old('scheduled_date');
        $doctorId = old('doctor_id');
        if($date && $doctorId) {
            $intervals = $scheduleService->getAvailableIntervals($date,$doctorId);     ;
        } else {
            $intervals = null;
        }
        
        
        //dd($doctors);
    	return view('appointments.create', compact('specialties','doctors','intervals'));
    }

    public function store(StoreAppointment $request)
    {
    	$created = Appointment::createForPatient($request, auth()->id());

        if($created) {
            $notification = "La cita se ha registrado correctamente";
        } else {
            $notification = "Ocurrió un problema al registrar la cita médica";
        }    	

    	return redirect('/appointments')->with(compact('notification'));
    }

    public function showCancelForm(Appointment $appointment)
    {
        if ($appointment->status == 'Confirmada') {
            $role = auth()->user()->role;
            return view('appointments.cancel', compact('appointment', 'role'));
        }
        return redirect('/appointments');
    }

    public function postCancel(Appointment $appointment, Request $request)
    {
        if ($request->has('justification')) {
            $cancellation = new CancelledAppointment();
            $cancellation->justification = $request->input('justification');
            $cancellation->cancelled_by_id = auth()->id();
            // $cancellation->appointment_id = ;
            // $cancellation->save();
            $appointment->cancellation()->save($cancellation);
        }
        
        $appointment->status = 'Cancelada';
        $saved = $appointment->save(); // update
        if ($saved)
            $appointment->patient->sendFCM('Su cita ha sido cancelada.');
        
        $notification = 'La cita se ha cancelado correctamente.';
        return redirect('/appointments')->with(compact('notification'));
    }

    public function postConfirm(Appointment $appointment, Request $request)
    {
        $appointment->status = 'Confirmada';
        $saved = $appointment->save();

        if ($saved)       
            $appointment->patient->sendFCM('Su cita se ha confirmado');

        $notification = "La cita se ha confirmada correctamente";

        return redirect('/appointments')->with(compact('notification'));
    }

    public function postAttended(Request $request)
    {
        $appointment_id = $request->appointmentId;
        $amount = $request->appointmentAmount;
        $comment = $request->ckeditor;

        $rules = [
            'appointmentId' => 'required',
            'appointmentAmount' => 'required|numeric|between:0,999.99',
            'ckeditor' => 'required'
        ];

        $this->validate($request, $rules);

        $appointment = Appointment::findOrFail($appointment_id);
        
        $appointment->status = 'Atendida';
        $appointment->amount = $amount;
        $appointment->comment = $comment;
        $saved = $appointment->save();

        if ($saved)       
            $appointment->patient->sendFCM('Su cita ha sido atendida');

        $notification = "La cita ha sido atendida correctamente";

        return redirect('/appointments')->with(compact('notification'));
    }
    
    public function patientList()
    {
        return view('doctors.patients');
    }
    public function myPatients()
    {
        $query = Appointment::select(
                'patient_id',
                'doctor_id',
                DB::raw('max(scheduled_date) as fecha_ultima_visita'),
                DB::raw('max(scheduled_date) as last_date'))
            ->with('patient')
            ->with('doctor')
            ->where('doctor_id', auth()->id())
            ->where('status','Atendida')
            ->groupBy('patient_id','doctor_id')
            ->get();

        return Datatables::of($query)
            ->with([
                'role' => $this->role
            ])            
            ->addIndexColumn()
            ->editColumn('last_date', function($row){
                // Carbon::setLocale('es');
                $dateActual = Carbon::createFromDate($row->last_date);
                $now = Carbon::now();
                return $dateActual->diffForHumans();
            })            
            ->make(true);       
    }
}
