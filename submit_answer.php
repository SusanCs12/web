<?php
session_start();
require_once 'db/db.php';

// Verifica si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Obtener los datos del formulario
$question_id = $_POST['question_id'];
$answer = $_POST['answer'];
$user_id = $_SESSION['user_id'];

// Insertar la respuesta en la base de datos
$query = $pdo->prepare("INSERT INTO answers (question_id, user_id, answer) VALUES (:question_id, :user_id, :answer)");
$query->execute([
    'question_id' => $question_id,
    'user_id' => $user_id,
    'answer' => $answer
]);

// Redirigir de vuelta a la página principal (o donde sea necesario)
header('Location: index.php');
exit();
?>
