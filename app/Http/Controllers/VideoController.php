<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class VideoController extends Controller
{
    public function index()
    {
        $url = env("MAIN_API_URL", "http://5.35.94.149:5000/receive");

        // Тело запроса, например, массив данных
        $requestBody = [
            'session_id' => \Illuminate\Support\Facades\Session::getId(),
            'video_id' => ['00000859-f2bf-4578-8d90-429d4c0e5c9c'],
            'action' => 'top',
            'info' => [
                'foo' => 80
            ]
        ];

        // Отправка POST-запроса
//        try {
//            $response = Http::post($url, $requestBody);
//        } catch (\Exception $e) {
//            dump($e->getMessage());
//            dd();
//        }
        $response = Http::post($url, $requestBody);

        // Получение тела ответа
        $responseBody = $response->body(); // Вся причина
        // Если вам нужно получить данные как массив
        // $responseData = $response->json();

//        return $responseBody; // Вернуть или обработать ответ, как вам нужно
//        dump(json_decode($responseBody));
        $videos = [];
        $body = json_decode($responseBody);
//        dump($body);
        $values = $body->videos;
        foreach ($values as $value) {
//            dd($value);
            $videos[] = Video::make([
                'category_id' => $value->category_id,
                'description' => $value->description,
                'title' => $value->title,
                'dislikes_count' => $value->v_dislikes,
                'likes_count' => $value->v_likes,
                'video_id' => $value->video_id,
            ]);
        }
//        $videos = Video::inRandomOrder()->get();
        return view('videos.index', compact('videos'));
    }
}
