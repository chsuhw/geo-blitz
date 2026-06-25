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
    <title>Таблица рекордов</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background-image: url('backgrounds/background.png');">
    <div class="question-container">
        <h3>Рекорды всех пользователей</h3>
        <div class="leaderboard-container">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Имя игрока</th>
                        <th>Режим игры</th>
                        <th>Очки</th>
                        <th>Время</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $servername = "localhost:3306";
                    $username = "volkov4309";
                    $password = "volkov4309";
                    $dbname = "volkov4309";

                    try {
                        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        $modes = ['по описанию', 'по очертанию', 'по флагу', 'по валюте'];
                        $topScores = [];

                        foreach ($modes as $mode) {
                            $stmt = $conn->prepare("
                                SELECT nickname, gamemode, bestscore, date
                                FROM playersdata
                                WHERE gamemode = :gamemode
                                ORDER BY bestscore DESC
                                LIMIT 1
                            ");
                            $stmt->bindParam(':gamemode', $mode);
                            $stmt->execute();
                            $topScore = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($topScore) {
                                $topScores[] = $topScore;
                            } else {
                                $topScores[] = [
                                    'nickname' => '—',
                                    'gamemode' => $mode,
                                    'bestscore' => 0,
                                    'date' => '—'
                                ];
                            }
                        }

                        usort($topScores, function($a, $b) {
                            return $b['bestscore'] <=> $a['bestscore'];
                        });

                        foreach ($topScores as $topScore) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($topScore['nickname']) . "</td>";
                            echo "<td>" . htmlspecialchars($topScore['gamemode']) . "</td>";
                            echo "<td>" . htmlspecialchars($topScore['bestscore']) . "</td>";
                            echo "<td>" . htmlspecialchars($topScore['date']) . "</td>";
                            echo "</tr>";
                        }
                    } catch(PDOException $e) {
                        echo "<tr><td colspan='4' class='result-message' style='color: #e74c3c;'>Ошибка: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <h3>Ваши рекорды</h3>
        <div class="leaderboard-container">
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Режим игры</th>
                        <th>Очки</th>
                        <th>Время</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $nickname = $_SESSION['nickname'];
                        $stmt = $conn->prepare("SELECT gamemode, bestscore, date FROM playersdata WHERE nickname = :nickname ORDER BY bestscore DESC");
                        $stmt->bindParam(':nickname', $nickname);
                        $stmt->execute();
                        $userScores = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($userScores) > 0) {
                            foreach ($userScores as $userScore) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($userScore['gamemode']) . "</td>";
                                echo "<td>" . htmlspecialchars($userScore['bestscore']) . "</td>";
                                echo "<td>" . htmlspecialchars($userScore['date']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>У вас пока нет рекордов.</td></tr>";
                        }
                    } catch(PDOException $e) {
                        echo "<tr><td colspan='3' class='result-message' style='color: #e74c3c;'>Ошибка: " . $e->getMessage() . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <button class="button" id="back-to-main" onclick="window.location.href='/~volkov4309/'">
        Вернуться в главное меню
    </button>
</body>
</html>