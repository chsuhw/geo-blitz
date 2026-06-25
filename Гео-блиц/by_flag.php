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
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Угадай страну по флагу</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body style="background-image: url('backgrounds/background_flag.png');">
    <div class="header">
        <div class="score">Счёт: <span id="score-value">0</span></div>
        <div class="timer">Время: <span id="timer-value">15</span></div>
    </div>

    <h1></h1>
    <h1></h1>
    <h1></h1>

    <div class="question-container">
        <div class="result-message" id="result-message"></div>
        <div style="text-align: center; margin-bottom: 20px;">
            <img id="flag-image" src="" alt="Флаг" style="max-width: 100%; max-height: 200px; border-radius: 5px;">
        </div>
        <div class="options" id="options-container"></div>
    </div>

    <script>
        // Переменные для игры
        let score = 0;
        let timer;
        let timeLeft = 15;
        let currentQuestion = {};
        let questions = [];
        let usedQuestionsIndices = [];
        const maxQuestions = 25;
        let questionCount = 0;
        const nickname = "<?php echo $_SESSION['nickname']; ?>";
        const gamemode = "по флагу";

        // Функция для загрузки списка флагов и стран
        async function loadFlags() {
            try {
                const response = await fetch('lists/flags_list.txt');
                const text = await response.text();
                const lines = text.split('\n');

                lines.forEach(line => {
                    if (line.trim() !== '') {
                        const parts = line.split('|');
                        if (parts.length === 5) {
                            questions.push({
                                flag: parts[0].trim(),
                                correctAnswer: parts[1].trim(),
                                options: parts.slice(1).map(opt => opt.trim())
                            });
                        }
                    }
                });

                if (questions.length < maxQuestions) {
                    alert(`В файле недостаточно вопросов. Требуется минимум ${maxQuestions} вопросов.`);
                    return;
                }

                nextQuestion();
            } catch (error) {
                alert('Ошибка загрузки файла с флагами: ' + error.message);
            }
        }

        // Функция для выбора случайного вопроса
        function getRandomQuestion() {
            if (usedQuestionsIndices.length >= questions.length) {
                usedQuestionsIndices = [];
            }

            let randomIndex;
            do {
                randomIndex = Math.floor(Math.random() * questions.length);
            } while (usedQuestionsIndices.includes(randomIndex));

            usedQuestionsIndices.push(randomIndex);
            return questions[randomIndex];
        }

        // Функция для запуска таймера
        function startTimer() {
            timeLeft = 15;
            document.getElementById('timer-value').textContent = timeLeft;
            timer = setInterval(() => {
                timeLeft--;
                document.getElementById('timer-value').textContent = timeLeft;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    showResult(false);
                }
            }, 1000);
        }

        // Функция для отображения результата
        function showResult(isCorrect) {
            const resultMessage = document.getElementById('result-message');
            if (isCorrect) {
                const points = Math.floor(timeLeft);
                score += points;
                document.getElementById('score-value').textContent = score;
                resultMessage.textContent = `Правильно! +${points} баллов`;
                resultMessage.style.color = "#2ecc71";
            } else {
                resultMessage.textContent = `Правильный ответ: ${currentQuestion.correctAnswer}`;
                resultMessage.style.color = "#e74c3c";
            }

            setTimeout(nextQuestion, 2000);
        }

        // Функция для генерации вопроса
        function generateQuestion() {
            currentQuestion = getRandomQuestion();
            const flagImage = document.getElementById('flag-image');
            flagImage.src = `flags/${currentQuestion.flag}`;
            document.getElementById('result-message').textContent = '';

            const optionsContainer = document.getElementById('options-container');
            optionsContainer.innerHTML = '';

            const shuffledOptions = [...currentQuestion.options].sort(() => Math.random() - 0.5);

            shuffledOptions.forEach(option => {
                const button = document.createElement('button');
                button.className = 'option-button';
                button.textContent = option;
                button.onclick = () => {
                    clearInterval(timer);
                    if (option === currentQuestion.correctAnswer) {
                        showResult(true);
                    } else {
                        showResult(false);
                    }
                };
                optionsContainer.appendChild(button);
            });
        }

        // Функция для перехода к следующему вопросу
        function nextQuestion() {
            questionCount++;
            if (questionCount <= maxQuestions) {
                generateQuestion();
                startTimer();
            } else {
                saveScore();
            }
        }

        // Функция для сохранения счёта
        function saveScore() {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    window.location.href = `result?gamemode=${encodeURIComponent(gamemode)}`;
                }
            };
            xhr.send(`action=saveScore&score=${score}&gamemode=${encodeURIComponent(gamemode)}`);
        }

        // Запуск игры
        loadFlags();
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'saveScore') {
        $score = isset($_POST['score']) ? intval($_POST['score']) : 0;
        $gamemode = isset($_POST['gamemode']) ? $_POST['gamemode'] : 'по флагу';
        $nickname = $_SESSION['nickname'];
        $currentDate = date('Y-m-d H:i:s');

        try {
            // Проверка существования записи с данным gamemode
            $stmt = $conn->prepare("SELECT * FROM playersdata WHERE nickname = :nickname AND gamemode = :gamemode");
            $stmt->bindParam(':nickname', $nickname);
            $stmt->bindParam(':gamemode', $gamemode);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($score > $row['bestscore']) {
                    // Обновление существующей записи, если новый рекорд
                    $updateStmt = $conn->prepare("UPDATE playersdata SET bestscore = :score, date = :date WHERE nickname = :nickname AND gamemode = :gamemode");
                    $updateStmt->bindParam(':score', $score);
                    $updateStmt->bindParam(':date', $currentDate);
                    $updateStmt->bindParam(':nickname', $nickname);
                    $updateStmt->bindParam(':gamemode', $gamemode);
                    $updateStmt->execute();
                }
            } else {
                // Добавление новой записи, если это первый рекорд для данного режима
                $insertStmt = $conn->prepare("INSERT INTO playersdata (nickname, gamemode, bestscore, date) VALUES (:nickname, :gamemode, :score, :date)");
                $insertStmt->bindParam(':nickname', $nickname);
                $insertStmt->bindParam(':gamemode', $gamemode);
                $insertStmt->bindParam(':score', $score);
                $insertStmt->bindParam(':date', $currentDate);
                $insertStmt->execute();
            }

            // Обновление текущего результата в поле temp
            $updateTempStmt = $conn->prepare("UPDATE playersdata SET temp = :score WHERE nickname = :nickname AND gamemode = :gamemode");
            $updateTempStmt->bindParam(':score', $score);
            $updateTempStmt->bindParam(':nickname', $nickname);
            $updateTempStmt->bindParam(':gamemode', $gamemode);
            $updateTempStmt->execute();

        } catch(PDOException $e) {
            file_put_contents('error_log.txt', $e->getMessage(), FILE_APPEND);
        }
    }
    ?>
</body>
</html>