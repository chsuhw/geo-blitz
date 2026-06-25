<?php
session_start();
if (!isset($_SESSION['nickname'])) {
    header("Location: auth");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Гео-блиц</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background-image: url('backgrounds/background.png');">
    <?php
    $userName = $_SESSION['nickname'];?>
    <h1></h1>
    <h1></h1>
    <h1></h1>
    <h1></h1>
    <h1></h1>
    <h3></h3>
    <div class="button-container">
        <a href="by_description" class="button" id="by-description">по описанию</a>
        <a href="by_outline" class="button" id="by-outline">по очертанию</a>
        <a href="by_flag" class="button" id="by-flag">по флагу</a>
        <a href="by_currency" class="button" id="by-currency">по валюте</a>
        <a href="leaderboard" class="button" id="leaderboard">таблица рекордов</a>
    </div>
</body>
</html>

