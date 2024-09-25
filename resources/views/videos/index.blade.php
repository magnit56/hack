@extends('layouts.app')

@section('content')
<style>
    .video-container {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%; /* Для соотношения 16:9 */
        height: 0;
        overflow: hidden;
    }

    .video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>
<div class="container">
    <div class="row">
        @foreach($videos as $video)
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" data-toggle="tooltip" data-placement="top" title="{{ $video->title }}">{{ Str::length($video->title) > 50 ? Str::limit($video->title, 47) : $video->title }}</h5>
                    <p class="card-text"><span class="badge bg-primary">video</span></p>
                    <div class="video-container">
                        <video controls>
                            <source src="{{ asset('storage/videos/demo.mp4') }}" type="video/mp4">
                            Ваш браузер не поддерживает HTML5 видео.
                        </video>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection
