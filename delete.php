<?php
session_start();
require('base/db.php');

$editor = (bool) $_SESSION['login'];

if (!$editor) {
  header('HTTP/1.1 403 Unauthorized');
  print 'Доступ заборонено.';
  require('base/footer.php');
  exit;
}

try {

  $sql = $conn->prepare("DELETE FROM content WHERE id = :id");
  $sql->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
  $sql->execute();

  $_SESSION['message'] = "Статтю успішно видалено";

} catch(PDOException $e) {
  $_SESSION['message'] = "ERROR: {$e->getMessage()}";
}

header("location: index.php");

?>
