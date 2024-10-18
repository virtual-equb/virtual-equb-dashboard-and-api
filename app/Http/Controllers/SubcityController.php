<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCity\StoreSubcityRequest;
use App\Http\Requests\SubCity\UpdateSubcityController;
use App\Models\Sub_city;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "member" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {
                $subcities = Sub_city::with('city')->get();

                return response()->json([
                    'code' => 200,
                    'data' => $subcities
                ]);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'error' => $ex->getMessage()
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
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "member" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {
                $data = $request->validated();
                $subcity = Sub_city::create($data);

                return response()->json([
                    'code' => 200,
                    'data' => $subcity
                ]);
            } else {
                return response()->json([
                    'code' => 403,
                    'message' => 'You can\'t perform this action!'
                ]);
            }
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
        $userData = Auth::user();

        try {
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "member" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it" || $userData['role'] == "customer_service" || $userData['role'] == "assistant")) {

                $subcity = Sub_city::where('id', $id)->with('city')->get();

                return response()->json([
                    'code' => 200,
                    'data' => $subcity 
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
    public function update(UpdateSubcityController $request, $id)
    {
        $userData = Auth::user();

        try {
            if ($userData && ($userData['role'] == "admin" || $userData['role'] == "general_manager" || $userData['role'] == "operation_manager" || $userData['role'] == "it")) {

                $subcity = Sub_city::where('id', $id)->with('city')->first();
                $data = $request->validated();

                $subcity->update($data);

                return response()->json([
                    'code' => 200,
                    'data' => $subcity
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
