<?php
session_start();
if (!isset($_SESSION['nickname'])) {
    header("Location: auth");
    exit();
}

$servername = "localhost:3306";
$username = "volkov4309";
$password = "volkov4309";
$dbname = "volkov4309";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nickname = $_SESSION['nickname'];
    $gamemode = isset($_GET['gamemode']) ? $_GET['gamemode'] : 'по описанию';

    // Получаем текущий результат из поля temp для конкретного режима игры
    $stmt = $conn->prepare("SELECT temp FROM playersdata WHERE nickname = :nickname AND gamemode = :gamemode");
    $stmt->bindParam(':nickname', $nickname);
    $stmt->bindParam(':gamemode', $gamemode);
    $stmt->execute();

    $tempScore = 0;
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $tempScore = $row['temp'];
    }

    // Получаем лучший результат для данного режима игры
    $bestScore = 0;
    $bestScoreStmt = $conn->prepare("SELECT bestscore FROM playersdata WHERE nickname = :nickname AND gamemode = :gamemode");
    $bestScoreStmt->bindParam(':nickname', $nickname);
    $bestScoreStmt->bindParam(':gamemode', $gamemode);
    $bestScoreStmt->execute();

    if ($bestScoreStmt->rowCount() > 0) {
        $bestScoreRow = $bestScoreStmt->fetch(PDO::FETCH_ASSOC);
        $bestScore = $bestScoreRow['bestscore'];
    }

} catch(PDOException $e) {
    file_put_contents('error_log.txt', $e->getMessage(), FILE_APPEND);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background-image: url('backgrounds/background_rest.png');">
    <div class="question-container">
        <h2>Ваш результат</h2>
        <h3>Вы набрали <?php echo $tempScore; ?> очков!</h3>
        <div class="button-row">
            <button class="button" onclick="window.location.href='/~volkov4309/'" id="back-to-main">Вернуться на главный экран</button>
	    <button class="button" onclick="window.location.href='leaderboard'" id="back-to-main">Таблица рекордов</button>
	</div>
    </div>
</body>
</html>