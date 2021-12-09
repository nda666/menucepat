<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Http\Resources\BaseResource;
use App\Repositories\LocationRepository;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    private $locationRepo;

    public function __construct(LocationRepository $locationRepo)
    {
        $this->locationRepo = $locationRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('location.index');
    }

    public function table(Request $request)
    {
        return response()->json($this->locationRepo->paginate($request));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(LocationRequest $request)
    {
        $this->locationRepo->create($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationRequest $request)
    {
        $location = $this->locationRepo->create($request);
        return new BaseResource($location);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->locationRepo->find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(LocationRequest $request, $id)
    {
        $this->locationRepo->update($request, $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LocationRequest $request, $id)
    {
        $location = $this->locationRepo->update($request, $id);
        return new BaseResource($location);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->locationRepo->delete($id);
    }
}
