<?php

namespace App\Http\Controllers;

use App\Http\Requests\Code\StoreCodeRequest;
use App\Models\CountryCode;
use Exception;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $codes = CountryCode::all();

        return response()->json([
            'data' => $codes
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
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCodeRequest $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'created_by' => 'required',
                'icone' => 'nullable',
            ]);
            $name = $request->input('name');
            $created_by = $request->input('created_by');
            $icone = $request->file('icone');
            
            $code = [
                'name' => $name,
                'created_by' => $created_by,
            ];
            if ($request->file('icone')) {
                $icone = $request->file('icone');
                $imageName = time() . '.' . $icone->getClientOriginalExtension();
                $icone->storeAs('public/code', $imageName);
                $code['icone'] = 'code' . $imageName;
            }
            $create = CountryCode::create($code);

            return response()->json([
                'data' => $create,
                'code' => 200,
                'message' => 'Successfully Created Country Code'
            ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'error' => $ex->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $code = CountryCode::where('id', $id)->first();

            return response()->json([
                'data' => $code,
            ]);
        } catch (Exception $ex) {
           //
        }
        
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
}
