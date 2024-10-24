<?php

namespace App\Http\Controllers;

use App\Models\Sub_city;
use Illuminate\Http\Request;
use App\Repositories\SubCity\ISubCityRepository;
use App\Repositories\City\ICityRepository;

class SubCityController extends Controller
{
    private $subCityRepository;
    private $cityRepository;
    private $title;

    public function __construct(ISubCityRepository $subCityRepository,ICityRepository $cityRepository)
    {
        $this->subCityRepository = $subCityRepository;
        $this->cityRepository = $cityRepository;
        $this->title = "Virtual Equb - Sub City";
    }

    /**
     * Display a listing of sub cities for authorized users.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $subCities = $this->subCityRepository->getAll(); // Assuming this method retrieves all sub cities
        $cities = $this->cityRepository->getActiveCity();
        return view('admin.subCity.subCityList', [
            'title' => $this->title,
            'subCities' => $subCities,
            'cities' =>$cities
        ]);
    }
    public function getSubCitiesByCityId($cityId)
    {
    $subCities=     $this->subCityRepository->getSubCityByCityId(1);
        return response()->json($subCities);
    }
    public function show($id)
    {
         // Retrieve the equb by ID
         $city = $this->subCityRepository->getSubCityById($id);
         // Return the data as JSON
         return response()->json($city);
    }
    /**
     * Store a newly created sub city in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id', // Ensure the city exists
            'remark' => 'nullable|string', // If you have a remark field
            'active' => 'boolean', // Optional, depending on your needs
        ]);
    
        // Create a new sub city
        Sub_city::create([
            'name' => $request->name,
            'city_id' => $request->city_id, // Add city_id to the creation
            'active' => $request->active ?? false, // Default to false if not provided
        ]);
    
        // Redirect back with a success message
        return redirect()->back()->with('success', 'Sub City added successfully!');
    }

    /**
     * Check if the user has the required role to access the sub cities.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return bool
     */
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
}