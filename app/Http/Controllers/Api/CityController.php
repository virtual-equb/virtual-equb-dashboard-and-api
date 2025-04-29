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
    public function __construct() {
        
    }

    public function index()
    {
        // $userData = Auth::user();
        try {
                $cities = Cities::with('cityCountry', 'subCity')->get();

                return response()->json([
                    'code' => 200,
                    'data' => $cities
                ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function create()
    {
        //
    }

    public function store(StoreCityRequest $request)
    {
        $userData = Auth::user();

        try {
                $data = $request->validated();
                $city = Cities::create($data);

                return response()->json([
                    'code' => 200,
                    'data' => $city
                ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        $userData = Auth::user();

        try {
                $city = Cities::where('id', $id)->with('cityCountry', 'subCity')->first();

                return response()->json([
                    'code' => 200,
                    'data' => $city
                ]);
        } catch (Exception $ex) {
            return response()->json([
                'code' => 400,
                'error' => $ex->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(UpdateCityRequest $request, $id)
    {
        $userData = Auth::user();

        try {
                $city = Cities::where('id', $id)->with('cityCountry', 'subCity')->first();

                $data = $request->validated();
                $city->update($data);

                return response()->json([
                    'code' => 200,
                    'data' => $city
                ]);

        } catch (Exception $ex) {
            return response()->json([
                'code' => 500,
                'message' => 'Something went wrong: ' . $ex->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        //
    }
}
