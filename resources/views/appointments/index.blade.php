@extends('layouts.panel')

@section('title','Mis citas')

@section('styles')
  <style>
    @media screen and (min-width: 768px) {
        .modal-dialog {
          width: 700px; /* New width for default modal */
        }
        .modal-sm {
          width: 350px; /* New width for small modal */
        }
    }
    @media screen and (min-width: 992px) {
        .modal-lg {
          width: 950px; /* New width for large modal */
        }
    }
  </style>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0">Mis citas</h3>
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
                  <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-toggle="tab" href="#confirmed-appointments" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true"><i class="ni ni-cloud-upload-96 mr-2"></i>Mis próximas citas</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-toggle="tab" href="#pending-appointments" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>Citas por confirmar</a>
              </li>  
              <li class="nav-item">
                  <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-toggle="tab" href="#old-appointments" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>Historial de citas</a>
              </li>           
          </ul>
      </div>
      <div class="card shadow">
          <div class="card-body">              
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="confirmed-appointments" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                    @include('appointments.tables.confirmed')
                </div>
                <div class="tab-pane fade" id="pending-appointments" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                    @include('appointments.tables.pending')
                </div> 
                <div class="tab-pane fade" id="old-appointments" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                     @include('appointments.tables.old')
                </div>                
              </div>
          </div>
      </div>
    </div>      
</div>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Ingreso de Monto por Cita Atendida</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="{{ url('/appointments/attended') }}" method="POST" class="d-inline-block">
        @csrf
        <div class="modal-body">        
          <div class="form-group">        
            <label for="appointmentId">Cita #</label>
            <input type="text" class="form-control" name="appointmentId" id="appointmentId" value="" readonly/>
          </div>
          <div class="form-group">        
            <label for="appointmentAmount">Valor de consulta</label>
            <input type="text" class="form-control" name="appointmentAmount" id="appointmentAmount" required/>
          </div>
          <div class="form-group">        
            <label for="appointmentAmount">Ingrese comentarios consulta</label>
            <textarea class="form-control" id="ckeditor" name="ckeditor" required></textarea>
          </div>                   
        </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script type="text/javascript">

  $(function () {

    if (!CKEDITOR.instances['ckeditor']) {
       // CKEDITOR.remove(CKEDITOR.instances['ckeditor']);       
       CKEDITOR.replace( 'ckeditor', {
          filebrowserUploadUrl: "{{route('upload', ['_token' => csrf_token() ])}}",
          filebrowserUploadMethod: 'form'
      });
    }

    if ( $.fn.dataTable.isDataTable( '#dtPendingAppointments' ) ) {
        table = $('#dtPendingAppointments').DataTable();
    }
    else {
        table = $('#dtPendingAppointments').DataTable( {
            retrieve: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('appointments.index') }}",
            paging: true,
            searching: true,
            data: {
              "_token": "{{ csrf_token() }}",  
            },            
            columns: [                
                {data: 'id', name: 'id', orderable: true, searchable: true },
                {data: 'description', name: 'description'},          
                {data: 'specialty.name', name: 'specialty.name'},
                {data: 'doctor.name', name: 'doctor.name'},
                {data: 'patient.name', name: 'patient.name'},
                {data: 'scheduled_date', name: 'scheduled_date'},
                {data: 'scheduled_time_12', name: 'scheduled_time_12'},
                {data: 'type', name: 'type'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        } );
    }

    if ( $.fn.dataTable.isDataTable( '#dtConfirmAppointments' ) ) {
        table = $('#dtConfirmAppointments').DataTable();
    }
    else {
        table = $('#dtConfirmAppointments').DataTable( {
            retrieve: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('appointments.confirm') }}",
            paging: true,
            searching: true,                
            columns: [                
                {data: 'id', name: 'id', orderable: true, searchable: true },
                {data: 'description', name: 'description'},          
                {data: 'specialty.name', name: 'specialty.name'},
                {data: 'doctor.name', name: 'doctor.name'},
                {data: 'patient.name', name: 'patient.name'},
                {data: 'scheduled_date', name: 'scheduled_date'},
                {data: 'scheduled_time_12', name: 'scheduled_time_12'},
                {data: 'type', name: 'type'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        } );
    }

    if ( $.fn.dataTable.isDataTable( '#dtHistoricalAppointments' ) ) {
        table = $('#dtHistoricalAppointments').DataTable();
    }
    else {
        table = $('#dtHistoricalAppointments').DataTable( {
            retrieve: true,
            processing: true,
            serverSide: true,
            ajax: "{{ route('appointments.history') }}",
            paging: true,
            searching: true,          
            columns: [                
                {data: 'id', name: 'id', orderable: true, searchable: true },
                {data: 'description', name: 'description'},          
                {data: 'specialty.name', name: 'specialty.name'},
                {data: 'doctor.name', name: 'doctor.name'},
                {data: 'patient.name', name: 'patient.name'},
                {data: 'scheduled_date', name: 'scheduled_date'},
                {data: 'scheduled_time_12', name: 'scheduled_time_12'},
                {data: 'type', name: 'type'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        } );
    }
    
    //triggered when modal is about to be shown
    $('#exampleModal').on('show.bs.modal', function(e) {
      //get data-id attribute of the clicked element
      var appointmentId = $(e.relatedTarget).data('appointment-id');

      //populate the textbox
      $(e.currentTarget).find('input[name="appointmentId"]').val(appointmentId);
    });  
   
    
  });
</script>
@endsection