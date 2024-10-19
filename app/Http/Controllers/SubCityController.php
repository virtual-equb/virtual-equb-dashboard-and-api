<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\City\ICityRepository;
use App\Repositories\SubCity\ISubCityRepository;
use Exception;

class SubCityController extends Controller
{
    private $cityRepository;
    private $subCityRepository;

    public function __construct(ISubCityRepository $subCityRepository)
    {
        $this->subCityRepository = $subCityRepository;
    }

    /**
     * Get sub-cities by city ID.
     *
     * @param  int  $cityId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSubCitiesByCityId($cityId)
    {
        try {
            $subCities = $this->subCityRepository->getSubCityByCityId($cityId);
            return response()->json($subCities);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve sub-cities.'], 500);
        }
    }
    
}