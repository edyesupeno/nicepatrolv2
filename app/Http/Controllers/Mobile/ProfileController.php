<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load('perusahaan:id,nama_perusahaan');

        return view('mobile.profile.index', compact('user'));
    }
}
