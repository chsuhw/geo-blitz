<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background-image: url('backgrounds/background.png');">

    <div class="question-container">
        <form id="authForm" method="post" action="">
            <div>
                <h3 for="nickname">Имя пользователя:</h3>
                <input type="text" id="nickname" name="nickname" required class="option-field" style="color: black; width: 96%; margin-bottom: 10px;">
            </div>
            <div>
                <h3 for="passwd">Пароль:</h3>
                <input type="password" id="passwd" name="passwd" required class="option-field" style="color: black; width: 96%; margin-bottom: 10px;">
            </div>
            <div class="button-row">
                <button type="submit" class="button" id="authbutton">Войти</button>
                <button type="button" onclick="window.location.href='regform'" class="button" id="regbutton">Зарегистрироваться</button>
            </div>
        </form>
        <?php
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nickname = $_POST['nickname'];
            $passwd = $_POST['passwd'];

            $servername = "localhost:3306";
            $username = "volkov4309";
            $password = "volkov4309";
            $dbname = "volkov4309";

            try {
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Проверка, существует ли пользователь
                $stmt = $conn->prepare("SELECT nickname, passwd FROM users WHERE nickname = :nickname");
                $stmt->bindParam(':nickname', $nickname);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (password_verify($passwd, $user['passwd'])) {
                        $_SESSION['nickname'] = $nickname;
                        echo '<div class="result-message" style="color: #2ecc71;">Авторизация успешна!</div>';
                        echo '<script>setTimeout(function(){ window.location.href = "/~volkov4309/"; }, 2000);</script>';
                    } else {
                        echo '<div class="result-message" style="color: #e74c3c;">Неверный пароль.</div>';
                    }
                } else {
                    echo '<div class="result-message" style="color: #e74c3c;">Пользователь не найден.</div>';
                }
            } catch(PDOException $e) {
                echo '<div class="result-message" style="color: #e74c3c;">Ошибка: ' . $e->getMessage() . '</div>';
            }
            $conn = null;
        }
        ?>
    </div>
</body>
</html>