<?php 
header("HTTP/1.1 404 Not Found"); 
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Страница не найдена</title>
    <link rel="stylesheet" href="/~volkov4309/styles.css">
</head>
<body style="background-image: url('backgrounds/background_err.png');">

    <h1>404</h1>
    <h2>Упс! Страница не найдена</h2>
    <div class="button-container">
        <p style="text-align: center; color: #005691; margin-bottom: 20px;">
            Кажется, вы забрели не туда. Вернитесь на главную, чтобы продолжить игру.
        </p>
        <a href="/~volkov4309/" class="button" id="back-to-main">На главную</a>
    </div>

</body>
</html>
