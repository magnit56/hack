<?php

namespace App\Http\Controllers;

use App\Jobs\RequestJob;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $url = env("MAIN_API_URL", "http://5.35.94.149:5000/receive");
        $video = $request->session()->get('last', '00000859-f2bf-4578-8d90-429d4c0e5c9c');

        // Тело запроса, например, массив данных
        $requestBody = [
            'session_id' => \Illuminate\Support\Facades\Session::getId(),
            'video_id' => [$video],
            'action' => 'top',
            'info' => [
                'foo' => 80
            ]
        ];
        $response = Http::post($url, $requestBody);
        $responseBody = $response->body(); // Вся причина
        $videos = [];
        $body = json_decode($responseBody);
        $values = $body->videos;
        foreach ($values as $value) {
            $videos[] = Video::make([
                'category_id' => $value->category_id,
                'description' => $value->description,
                'title' => $value->title,
                'dislikes_count' => $value->v_dislikes,
                'likes_count' => $value->v_likes,
                'video_id' => $value->video_id,
            ]);
        }

        return view('videos.index', compact('videos'));
    }

    public function action(Request $request, string $video)
    {
        $request->session()->put('last', $video);
        $requestBody = [
            'session_id' => Session::getId(),
            'video_id' => [$video],
            'action' => 'top',
            'info' => [
                'foo' => 80
            ]
        ];
        RequestJob::dispatch($requestBody);
        return true;
    }
}
