<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\City\ICityRepository;
use Illuminate\Support\Facades\Response;
use App\Models\Sub_city;
use App\Repositories\SubCity\ISubCityRepository; // Updated repository interface

use App\Models\SubCity; // Updated model

class BoleController extends Controller
{
    private $subCityRepository; // Updated variable name
    private $cityRepository; // Updated variable name
    private $title;

    public function __construct(ISubCityRepository $subCityRepository, ICityRepository $cityRepository) // Updated constructor
    {
        $this->cityRepository = $cityRepository;
        $this->subCityRepository = $subCityRepository;
        $this->title = "Virtual Equb - SubCity"; // Updated title
    }
    
    /**
     * Display a listing of SubCities for authorized users.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $user = Auth::user();

           // if ($this->isAuthorized($user)) {
                $subCities = $this->subCityRepository->getAll(); // Updated method
                $title = $this->title;
                $cities =  $this->cityRepository->getAll(); // Updated method
                return view('admin/subCity.subCityList', compact('title', 'subCities','cities')); // Updated view path
         //   }
          //  return Response::json(['error' => 'Unauthorized access.'], 403);
        } catch (\Exception $e) {
            return Response::json(['error' => 'Failed to retrieve SubCities.'], 500);
        }
    }    
    
   /* public function show($id)
    {
        // Retrieve the SubCity by ID
        $subCity = $this->subCityRepository->getAll(); // Adjust this as per your logic
        // Return the data as JSON
        return response()->json($subCity);
    }*/
    public function show($id)
    {
        // Retrieve the equb by ID
      //  $city = $this->cityRepository->getAll(); // Adjust this logic based on your needs

        $subCity = Sub_city::findOrFail($id);
        // Return the data as JSON
        return response()->json($subCity);
    }
    public function getSubCitiesByCityId($cityId)
    {
        // Fetch sub-cities based on the city ID
        $subcities = Sub_city::where('city_id', $cityId)->get();

        // Return the sub-cities as a JSON response
        return response()->json($subcities);
    }
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id', // Assuming there's a city_id field
        ]);

        // Create a new SubCity
        Sub_city::create([
            'name' => $request->name,
            'city_id' => $request->city_id,
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'SubCity added successfully!');
    }
    
    /**
     * Update a SubCity.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'required|boolean', // Assuming it's the field for status
        ]);
    
        // Find the SubCity by ID and update it
        $subCity = Sub_city::findOrFail($id);
        $subCity->name = $validatedData['name'];
        $subCity->active = $validatedData['active'];
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

}