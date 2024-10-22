<?php

namespace App\Http\Controllers\Api;

use Exception;
use Validator;
use App\Models\CountryCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Code\StoreCodeRequest;

class CountryCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userData = Auth::user();
        try {
            if ($userData && in_array($userData['role'], ['admin', "equb_collector", "operation_manager", "it", "member"])) {
                $codes = CountryCode::with('country')->get();
    
                return response()->json([
                    'data' => $codes,
                    'code' => 200,
                ]);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => $ex->getMessage()
            ]);
        }
        
         
        
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
        $userData = Auth::user();
        try {
            if ($userData && in_array($userData['role'], ['admin', "equb_collector", "operation_manager", "it", "member"])) {
                $data = $request->validated();
                if ($request->hasFile('icon')) {
                    // Get the uploaded icon file
                    $icon = $request->file('icon');
            
                    // Create a unique file name for the icon
                    $imageName = time() . '.' . $icon->getClientOriginalExtension();
            
                    // Store the icon in the 'public/code' directory
                    $icon->storeAs('public/code', $imageName);
            
                    // Add the icon path to the $data array
                    $data['icon'] = 'code/' . $imageName;
                }
                $code = CountryCode::create($data);

                return response()->json([
                    'code' => 200,
                    'data' => $code
                ]);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }

        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => $ex->getMessage()
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
        $userData = Auth::user();

        try {
            if ($userData && in_array($userData['role'], ['admin', "equb_collector", "member", "general_manager", "operatio_manager", "it"])) {
                $code = CountryCode::where('id', $id)->with('country')->first();

                return response()->json([
                    'data' => $code,
                    'code' => 200
                ]);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }

        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Something went wrong: ' . $ex->getMessage()
            ]);
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
        $userData = Auth::user();
        try {
            if ($userData && in_array($userData['role'], ['admin', "equb_collector", "operation_manager", "it", "member"])) {

                $code = CountryCode::where('id', $id)->with('country')->first();

                $request->validate([
                    'name' => 'required',
                    'created_by' => 'required',
                    'active' => 'nullable'
                ]);

                $update = [
                    'name' => $request->input('name'),
                    'created_by' => $request->input('created_by'),
                    'active' => $request->input('active')
                ];
                if ($request->hasFile('icon')) {
                    // Get the uploaded icon file
                    $icon = $request->file('icon');
            
                    // Create a unique file name for the icon
                    $imageName = time() . '.' . $icon->getClientOriginalExtension();
            
                    // Store the icon in the 'public/code' directory
                    $icon->storeAs('public/code', $imageName);
            
                    // Add the icon path to the $data array
                    $update['icon'] = 'code/' . $imageName;
                }

                $code->update($update);
                return response()->json([
                    'code' => 200,
                    'data' => $code
                ]);

            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => $ex->getMessage()
            ]);
        }
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
