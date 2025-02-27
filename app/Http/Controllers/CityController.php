<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\City\ICityRepository;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use App\Models\City;

class CityController extends Controller
{
    private $cityRepository;
    private $title;

    public function __construct(ICityRepository $cityRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->title = "Virtual Equb - City";
    }
    
    public function index()
    {
        try {
            $user = Auth::user();

                $cities = $this->cityRepository->getAll();
                $title = $this->title;
                $equbTypes = $this->cityRepository->getAll();
                $equbs = $this->cityRepository->getAll();
                $payments = $this->cityRepository->getAll();
                $totalCity = City::count();
                $totalActiveCity =  City::active()->count();
                $totalInactiveCity =  City::inactive()->count();

                return view('admin/city.cityList', compact('title', 'cities', 'totalCity', 'totalActiveCity', 'totalInactiveCity'));            
        } catch (\Exception $e) {
            return Response::json(['error' => 'Failed to retrieve cities.'], 500);
        }
    }    

    public function show($id)
    {
        $city = City::findOrFail($id);

        return response()->json($city);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'required|boolean'
        ]);

        City::create([
            'name' => $request->name,
            'active' => $request->active,
        ]);

        return redirect()->back()->with('success', 'City added successfully!');
    }

    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required',
        ]);

        $city = City::findOrFail($id);
        $city->name = $validatedData['name'];
        $city->active = $validatedData['status'];
        $city->save();

        return response()->json(['message' => 'City updated successfully.']);
    }

    private function isAuthorized($user)
    {
        $allowedRoles = [
            'admin',
            'general_manager',
            'operation_manager',
            'it',
            'finance',
            'customer_service',
            'assistant',
        ];

        return $user && in_array($user->role, $allowedRoles);
    }

    public function destroy($id)
    {
        try {
            $city = City::find($id);

            if (!$city) {
                $msg = "cITY not found!";
                $type = 'error';
                Session::flash($type, $msg);

                return redirect()->back();
            }

            $city->delete();
            $msg = "City deleted successfully!";
            $type = 'success';
            Session::flash($type, $msg);
            return redirect()->back();
        } catch (\Exception $ex) {
            $msg = "Unable to delete the City Data, please try again!";
            $type = 'error';
            Session::flash($type, $msg);
            return $msg;
        }
    }
}