<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnnouncementRequest;
use App\Http\Resources\BaseResource;
use App\Repositories\AnnouncementRepository;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    private $announcementRepo;

    public function __construct(AnnouncementRepository $announcementRepo)
    {
        $this->announcementRepo = $announcementRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('announcement.index');
    }

    public function table(Request $request)
    {
        return response()->json($this->announcementRepo->paginate($request));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(AnnouncementRequest $request)
    {
        $this->announcementRepo->create($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnouncementRequest $request)
    {
        $announcement = $this->announcementRepo->create($request);
        return new BaseResource($announcement);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json($this->announcementRepo->find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(AnnouncementRequest $request, $id)
    {
        $this->announcementRepo->update($request, $id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AnnouncementRequest $request, $id)
    {
        $announcement = $this->announcementRepo->update($request, $id);
        return new BaseResource($announcement);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->announcementRepo->delete($id);
    }
}
