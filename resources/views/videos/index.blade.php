@extends('layouts.app')

@section('content')
<style>
    button {
        margin-top: 3px;
        margin-bottom: 3px;
    }
    .slider-container {
        border: 1px solid #ccc; /* Цвет и стиль рамки */
        padding: 10px; /* Внутренние отступы */
        margin-top: 1px; /* Внешние отступы */
        margin-bottom: 5px; /* Внешние отступы */
        border-radius: 5px; /* Закругление углов рамки (по желанию) */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); /* Легкая тень (по желанию) */
        display: flex;
        align-items: center; /* Центрирование по вертикали */
    }

    .slider {
        flex: 1; /* Занять оставшееся пространство */
        margin-right: 10px; /* Отступ между слайдером и процентами */
    }

    .percentage {
        margin-left: 10px; /* Отступ между процентами и слайдером */
    }

    .control-button {
        margin-left: 10px; /* Отступ между кнопками */
    }
    .btn.btn-primary {
        border: none; /* Убираем обводку */
        box-shadow: none; /* Убираем тень, если она есть */
    }

    .btn.btn-primary:focus {
        outline: none; /* Убираем обводку при фокусе */
        box-shadow: none; /* Убираем тень при фокусе */
    }

    .btn-custom {
        background-color: #100943;
        color: white; /* Чтобы текст кнопки стал белым */
    }
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
                    <h5 class="card-title" data-toggle="tooltip" data-placement="top" title="{{ $video->title }}">
                        {{ Str::length($video->title) > 50 ? Str::limit($video->title, 47) : $video->title }}
                    </h5>
                    <p class="card-text" data-toggle="tooltip" data-placement="top" title="{{ $video->description }}">
                        <span class="badge" style="background-color: #100943; color: white;">video</span>
                    </p>
                    <p class="card-text" data-toggle="tooltip" data-placement="top" title="{{ $video->description }}">
                        {{ Str::length($video->description) > 30 ? Str::limit($video->description, 27) : $video->description }}
                    </p>
                    <div class="slider-container">
                        <button id="start-{{ $loop->index }}" class="control-button">
                            <i class="fas fa-play"></i> <!-- Иконка "Плей" -->
                        </button>
                        <button id="stop-{{ $loop->index }}" class="control-button">
                            <i class="fas fa-stop"></i> <!-- Иконка "Стоп" -->
                        </button>
                        <div id="slider-{{ $loop->index }}" class="slider"></div>
                        <div id="percentage-{{ $loop->index }}" class="percentage">0%</div>
                    </div>
                    <button id="like-{{ $loop->index }}" class="btn likes-count-{{ $loop->index }}">👍 {{ $video->likes_count }}</button>
                    <button id="dislike-{{ $loop->index }}" class="btn dislikes-count-{{ $loop->index }}">👎 {{ $video->dislikes_count }}</button>
<!--                    <div class="likes-count-{{ $loop->index }}">👍 {{ $video->likes_count }}</div>-->
<!--                    <div class="dislikes-count-{{ $loop->index }}">👎 {{ $video->dislikes_count }}</div>-->

                    <!-- Форма для комментариев -->
                    <div class="comment-section">
                        <textarea id="comment-{{ $loop->index }}" placeholder="Введите комментарий" class="form-control mb-2"></textarea>
                        <button id="submit-comment-{{ $loop->index }}" class="btn btn-primary" style="background-color: #100943; color: white;">Оставить комментарий</button>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script>
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
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
                    $(".likes-count-{{ $loop->index }}").text("👍 " + data.likes_count);
                    $(".dislikes-count-{{ $loop->index }}").text("👎 " + data.dislikes_count);
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
                            const currentLikes = parseInt($(".likes-count-{{ $loop->index }}").text().split("👍 ")[1]);
                            $(".likes-count-{{ $loop->index }}").text("👍 " + (currentLikes - 1));
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
                                const currentDislikes = parseInt($(".dislikes-count-{{ $loop->index }}").text().split("👎 ")[1]);
                                $(".dislikes-count-{{ $loop->index }}").text("👎 " + (currentDislikes - 1));
                                $("#dislike-{{ $loop->index }}").removeClass("btn-danger");
                            }
                        });
                    }
                    $.ajax({
                        url: '/api/videos/{{ $video->id }}/like',
                        method: 'POST',
                        success: function(data) {
                            liked = true;
                            const currentLikes = parseInt($(".likes-count-{{ $loop->index }}").text().split("👍 ")[1]);
                            $(".likes-count-{{ $loop->index }}").text("👍 " + (currentLikes + 1));
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
                            const currentDislikes = parseInt($(".dislikes-count-{{ $loop->index }}").text().split("👎 ")[1]);
                            $(".dislikes-count-{{ $loop->index }}").text("👎 " + (currentDislikes - 1));
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
                                const currentLikes = parseInt($(".likes-count-{{ $loop->index }}").text().split("👍 ")[1]);
                                $(".likes-count-{{ $loop->index }}").text("👍 " + (currentLikes - 1));
                                $("#like-{{ $loop->index }}").removeClass("btn-success");
                            }
                        });
                    }
                    $.ajax({
                        url: '/api/videos/{{ $video->id }}/dislike',
                        method: 'POST',
                        success: function(data) {
                            disliked = true;
                            const currentDislikes = parseInt($(".dislikes-count-{{ $loop->index }}").text().split("👎 ")[1]);
                            $(".dislikes-count-{{ $loop->index }}").text("👎 " + (currentDislikes + 1));
                            $(this).addClass("btn-danger");
                        }.bind(this)
                    });
                }
            });

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
