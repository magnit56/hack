@extends('layouts.app')

@section('content')
<style>
    .video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .btn-success {
        background-color: green;
        color: white;
    }
    .btn-danger {
        background-color: red;
        color: white;
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
                    <div id="slider-{{ $loop->index }}"></div>
                    <div id="percentage-{{ $loop->index }}">0%</div>
                    <button id="start-{{ $loop->index }}">Старт</button>
                    <button id="stop-{{ $loop->index }}">Стоп</button>
                    <button id="like-{{ $loop->index }}" class="btn">👍 Лайк</button>
                    <button id="dislike-{{ $loop->index }}" class="btn">👎 Дизлайк</button>
                    <div id="likes-count-{{ $loop->index }}">Лайки: {{ $video->likes_count }}</div>
                    <div id="dislikes-count-{{ $loop->index }}">Дизлайки: {{ $video->dislikes_count }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<style>
    .slider {
        margin: 20px;
    }
</style>
<script>
    $(document).ready(function () {
        // Устанавливаем событие для каждого видео
    @foreach($videos as $video)
        (function (index) {
            let interval;
            let percentage = 0;
            const videoElement = $(`video:eq(${index})`)[0]; // Получаем элемент видео по индексу

            // Инициализация ползунка
            $("#slider-{{ $loop->index }}").slider({
                min: 0,
                max: 100,
                slide: function (event, ui) {
                    $("#percentage-{{ $loop->index }}").text(ui.value + "%");
                    percentage = ui.value;
                }
            });

            // Функция остановки
            function stopVideo() {
                clearInterval(interval);
                console.log("Процент при остановке видео:", percentage);
                $("#start-{{ $loop->index }}").prop("disabled", false);
                $("#stop-{{ $loop->index }}").prop("disabled", true);
            }

            // Кнопка "Старт"
            $("#start-{{ $loop->index }}").click(function () {
                $("#start-{{ $loop->index }}").prop("disabled", true);
                $("#stop-{{ $loop->index }}").prop("disabled", false);

                if (percentage === 100) {
                    percentage = 0;
                    $("#slider-{{ $loop->index }}").slider("value", percentage);
                    $("#percentage-{{ $loop->index }}").text(percentage + "%");
                }

                interval = setInterval(function () {
                    if (percentage < 100) {
                        percentage++;
                        $("#slider-{{ $loop->index }}").slider("value", percentage);
                        $("#percentage-{{ $loop->index }}").text(percentage + "%");
                    } else {
                        stopVideo();
                    }
                }, 100);
            });

            // Кнопка "Стоп"
            $("#stop-{{ $loop->index }}").click(stopVideo);

            // Обработчик события окончания видео
            $(videoElement).on('ended', function () {
                stopVideo();
            });

            // Обработка лайков и дизлайков
            $("#like-{{ $loop->index }}").click(function () {
                console.log(`Лайк для видео: ${{{ $video->id }}}`);
                $.ajax({
                    url: '/api/videos/{{ $video->id }}/like', // Замени на свой API
                    method: 'POST',
                    success: function(data) {
                        const currentLikes = parseInt($("#likes-count-{{ $loop->index }}").text().split(": ")[1]);
                        $("#likes-count-{{ $loop->index }}").text("Лайки: " + (currentLikes + 1));
                        $(this).addClass("btn-success"); // Окрасить кнопку в зеленый
                        $("#dislike-{{ $loop->index }}").removeClass("btn-danger"); // Убрать красный цвет с дизлайка
                    }.bind(this) // Привязать контекст
                });
            });

            $("#dislike-{{ $loop->index }}").click(function () {
                console.log(`Дизлайк для видео: ${{{ $video->id }}}`);
                $.ajax({
                    url: '/api/videos/{{ $video->id }}/dislike', // Замени на свой API
                    method: 'POST',
                    success: function(data) {
                        const currentDislikes = parseInt($("#dislikes-count-{{ $loop->index }}").text().split(": ")[1]);
                        $("#dislikes-count-{{ $loop->index }}").text("Дизлайки: " + (currentDislikes + 1));
                        $(this).addClass("btn-danger"); // Окрасить кнопку в красный
                        $("#like-{{ $loop->index }}").removeClass("btn-success"); // Убрать зеленый цвет с лайка
                    }.bind(this) // Привязать контекст
                });
            });

        })({{ $loop->index }});
    @endforeach
    });
</script>
@endsection
