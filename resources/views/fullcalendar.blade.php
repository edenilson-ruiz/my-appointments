
@extends('layouts.panel')

@section('title','Mis citas')


@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.css"/>
@endsection

@section('content')


<div class="card shadow">
    <div class="card-header border-0">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0">Mis agenda</h3>
        </div>       
      </div>
    </div>
    <div class="card-body">
      @if(session('notification'))
      <div class="alert alert-success" role="alert">
        {{ session('notification') }}        
      </div>
      @endif
      @if($errors->any())        
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      @endif
      <div class="nav-wrapper">
          <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
              <li class="nav-item">
                  <a class="nav-link mb-sm-3 mb-md-0 active" 
                    id="tabs-icons-text-1-tab" 
                    data-toggle="tab" 
                    href="#calendar" 
                    role="tab" 
                    aria-controls="tabs-icons-text-1" 
                    aria-selected="true">
                        <i class="ni ni-cloud-upload-96 mr-2"></i>Mi agenda de citas
                </a>
              </li>                   
          </ul>
      </div>
      <div class="card shadow">
          <div class="card-body">              
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="calendar" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    @if($role == 'admin')
                    <div class="row">
                        <div class="col-md-4">                            
                            <form action="{{ url('/events') }}" method="POST" class="form-inline">
                                @csrf
                                <div class="form-group" style="padding: 10px;">
                                    <label></label>
                                    <select id="doctor" name="doctor" class="form-control" onchange="this.form.submit()">
                                        <option value="0">Seleccione un doctor</option>
                                        @foreach ($doctors as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == $oldValue ? 'selected' : '' }} >{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;">Consultar</button>
                                </div>
                            </form>
                        </div>                        
                    </div>
                    @endif                                      
                    <div class="row">                        
                        <div class="col-md-10 col-md-offset-2">
                            @if($calendar=='no_data')
                                <div class="alert alert-info">Seleccione un doctor para consultar su agenda</div>                    
                            @else
                            {!! $calendar->calendar() !!}
                            @endif
                        </div>
                    </div>                    
                </div>                          
              </div>
          </div>
      </div>
    </div>      
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/2.2.7/fullcalendar.min.js"></script>
@if($calendar=='no_data')
@else
    {!! $calendar->script() !!}
@endif
@endsection