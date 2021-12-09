<?php

namespace App\Http\Controllers;

use App\Http\Requests\FamilyRequest;
use App\Http\Resources\BaseResource;
use App\Repositories\FamilyRepository;
use Illuminate\Http\Request;

class FamilyController extends Controller
{
    private $familyRepo;

    public function __construct(FamilyRepository $familyRepo)
    {
        $this->familyRepo = $familyRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('family.index');
    }

    public function table(Request $request)
    {
        return response()->json($this->familyRepo->paginate($request));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(FamilyRequest $request)
    {
        $this->familyRepo->create($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FamilyRequest $request)
    {
        $family = $this->familyRepo->create($request);
        return new BaseResource($family);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->familyRepo->find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(FamilyRequest $request, $id)
    {
        $this->familyRepo->update($request, $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(FamilyRequest $request, $id)
    {
        $family = $this->familyRepo->update($request, $id);
        return new BaseResource($family);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->familyRepo->delete($id);
    }
}
