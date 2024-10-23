<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Country\StoreCountryRequest;

use function PHPSTORM_META\map;

class CountryController extends Controller
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
            if ($userData && in_array($userData['role'], ['admin', "equb_collector", "role", "it", "member"])) {
                $countries = Country::with('countryCode')->get();
    
                return response()->json([
                    'data' => $countries,
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
    public function store(StoreCountryRequest $request)
    {
        $userData = Auth::user();
        try {
            if ($userData && in_array($userData['role'], ['admin', "equb_collector", "role", "it", "member"])) {
                $data = $request->validated();
                $country = Country::create($data);

                return response()->json([
                    'code' => 200,
                    'message' => 'Successfully Created Country',
                    'data' => $country
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
            if ($userData && in_array($userData['role'], ['admin', "equb_collector", "role", "it", "member"])) {
                $country = Country::where('id', $id)->with('countryCode')->first();

                return response()->json([
                    'data' => $country,
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

            if ($userData && in_array($userData['role'], ['admin', "operation_manager", "general_manager", "it", "member"])) {
                $country = Country::where('id', $id)->with('countryCode')->first();

                $request->validate([
                    'name' => 'required',
                    'code' => 'required',
                    'active' => 'nullable',
                    'remark' => 'nullable',
                    'created_by' => 'required',
                    'status' => 'nullable'
                ]);

                $update = [
                    'name' => $request->input('name'),
                    'code' => $request->input('code'),
                    'remark' => $request->input('remark'),
                    'created_by' => $request->input('created_by'),
                    'status' => $request->input('status'),
                    'active' => $request->input('active')
                ];

                $country->update($update);
                return response()->json([
                    'code' => 200,
                    'data' => $country,
                    'message' => 'The Country was successfully updated'
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
