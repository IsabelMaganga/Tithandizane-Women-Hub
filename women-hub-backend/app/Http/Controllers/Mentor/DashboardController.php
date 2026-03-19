<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(){

        // Get current admin user info
        $mentorUser = Auth::guard('mentor')->user();
        $mentorName = $mentorUser ? $mentorUser->name : 'mentor';
        $mentorEmail = $mentorUser ? $mentorUser->email : 'mentor@tithandizane.com';


        return view('mentor.dashboard.index', compact(
            'mentorName',
            'mentorEmail'
        ));

    }

}
