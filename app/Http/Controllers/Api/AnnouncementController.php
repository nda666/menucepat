<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Repositories\AnnouncementRepository;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * @var AnnouncementRepository
     */
    protected $announcementRepository;

    public function __construct(AnnouncementRepository $announcementRepository)
    {
        $this->announcementRepository = $announcementRepository;
    }

    public function index(Request $request)
    {
        $annoucements = $this->announcementRepository->findFilter($request);
        return new BaseResource($annoucements);
    }
}
