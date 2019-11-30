<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Appointment;
use Illuminate\Console\Command;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enviar mensajes via FCM';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Buscando citas médicas');

        $now = Carbon::now();

        $headers = ['id','scheduled_date','scheduled_time','patient_id'];

        $appointmentsTomorrow = $this->getAppointments24Hours($now->copy());

        foreach ($appointmentsTomorrow as $appointment) {
            $appointment->patient->sendFCM('No olvides tu cita mañana a esta hora');
            $this->info('Mensaje FCM enviado 24h antes, al paciente ' . $appointment->patient_id);
        }

        $this->table($headers, $appointmentsTomorrow);        

        $appointmentsNextHour= $this->getAppointmentsNextHour($now->copy());

        foreach ($appointmentsNextHour as $appointment) {
            $appointment->patient->sendFCM('Tienes una cita en 1 hora. Te esperamos');
            $this->info('Mensaje FCM enviado faltando 1h al paciente ' . $appointment->patient_id);
        }

        $this->table($headers, $appointmentsNextHour);
    }

    private function getAppointments24Hours($now)
    {
        return Appointment::where('status','Confirmada')
            ->where('scheduled_date',$now->addDay()->toDateString())
            ->where('scheduled_time','>=',$now->copy()->subMinutes(3)->toTimeString())
            ->where('scheduled_time','<',$now->copy()->addMinutes(2)->toTimeString())
            ->get(['id','scheduled_date','scheduled_time','patient_id'])->toArray();
    }

    private function getAppointmentsNextHour($now)
    {
        return Appointment::where('status','Confirmada')
            ->where('scheduled_date',$now->addHour()->toDateString())
            ->where('scheduled_time','>=',$now->copy()->subMinutes(3)->toTimeString())
            ->where('scheduled_time','<',$now->copy()->addMinutes(2)->toTimeString())
            ->get(['id','scheduled_date','scheduled_time','patient_id'])->toArray();
    }
}