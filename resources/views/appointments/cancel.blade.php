@extends('layouts.panel')

@section('title','Mis citas')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0">Cancelar cita</h3>
        </div>       
      </div>
    </div>
    <div class="card-body">
      @if(session('notification'))
      <div class="alert alert-success" role="alert">
        {{ session('notification') }}
      </div>
      @endif

      <p>Estás a punto de cancelar tu cita reservada con el médico {{ $appointment->doctor->name }} en la Especialidad {{ $appointment->specialty->name }} para el día {{ $appointment->scheduled_date }}</p>
      <form action="{{ url('/appointments/'.$appointment->id.'/cancel') }}" method="POST">
        @csrf

        <div class="form-group">
          <label for="justification">Por favor cuéntanos el motivo de la cancelación</label>
          <textarea name="justification" rows="3" class="form-control" required=""></textarea>
        </div>        
        <button class="btn btn-danger" type="submit">Cancelar</button>
        <a href="{{ url('/appointments') }}" class="btn btn-default">
          Volver al listado de citas sin cancelar
        </a>
      </form>

    </div>      
</div>        
@endsection
