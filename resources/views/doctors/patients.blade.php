@extends('layouts.panel')

@section('title','Mis pacientes')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0">Mis pacientes</h3>
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
                    href="#my-patients" 
                    role="tab" 
                    aria-controls="tabs-icons-text-1" 
                    aria-selected="true">
                    <i class="ni ni-single-02 mr-2"></i>Mis pacientes
                </a>
              </li>                  
          </ul>
      </div>
      <div class="card shadow">
          <div class="card-body">              
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="cmy-patients" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    @include('doctors.tables.patients')
                </div>                         
              </div>
          </div>
      </div>
    </div>      
</div>
@endsection

@section('scripts')
<script type="text/javascript">

  $(function () {

    $.fn.dataTable.moment('DD-MMM-Y HH:mm:ss');

    if ( $.fn.dataTable.isDataTable( '#dtMyPatients' ) ) {
        table = $('#dtMyPatients').DataTable();   
        new $.fn.dataTable.Buttons( table, {
            buttons: [
                'copy', 'excel', 'pdf'
            ]
        } );     
    }
    else {        
        table = $('#dtMyPatients').DataTable( {            
            retrieve: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('doctor.patients') }}",
            paging: true,
            searching: true,
            data: {
              "_token": "{{ csrf_token() }}",  
            },          
            columns: [                
                {data: 'patient.id', name: 'patient.id'},
                {data: 'patient.name', name: 'patient.name'},          
                {data: 'patient.email', name: 'patient.email'},                
                {data: 'patient.address', name: 'patient.address'},
                {data: 'patient.phone', name: 'patient.phone'},                
                {data: 'fecha_ultima_visita', name: 'fecha_ultima_visita'},
                {data: 'last_date', name: 'last_date'}
            ]
        } );
    }
    
  });

  
</script>
@endsection