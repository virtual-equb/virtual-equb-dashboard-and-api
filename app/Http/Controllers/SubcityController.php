<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\City\ICityRepository;
use Illuminate\Support\Facades\Response;
use App\Models\Sub_city;
use App\Repositories\SubCity\ISubCityRepository;

class SubCityController extends Controller
{
    private $subCityRepository;
    private $cityRepository;
    private $title;

    public function __construct(ISubCityRepository $subCityRepository, ICityRepository $cityRepository) 
    {
        $this->cityRepository = $cityRepository;
        $this->subCityRepository = $subCityRepository;
        $this->title = "Virtual Equb - SubCity";
    }
    
    public function index()
    {
        try {
            $user = Auth::user();

                $subCities = $this->subCityRepository->getAll();
                $title = $this->title;
                $cities =  $this->cityRepository->getAll();
                $totalSubCity = Sub_city::count();
                $totalActiveSubCity =  Sub_city::active()->count();
                $totalInactiveSubCity =  Sub_city::inactive()->count();

                return view('admin.subCity.subCityList', compact('title', 'subCities','cities', 'totalSubCity','totalActiveSubCity', 'totalInactiveSubCity'));
        } catch (\Exception $e) {
            return Response::json(['error' => 'Failed to retrieve SubCities.'], 500);
        }
    }    
    
    public function show($id)
    {
        $subCity = Sub_city::findOrFail($id);

        return response()->json($subCity);
    }

    public function getSubCitiesByCityId($cityId)
    {
        $subcities = Sub_city::where('city_id', $cityId)->get();

        return response()->json($subcities);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'active' => 'required',
        ]);

        Sub_city::create([
            'name' => $request->name,
            'city_id' => $request->city_id,
            'active' => $request->active,
        ]);

        return redirect()->back()->with('success', 'SubCity added successfully!');
    }
    
    public function update(Request $request, $id) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required',
        ]);
    
        $subCity = Sub_city::findOrFail($id);
        $subCity->name = $validatedData['name'];
        $subCity->active = $validatedData['status'];
        $subCity->save();
    
        return response()->json(['message' => 'SubCity updated successfully.']);
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
        $subCity = Sub_city::findOrFail($id);

        if (!$subCity) {
            return redirect()->back()->with('error', 'SubCity Data not found!');
        }

        if ($subCity['active'] == '1') {
            return redirect()->back()->with('error', 'Unable to delete active subCity data!');
        }

        $subCity->delete();

        return redirect()->back()->with('success', 'SubCity data deleted successfully!');

    }
}