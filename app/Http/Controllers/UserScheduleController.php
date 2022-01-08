<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserScheduleRequest;
use App\Http\Resources\BaseResource;
use App\Repositories\UserScheduleRepository;
use Illuminate\Http\Request;

class UserScheduleController extends Controller
{
    private $userScheduleRepo;

    public function __construct(UserScheduleRepository $userScheduleRepo)
    {
        $this->userScheduleRepo = $userScheduleRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user-schedule.index');
    }

    public function table(Request $request)
    {
        return response()->json($this->userScheduleRepo->paginate($request));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(UserScheduleRequest $request)
    {
        $this->userScheduleRepo->create($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserScheduleRequest $request)
    {
        $userSchedule = $this->userScheduleRepo->create($request);
        return new BaseResource($userSchedule);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->userScheduleRepo->find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(UserScheduleRequest $request, $id)
    {
        $this->userScheduleRepo->update($request, $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserScheduleRequest $request, $id)
    {
        $userSchedule = $this->userScheduleRepo->update($request, $id);
        return new BaseResource($userSchedule);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->userScheduleRepo->delete($id);
    }
}
