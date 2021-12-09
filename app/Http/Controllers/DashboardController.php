<?php

namespace App\Http\Controllers;

use App\Repositories\BaseRepositoryInterface;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $userRepo;
    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }
    public function index()
    {
        return view('dashboard.index');
    }
}
