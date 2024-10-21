<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\City\StoreCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Models\Cities;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
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
            if ($userData && in_array($userData['role'], ['admin', 'member', 'general_manager', 'operation_manager', 'it', 'customer_service', 'assistant'])) {

                $cities = Cities::with('cityCountry', 'subCity')->get();

                return response()->json([
                    'code' => 200,
                    'data' => $cities
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
    public function store(StoreCityRequest $request)
    {
        $userData = Auth::user();

        try {
            if ($userData && in_array($userData['role'], ['admin', 'member', 'general_manager', 'operation_manager', 'it', 'customer_service', 'assistant'])) {

                $data = $request->validated();
                $city = Cities::create($data);

                return response()->json([
                    'code' => 200,
                    'data' => $city
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
            if ($userData && in_array($userData['role'], ['admin', 'member', 'general_manager', 'operation_manager', 'it', 'customer_service', 'assistant'])) {
                $city = Cities::where('id', $id)->with('cityCountry', 'subCity')->first();

                return response()->json([
                    'code' => 200,
                    'data' => $city
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
    public function update(UpdateCityRequest $request, $id)
    {
        $userData = Auth::user();

        try {
            if ($userData && in_array($userData['role'], ['admin', 'member', 'general_manager', 'operation_manager', 'it', 'customer_service', 'assistant'])) {
                $city = Cities::where('id', $id)->with('cityCountry', 'subCity')->first();

                $data = $request->validated();
                $city->update($data);

                return response()->json([
                    'code' => 200,
                    'data' => $city
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
