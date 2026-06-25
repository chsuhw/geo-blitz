<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background-image: url('backgrounds/background.png');">

    <div class="question-container">
        <form id="registrationForm" method="post" action="">
            <div>
                <h3 for="nickname">Имя пользователя:</h3>
                <input type="text" id="nickname" name="nickname" required class="option-field" style="color: black; width: 96%; margin-bottom: 10px;">
            </div>
            <div>
                <h3 for="passwd">Пароль:</h3>
                <input type="password" id="passwd" name="passwd" required class="option-field" style="color: black; width: 96%; margin-bottom: 10px;">
            </div>
	    <div class="button-row">
                <button type="submit" class="button" id="regbutton">Зарегистрироваться</button>
	        <button type="button" onclick="window.location.href='auth'" class="button" id="authbutton">Уже зарегистрированы?</button>
	    </div>
        </form>
        <?php
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
                $stmt = $conn->prepare("SELECT nickname FROM users WHERE nickname = :nickname");
                $stmt->bindParam(':nickname', $nickname);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    echo '<div class="result-message" style="color: #e74c3c;">Имя уже занято, попробуйте другое.</div>';
                } else {
                    // Регистрация нового пользователя
                    $hashed_password = password_hash($passwd, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users (nickname, passwd) VALUES (:nickname, :passwd)");
                    $stmt->bindParam(':nickname', $nickname);
                    $stmt->bindParam(':passwd', $hashed_password);
                    $stmt->execute();

                    echo '<div class="result-message" style="color: #2ecc71;">Регистрация успешна!</div>';
                    echo '<script>setTimeout(function(){ window.location.href = "auth"; }, 2000);</script>';
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
