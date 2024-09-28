@extends('layouts.app')

@section('content')
<style>
    .highlight {
        border-color: green;
        background-color: #ccffcc; /* Салатовый фон */
    }
    /* Добавьте класс для обычного состояния */
    .comment-input {
        border-color: #ced4da; /* Стандартный цвет границы */
    }
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
    .slider {
        margin: 20px;
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

                    <!-- Форма для комментариев -->
                    <div class="comment-section">
                        <textarea id="comment-{{ $loop->index }}" placeholder="Введите комментарий" class="form-control mb-2"></textarea>
                        <button id="submit-comment-{{ $loop->index }}" class="btn btn-primary">Оставить комментарий</button>
                        <div id="comment-success-message-{{ $loop->index }}" class="text-success mt-2" style="display:none;">Комментарий оставлен!</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<!-- Модальное окно для сообщений -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">Успех</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Комментарий оставлен!
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    $(document).ready(function () {
    @foreach($videos as $video)
        (function (index) {
            let interval;
            let percentage = 0;
            const videoElement = $(`video:eq(${index})`)[0]; // Получаем элемент видео по индексу
            let liked = false;
            let disliked = false;

            // Инициализация ползунка
            $("#slider-{{ $loop->index }}").slider({
                min: 0,
                max: 100,
                slide: function (event, ui) {
                    $("#percentage-{{ $loop->index }}").text(ui.value + "%");
                    percentage = ui.value;
                }
            });

            // Запрос начального количества лайков и дизлайков
            $.ajax({
                url: '/api/videos/{{ $video->id }}/counts',
                method: 'GET',
                success: function(data) {
                    $("#likes-count-{{ $loop->index }}").text("Лайки: " + data.likes_count);
                    $("#dislikes-count-{{ $loop->index }}").text("Дизлайки: " + data.dislikes_count);
                }
            });

            // Функция остановки
            function stopVideo() {
                clearInterval(interval);
                $("#start-{{ $loop->index }}").prop("disabled", false);
                $("#stop-{{ $loop->index }}").prop("disabled", true);

                // Отправка информации о просмотренном проценте на сервер
                $.ajax({
                    url: '/api/videos/{{ $video->id }}/viewed',
                    method: 'POST',
                    data: { percentage: percentage },
                    success: function(response) {
                        console.log('Процент просмотра успешно отправлен:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Ошибка при отправке процента просмотра:', error);
                    }
                });
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
                if (liked) {
                    // Отмена лайка
                    $.ajax({
                        url: '/api/videos/{{ $video->id }}/unlike',
                        method: 'POST',
                        success: function() {
                            liked = false;
                            const currentLikes = parseInt($("#likes-count-{{ $loop->index }}").text().split(": ")[1]);
                            $("#likes-count-{{ $loop->index }}").text("Лайки: " + (currentLikes - 1));
                            $(this).removeClass("btn-success");
                        }.bind(this)
                    });
                } else {
                    if (disliked) {
                        $.ajax({
                            url: '/api/videos/{{ $video->id }}/undislike',
                            method: 'POST',
                            success: function() {
                                disliked = false;
                                const currentDislikes = parseInt($("#dislikes-count-{{ $loop->index }}").text().split(": ")[1]);
                                $("#dislikes-count-{{ $loop->index }}").text("Дизлайки: " + (currentDislikes - 1));
                                $("#dislike-{{ $loop->index }}").removeClass("btn-danger");
                            }
                        });
                    }
                    $.ajax({
                        url: '/api/videos/{{ $video->id }}/like',
                        method: 'POST',
                        success: function(data) {
                            liked = true;
                            const currentLikes = parseInt($("#likes-count-{{ $loop->index }}").text().split(": ")[1]);
                            $("#likes-count-{{ $loop->index }}").text("Лайки: " + (currentLikes + 1));
                            $(this).addClass("btn-success");
                        }.bind(this)
                    });
                }
            });

            $("#dislike-{{ $loop->index }}").click(function () {
                if (disliked) {
                    $.ajax({
                        url: '/api/videos/{{ $video->id }}/undislike',
                        method: 'POST',
                        success: function() {
                            disliked = false;
                            const currentDislikes = parseInt($("#dislikes-count-{{ $loop->index }}").text().split(": ")[1]);
                            $("#dislikes-count-{{ $loop->index }}").text("Дизлайки: " + (currentDislikes - 1));
                            $(this).removeClass("btn-danger");
                        }.bind(this)
                    });
                } else {
                    if (liked) {
                        $.ajax({
                            url: '/api/videos/{{ $video->id }}/unlike',
                            method: 'POST',
                            success: function() {
                                liked = false;
                                const currentLikes = parseInt($("#likes-count-{{ $loop->index }}").text().split(": ")[1]);
                                $("#likes-count-{{ $loop->index }}").text("Лайки: " + (currentLikes - 1));
                                $("#like-{{ $loop->index }}").removeClass("btn-success");
                            }
                        });
                    }
                    $.ajax({
                        url: '/api/videos/{{ $video->id }}/dislike',
                        method: 'POST',
                        success: function(data) {
                            disliked = true;
                            const currentDislikes = parseInt($("#dislikes-count-{{ $loop->index }}").text().split(": ")[1]);
                            $("#dislikes-count-{{ $loop->index }}").text("Дизлайки: " + (currentDislikes + 1));
                            $(this).addClass("btn-danger");
                        }.bind(this)
                    });
                }
            });

            // Обработка комментариев
// Обработка комментариев
            $("#submit-comment-{{ $loop->index }}").click(function () {
                const comment = $("#comment-{{ $loop->index }}").val();
                if (comment) {
                    $.ajax({
                        url: '/api/videos/{{ $video->id }}/comment', // Замени на свой API для отправки комментария
                        method: 'POST',
                        data: { comment: comment },
                        success: function() {
                            $("#comment-{{ $loop->index }}").addClass('highlight').removeClass('comment-input'); // Подсвечиваем поле ввода
                            $("#comment-{{ $loop->index }}").val(''); // Очистить поле ввода
                            setTimeout(function() {
                                $("#comment-{{ $loop->index }}").removeClass('highlight').addClass('comment-input'); // Убираем подсветку через 2 секунды
                            }, 2000); // Задержка в 2000 мс
                        },
                        error: function(xhr, status, error) {
                            console.error('Ошибка при отправке комментария:', error);
                        }
                    });
                } else {
                    alert("Пожалуйста, введите комментарий.");
                }
            });
        })({{ $loop->index }});
    @endforeach
    });
</script>
@endsection
