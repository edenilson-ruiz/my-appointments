@extends('layouts.panel')

@section('title','Home')

@section('content')
<div class="row">
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">Dashboard</div>
            <div class="card-body">
                @if(session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                Bienvenido! | Por favor seleccione una opción del menú lateral izquierdo
            </div>
        </div>
    </div>

    @if(auth()->user()->role == 'admin')
      <div class="col-xl-6 mb-5 mb-xl-0">
        <div class="card shadow">
          <div class="card-header bg-transparent">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="text-uppercase ls-1 mb-1">Notificación general</h6>
                <h2 class="mb-0">Enviar a todos los usuarios</h2>
              </div>            
            </div>
          </div>
          <div class="card-body">
            @if(session('notification'))
            <div class="alert alert-success" role="alert">
              {{ session('notification') }}
            </div>
            @endif
            <form action="{{ url('/fcm/send') }}" method="post">
              @csrf 
              <div class="form-group">
                <label for="title">Título</label>
                <input value="{{ config('app.name') }}" type="text" name="title" id="title" class="form-control" required="">
              </div>
               <div class="form-group">
                <label for="body">Mensaje</label>
                <textarea name="body" id="body" class="form-control" rows="2" required=""></textarea>
              </div>
              <button class="btn btn-primary">Enviar notificación</button>
            </form>          
          </div>
        </div>
      </div>
      <div class="col-xl-6">
        <div class="card shadow">
          <div class="card-header bg-transparent">
            <div class="row align-items-center">
              <div class="col">
                <h6 class="text-uppercase text-muted ls-1 mb-1">Total de citas</h6>
                <h2 class="mb-0">Según día de la semana</h2>
              </div>
            </div>
          </div>
          <div class="card-body">
            <!-- Chart -->
            <div class="chart">
              <canvas id="chart-orders" class="chart-canvas"></canvas>
            </div>
          </div>
        </div>
      </div>
    @endif
</div>    
@endsection

@section('scripts')
  <script type="text/javascript">
    const appointmentsByDay = @json($appointmentsByDay)
  </script>
  <script type="text/javascript" src="{{ asset('js/charts/home.js')}}"></script>
@endsection
