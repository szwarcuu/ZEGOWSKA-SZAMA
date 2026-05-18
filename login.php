<?php
session_start();
require_once 'db.php';

function e($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$errors = [];
$message = '';
$login_or_email = '';

if (isset($_GET['registered'])) {
  $message = 'Konto zostalo utworzone. Mozesz sie zalogowac.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $login_or_email = trim($_POST['login_or_email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($login_or_email === '') {
    $errors[] = 'Podaj login albo e-mail.';
  }

  if ($password === '') {
    $errors[] = 'Podaj haslo.';
  }

  if (!$errors) {
    $stmt = $conn->prepare('SELECT id, login, name, email, password, role FROM users WHERE login = ? OR email = ? LIMIT 1');
    $stmt->bind_param('ss', $login_or_email, $login_or_email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['login'] = $user['login'];
      $_SESSION['name'] = $user['name'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['role'] = $user['role'];

      header('Location: index.php');
      exit;
    }

    $errors[] = 'Nieprawidlowy login/e-mail albo haslo.';
  }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Logowanie - Zegowska Szama</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="style.css" rel="stylesheet"/>
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
        <h5 class="fw-bold mb-4" style="color: #1a3c5e">Logowanie</h5>

        <?php if ($message): ?>
          <div class="alert alert-success"><?= e($message) ?></div>
        <?php endif; ?>

        <?php if ($errors): ?>
          <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
              <div><?= e($error) ?></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <form method="post" action="login.php">
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem">Login albo e-mail</label>
            <input type="text" name="login_or_email" class="form-control" required value="<?= e($login_or_email) ?>"/>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.85rem">Haslo</label>
            <input type="password" name="password" class="form-control" required/>
          </div>

          <button class="btn w-100 text-white fw-semibold" style="background: #1a3c5e; border-radius: 8px;">
            Zaloguj sie
          </button>
        </form>

        <p class="text-center text-muted mt-3 mb-0" style="font-size:.84rem">
          Nie masz konta? <a href="register.php" style="color: #1a3c5e">Zarejestruj sie</a>
        </p>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
