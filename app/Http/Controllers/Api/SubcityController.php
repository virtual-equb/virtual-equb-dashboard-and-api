<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubCity\StoreSubcityRequest;
use App\Http\Requests\SubCity\UpdateSubcityController;
use App\Http\Requests\SubCity\UpdateSubcityRequest;
use App\Models\Sub_city;
use Exception;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\map;

class SubcityController extends Controller
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
            // if ($userData && in_array($userData['role'], ['admin', "equb_collector", "role", "it", 'member'])) {
                $subCity = Sub_city::with('city')->get();

                return response()->json([
                    'data' => $subCity,
                    'code' => 200
                ]);
            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }
        
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
    public function store(StoreSubcityRequest $request)
    {
        $userData = Auth::user();

        try {
            // if ($userData && in_array($userData['role'], ['admin', "equb_collector", "role", "it", 'member'])) {
                $data = $request->validated();
                $subCity = Sub_city::create($data);

                return response()->json([
                    'code' => 200,
                    'data' => $subCity
                ]);

            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }

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
            // if ($userData && in_array($userData['role'], ['admin', "equb_collector", "role", "it", 'member'])) {
                
                $subCity = Sub_city::where('id', $id)->with('city')->first();

                return response()->json([
                    'code' => 200,
                    'data' => $subCity
                ]);

            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }

        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'message' => 'Something went wrong!',
                "error" => $ex->getMessage()
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
        // return 123;
        try {

            // if ($userData && in_array($userData['role'], ['admin', 'member', 'general_manager', 'operation_manager', 'it', 'customer_service', 'assistant'])) {
               
                $subCity = Sub_city::where('id', $id)->with('city')->first();

                $request->validate([
                    'name' => 'required',
                    'created_by' => 'required|exists:users,id',
                    'city_id' => 'required|exists:cities,id',
                    'active' => 'nullable',
                    'remark' => 'nullable',
                    'status' => 'nullable'
                ]);

                $data = [
                    'name' => $request->input('name'),
                    'city_id' => $request->input('city_id'),
                    'remark' => $request->input('remark'),
                    'active' => $request->input('active'),
                    'status' => $request->input('status'),
                    'created_by' => $userData->id
                ];

                $subCity->update($data);

                return response()->json([
                    'data' => $subCity,
                    'code' => 200,
                    'message' => 'The Subcity was successfully updated !'
                ]);
            // } else {
            //     return response()->json([
            //         'code' => 403,
            //         'message' => 'You can\'t perform this action!'
            //     ]);
            // }

        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Something went wrong: ' . $ex->getMessage()
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
