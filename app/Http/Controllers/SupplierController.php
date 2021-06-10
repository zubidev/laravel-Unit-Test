<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(file_get_contents(resource_path('data/suppliers.json')),200, [
            'Content-Type' => 'application/json'
        ]);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:suppliers',
        ]);

        if ($validator->fails()) {
            $responseData = [
                'errors'  => $validator->errors(),
            ];

            return response()->json($responseData, Response::HTTP_UNPROCESSABLE_ENTITY);
        }else{

            $aInput=$request->all();
            $aData['name'] = $aInput['name'];
            $aData['info'] = $aInput['info'];
            $aData['rules'] = $aInput['rules'];
            $aData['district'] = $aInput['district'];
            $aData['url'] = $aInput['url'];
            Supplier::insert($aData);
            $responseData = [
                'message'  => 'Supplier added!',
            ];
            return response()->json(['message' => $responseData], Response::HTTP_NO_CONTENT);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Supplier $supplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
