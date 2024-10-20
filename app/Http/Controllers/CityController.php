<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\City\ICityRepository;
use Illuminate\Support\Facades\Response;
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

    /**
     * Display a listing of cities for authorized users.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $user = Auth::user();

            if ($this->isAuthorized($user)) {
                $cities = $this->cityRepository->getAll();
                $title =$this->title;
                $equbTypes = $this->cityRepository->getAll(); // Assuming this method exists
                $equbs = $this->cityRepository->getAll(); // Assuming this method exists
                $payments = $this->cityRepository->getAll(); // Assuming this method exists
                return view('admin/city.cityList', compact('title', 'cities'));            
           }
            return Response::json(['error' => 'Unauthorized access.'], 403);
        } catch (\Exception $e) {
            return Response::json(['error' => 'Failed to retrieve cities.'], 500);
        }
    }    
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Create a new city
        City::create([
            'name' => $request->name,
        ]);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'City added successfully!');
    }
    /**
     * Check if the user has the required role to access the cities.
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
