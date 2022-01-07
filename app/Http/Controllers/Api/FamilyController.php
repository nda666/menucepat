<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\FamilyResource;
use App\Repositories\FamilyRepository;

class FamilyController extends Controller
{
    /**
     * @var FamilyRepository
     */
    protected $familyRepository;

    public function __construct(FamilyRepository $familyRepository)
    {
        $this->familyRepository = $familyRepository;
    }

    public function index()
    {
        $families = $this->familyRepository->findByUserId(auth()->user()->id);

        return FamilyResource::collection($families)->additional([
            'success' => true
        ]);
    }
}
