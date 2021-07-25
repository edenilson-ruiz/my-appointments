@extends('layouts.panel')

@section('title','Mis citas')

@section('styles')
<style>
blockquote {
  background: #f9f9f9;
  border-left: 10px solid #ccc;
  margin: 1.5em 10px;
  padding: 0.5em 10px;
  quotes: "\201C""\201D""\2018""\2019";
}
blockquote:before {
  color: #ccc;
  content: open-quote;
  font-size: 4em;
  line-height: 0.1em;
  margin-right: 0.25em;
  vertical-align: -0.4em;
}
blockquote p {
  display: inline;
}
</style>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0">Cita # {{ $appointment->id }}</h3>
        </div>       
      </div>
    </div>
    <div class="card-body">
      <h3>Detalle de la cita</h3>      
      <div class="table-responsive">
        <!-- Projects table -->
        <table class="table align-items-center table-flush">
          <thead class="thead-light">
            <tr>
              <th scope="col">Fecha</th> 
              <td>{{ $appointment->scheduled_date }}</td>
            </tr>
            <tr> 
              <th>Descripción</th>
              <td>{{ $appointment->description }}</td>                 
            </tr>
            <tr> 
              <th>Hora</th>                     
              <td>{{ $appointment->scheduled_time}}</td>                        
            </tr>
            <tr>
              <th>Estado</th>
              <td>
                @if($appointment->status == 'Cancelada')
                  <span class="badge badge-danger">{{ $appointment->status }}</span>
                @else
                  <span class="badge badge-success">{{ $appointment->status }}</span>
                @endif
              </td> 
            </tr>            
            @if($role == 'patient' || $role == 'admin')
            <tr>
              <th>Médico</td>
              <td>{{ $appointment->doctor->name }}</td> 
            </tr>
            @endif
            @if($role == 'doctor' || $role == 'admin')
            <tr>
              <th>Paciente</td>
              <td>{{ $appointment->patient->name }}</td> 
            </tr>
            @endif            
            <tr>
              <th>Tipo de cita</td>
              <td>{{ $appointment->type }}</td> 
            </tr> 
            <tr>
              <th>Especialidad</td>
              <td>{{ $appointment->specialty->name }}</td> 
            </tr>  
          </thead>
          <tbody>                        
          </tbody>
        </table>  

        @if($appointment->status == 'Cancelada')
          <div class="alert alert-default">
            <p>Acerca de la cancelación</p>
            <ul>
              @if($appointment->cancellation)                   
                <li><strong>Motivo de la cancelación:</strong> {{ $appointment->cancellation->justification }}</li> 
                <li><strong>Fecha de la cancelación:</strong> {{ $appointment->cancellation->created_at }}</li>               
                <li><strong>¿Quién canceló la cita?:</strong> 
                  @if(auth()->id() == $appointment->cancellation->cancelled_by_id)
                    Tú
                  @else
                    {{ $appointment->cancellation->cancelled_by->name }}
                  @endif
                </li>               
              @else              
                <li>Observación: Esta cita fue cancelada antes de su confirmación</li>              
              @endif
            </ul>          
          </div>   
        @endif    
      </div>      
    </div>
    <div class="card shadow">
      <div class="card-header">
        <h3>Comentarios cita</h3>      
      </div>
      <div class="card-body">
        <h5 class="card-title">Description:</h5>
        <div class="card-text">
          {!! $appointment->comment !!}
        </div>
        <a href="{{ url('/appointments') }}" class="btn btn-default" style="margin-top: 20px;">Volver</a>
      </div>      
    </div>    
</div>    
@endsection
