<?php
session_start();
require_once 'includes/db.php';

function e($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$errors = [];
$login = '';
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $login = trim($_POST['login'] ?? '');
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $password_repeat = $_POST['password_repeat'] ?? '';
  $terms = isset($_POST['terms']);

  if ($login === '') {
    $errors[] = 'Podaj login.';
  } elseif (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $login)) {
    $errors[] = 'Login moze miec 3-30 znakow: litery, cyfry i podkreslenie.';
  }

  if ($name === '') {
    $errors[] = 'Podaj imie i nazwisko.';
  }

  if ($email === '') {
    $errors[] = 'Podaj e-mail.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Podaj poprawny e-mail.';
  }

  if (strlen($password) < 8) {
    $errors[] = 'Haslo musi miec minimum 8 znakow.';
  }

  if ($password !== $password_repeat) {
    $errors[] = 'Hasla musza byc takie same.';
  }

  if (!$terms) {
    $errors[] = 'Zaakceptuj regulamin.';
  }

  if (!$errors) {
    $stmt = $conn->prepare('SELECT id FROM users WHERE login = ? OR email = ?');
    $stmt->bind_param('ss', $login, $email);
    $stmt->execute();
    $existing_user = $stmt->get_result()->fetch_assoc();

    if ($existing_user) {
      $errors[] = 'Uzytkownik z takim loginem albo e-mailem juz istnieje.';
    }
  }

  if (!$errors) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';

    $stmt = $conn->prepare('INSERT INTO users (login, name, email, password, role) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('sssss', $login, $name, $email, $password_hash, $role);
    $stmt->execute();

    header('Location: login.php?registered=1');
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Rejestracja - Zegowska Szama</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="css/style.css" rel="stylesheet"/>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">Zegowska Szama</a>
  </div>
</nav>

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-12 col-sm-8 col-md-5">
      <div class="bg-white rounded-3 p-4 border">
        <h5 class="fw-bold mb-4" style="color: #1a3c5e">Rejestracja</h5>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
              <div><?= e($error) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post" action="register.php">
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem">Login</label>
            <input type="text" name="login" class="form-control" required value="<?= e($login) ?>"/>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem">Imie i nazwisko</label>
            <input type="text" name="name" class="form-control" required value="<?= e($name) ?>"/>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem">E-mail</label>
            <input type="email" name="email" class="form-control" required value="<?= e($email) ?>"/>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem">Haslo</label>
            <input type="password" name="password" class="form-control" required placeholder="min. 8 znakow"/>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem">Powtorz haslo</label>
            <input type="password" name="password_repeat" class="form-control" required placeholder="min. 8 znakow"/>
          </div>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="terms" id="terms"/>
            <label class="form-check-label" style="font-size:.84rem" for="terms">
              Akceptuje regulamin sklepiku
            </label>
          </div>

          <button class="btn w-100 text-white fw-semibold" style="background: #e8a020; border: none; border-radius: 8px;">
            Utworz konto
          </button>
        </form>

        <p class="text-center text-muted mt-3 mb-0" style="font-size:.84rem">
          Masz juz konto? <a href="login.php" style="color: #1a3c5e">Zaloguj sie</a>
        </p>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
