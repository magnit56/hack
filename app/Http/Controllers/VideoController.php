<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::inRandomOrder()->get();
        return view('videos.index', compact('videos'));
    }
}
