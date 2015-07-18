<?php
session_start();

$editor = (bool) $_SESSION['login'];

if (!$editor) {
  header('HTTP/1.1 403 Unauthorized');
  print 'Доступ заборонено.';
  require('base/footer.php');
  exit;
}

require('base/db.php');

if (isset($_POST['submit'])) {
  try {
    $sql = $conn->prepare('UPDATE content
                          SET title=:title,
                              short_desc=:short_desc,
                              full_desc=:full_desc,
                              timestamp=:timestamp
                          WHERE id=:id
                          ');

    $sql->bindParam(':title', strip_tags($_POST['title']));

    $sql->bindParam(':short_desc', htmlspecialchars($_POST['short_desc']));
    $sql->bindParam(':full_desc', htmlspecialchars($_POST['full_desc']));
    $sql->bindParam(':id', $_POST['id'], PDO::PARAM_INT);

    $date = "{$_POST['date']}  {$_POST['time']}";
    $sql->bindParam(':timestamp', strtotime($date));

    $status = $sql->execute();

    $_SESSION['message'] = "Статтю успішно відредаговано";

  }
  catch(PDOException $e) {
    $_SESSION['message'] = "ERROR: {$e->getMessage()}";
  }
  $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
  header("location: article.php?id={$id}");

  exit;
}

try {
$sql = $conn->prepare('SELECT * FROM content WHERE id = :id');
$sql->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
$sql->execute();

$row = $sql->fetch(PDO::FETCH_ASSOC);

}
catch(PDOException $e) {
  print "ERROR: {$e->getMessage()}";
  require('base/footer.php');
  exit;
}

?>


<form action="<?php print $_SERVER["PHP_SELF"]; ?>" method="POST">

  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
  <div class="field-item">
    <label for="title">Заголовок</label>
    <input type="text" name="title" id="title" required maxlength="255" value="<?php echo $row['title']; ?>">
  </div>

  <div class="field-item">
    <label for="short_desc">Короткий зміст</label>
    <textarea name="short_desc" id="short_desc" required maxlength="600"><?php echo $row['short_desc']; ?></textarea>
  </div>

  <div class="field-item">
    <label for="full_desc">Повний зміст</label>
    <textarea name="full_desc" id="full_desc" required><?php echo $row['full_desc']; ?></textarea>
  </div>

  <div class="field-item">
    <label for="date">День створення</label>
    <input type="date" name="date" id="date" required value="<?php print date('Y-m-d')?>">
    <label for="time">Час створення</label>
    <input type="time" name="time" id="time" required value="<?php print date('G:i');?>">
  </div>

  <input type="submit" name="submit" value="Зберегти">

</form>

