<?php

namespace App\Http\Controllers;

use App\User;
use DataTables;
use App\TypeOfService;
use Illuminate\Http\Request;

class TypeOfServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('type_of_services.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->name;

        //dd($name);

        $rules = [
            'name' => 'required|min:5',
        ];

        $this->validate($request, $rules);

        $type_of_service = new TypeOfService();
        $type_of_service->name = $name;


        if($type_of_service->save()){
            $notification = 'El tipo de servicio ha sido guardado';
        } else {
            $notification = 'Hubo un error';
        }

        return response()->json(['success'=>'El servicio se adicionó correctamente']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getTypeOfServices()
    {
        $type_of_services = TypeOfService::all();

        // data-target="#modalNewTypeOfService";

        return Datatables::of($type_of_services)
            ->addIndexColumn()
            ->addColumn('action', function($row){
                $btn = '<a href="/type-of-service/'.$row->id.'" class="edit btn btn-primary btn-sm" title="Ver cita"><i class="ni ni-zoom-split-in"></i></a>';
                $btn.= '<button type="button"
                            class="btn btn-info btn-sm editarTipoServicio"
                            data-toggle="modal"
                            data-id="'.$row->id.'"
                            data-name="'.$row->name.'"
                            title="Editar Tipo de Servicio">
                                <i class="ni ni-collection"></i>
                        </button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
