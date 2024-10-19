<?php

namespace App\Http\Controllers;

use App\Models\MainEqub;
use App\Repositories\Equb\IEqubRepository;
use App\Repositories\MainEqub\MainEqubRepositoryInterface;
use App\Repositories\Payment\IPaymentRepository;
use Illuminate\Http\Request;

class FrontMainEqubController extends Controller
{
    private $paymentRepository;
    private $equbRepository;
    private $memberRepository;
    private $title;
    private $mainEqubRepository;

    public function __construct(
        IPaymentRepository $paymentRepository,
        IEqubRepository $equbRepository,
        MainEqubRepositoryInterface $mainEqubRepository
    )
    {
        $this->paymentRepository = $paymentRepository;
        $this->equbRepository = $equbRepository;
        $this->mainEqubRepository = $mainEqubRepository;
        $this->title = "Virtual Equb - Dashboard";
    }
    public function index() {
        $Equbs = $this->mainEqubRepository->all();
        $countSubEqubs = MainEqub::withCount('subEqub')->get();
        $mainEqubs = $this->mainEqubRepository->all();
        return view('admin/mainEqub/indexMain', ['equbs' => $Equbs, 'title' => $this->title, 'mainEqubs' => $mainEqubs, 'countSubEqub' => $countSubEqubs]);
    }

    public function show($id) {
        $mainEqub = MainEqub::where('id', $id)->withCount('subEqub')->first();
        $Equb = MainEqub::where('id', $id)->with('subEqub')->first();
        $activeEqubs = MainEqub::where('id', $id)->with(['subEqub' => function ($query) {
            $query->where('status', 'Active');
        }])->count();
        $deactiveEqubs = MainEqub::where('id', $id)->with(['subEqub' => function ($query) {
            $query->where('status', 'Deactive');
        }])->count();
        $mainEqubs = $this->mainEqubRepository->all();
        return view('admin/mainEqub/viewMain', ['activeEqubs' => $activeEqubs, 'deactiveEqubs' => $deactiveEqubs, 'Equb' => $Equb, 'mainEqub' => $mainEqub, 'title' => $this->title, 'mainEqubs' => $mainEqubs]);
    }
}
