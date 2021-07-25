@extends('layouts.panel')

@section('title','Tipos de servicio')

@section('content')
<div class="card shadow">
    <div class="card-header border-0">
      <div class="row align-items-center">
        <div class="col">
          <h3 class="mb-0">Tipos de Servicio</h3>
        </div>       
      </div>
    </div>
    <div class="card-body">
      @if(session('notification'))
      <div class="alert alert-success" role="alert">
        {{ session('notification') }} 
        <div id="notification"></div>       
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
      <div class="card shadow">
            <div class="card-header">
                <button type="button" 
                    id="btnNuevoTipoServicio"
                    class="btn btn-primary" 
                    data-toggle="modal"                    
                    title="Nuevo Tipo de Servicio">
                    Nuevo Tipo de Servicio
                </button>
            </div>
            <div class="card-body">            
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table id="dtTypeOfServices" class="table align-items-center table-flush data-table">
                        <thead>
                        <tr>
                            <th scope="col">Nombre </th>                        
                            <th scope="col">Accion</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>            
            </div>
      </div>
    </div>      
</div>
<!-- Modal -->
<div class="modal fade" id="modalNewTypeOfService" tabindex="-1" role="dialog" aria-labelledby="modalLabelTypeOfService" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabelTypeOfService">Ingreso de Monto por Cita Atendida</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form method="POST" class="d-inline-block">
          @csrf
          <div class="modal-body">                 
            <div class="form-group">      
              <input type="hidden" name="id" id="id" />  
              <label for="name">Nombre del servicio</label>
              <input type="text" class="form-control" name="name" id="name" required/>
            </div>                         
          </div>
            <div class="modal-footer">
              <button type="submit" id="btnGuardar" class="btn btn-primary">Guardar</button>
              <button type="submit" id="btnActualizar" class="btn btn-primary" style="display: none;">Actualizar</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(function(){

        if ( $.fn.dataTable.isDataTable( '#dtTypeOfServices' ) ) {
            table = $('#dtTypeOfServices').DataTable();
        }
        else {
            table = $('#dtTypeOfServices').DataTable( {
                dom: 'Bfrtip',
                retrieve: true,
                processing: true,
                serverSide: true,
                ajax: "{{ route('type-of-service.getTypeOfServices') }}",
                paging: true,              
                buttons: [
                    'csv', 'excel', 'pdf', 'print', 'reset', 'reload'
                ],  
                columns: [                            
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            } );
        }

        $('#btnNuevoTipoServicio').click(function(){
            $('#modalNewTypeOfService').modal();
        });

       $('#btnGuardar').click(function() {
            name = $('#name').val();

            $.ajax({
                url: "{{ route('type-of-service.store') }}",
                type: 'POST',              
                data: {
                    "_token": "{{ csrf_token() }}",
                    name: name
                }, 
                success: function(response)
                {
                    $('#notification').html(response);
                }
            });
       });       
    });  

    $(document).ready(function(){
        $('.editarTipoServicio').click(function(){
            /* $('#btnGuardar').css('display','none');
            $('#btnActualizar').css('display','block');
            $('#modalNewTypeOfService').modal(); */
            alert("Texto");
        });

         //triggered when modal is about to be shown
        $('#modalNewTypeOfService').on('show.bs.modal', function(e) {            
            //get data-id attribute of the clicked element
            var id = $(e.relatedTarget).data('id');
            var name = $(e.relatedTarget).data('name');
            //populate the textbox
            $(e.currentTarget).find('input[name="id"]').val(id);
            $(e.currentTarget).find('input[name="name"]').val(name);            
        });  
    });
</script>
@endsection