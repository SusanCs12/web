<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require 'db/db.php';

// Insertar una nueva pregunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
    $question = $_POST['question'];
    $stmt = $pdo->prepare('INSERT INTO questions (user_id, question) VALUES (?, ?)');
    $stmt->execute([$_SESSION['user_id'], $question]);
}

// Insertar una respuesta a una pregunta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'], $_POST['question_id'])) {
    $answer = $_POST['answer'];
    $question_id = $_POST['question_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare('INSERT INTO answers (question_id, user_id, answer) VALUES (?, ?, ?)');
    $stmt->execute([$question_id, $user_id, $answer]);
}

// Obtener las preguntas y sus respuestas
$stmt = $pdo->query('
    SELECT q.id as question_id, q.question, u.username 
    FROM questions q 
    JOIN users u ON q.user_id = u.id 
    ORDER BY q.created_at DESC
');
$questions = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/styles.css">
    <title>Página Principal</title>
</head>
<body>
    <h1>Página Principal</h1>

    <!-- Formulario para publicar una nueva pregunta -->
    <form method="POST">
        <textarea name="question" placeholder="Escribe tu pregunta aquí..." required></textarea>
        <button type="submit">Publicar</button>
    </form>

    <!-- Mostrar preguntas con sus respuestas -->
    <ul>
        <?php foreach ($questions as $q): ?>
            <li>
                <strong><?= htmlspecialchars($q['username']) ?>:</strong> <?= htmlspecialchars($q['question']) ?>

                <!-- Formulario para responder a la pregunta -->
                <form method="POST">
                    <textarea name="answer" placeholder="Escribe tu respuesta aquí..." required></textarea>
                    <input type="hidden" name="question_id" value="<?= $q['question_id'] ?>">
                    <button type="submit">Responder</button>
                </form>

                <!-- Mostrar respuestas a la pregunta -->
                <?php
                $answersQuery = $pdo->prepare('SELECT a.answer, u.username FROM answers a JOIN users u ON a.user_id = u.id WHERE a.question_id = ? ORDER BY a.created_at DESC');
                $answersQuery->execute([$q['question_id']]);
                $answers = $answersQuery->fetchAll();

                foreach ($answers as $answer): ?>
                    <div>
                        <strong><?= htmlspecialchars($answer['username']) ?>:</strong> <?= htmlspecialchars($answer['answer']) ?>
                    </div>
                <?php endforeach; ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="logout.php">Cerrar Sesión</a>
</body>
</html>
