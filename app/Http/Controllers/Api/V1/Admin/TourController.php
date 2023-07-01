<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTourRequest;
use App\Http\Resources\TourResource;
use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function store(CreateTourRequest $request, Travel $travel)
    {
       
        $tour = $travel->tours()->create($request->validated());

        return new TourResource($tour);
    }
}
