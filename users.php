<?php
require_once 'auth.php';
require_admin();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = (int)$_POST['delete_user_id'];

    if ($delete_user_id !== (int)$_SESSION['user_id']) {
        $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $delete_user_id);
        $stmt->execute();
    }

    header('Location: users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $user_id = (int)$_POST['user_id'];
    $role = $_POST['role'];

    if (in_array($role, ['user', 'admin'], true)) {
        $stmt = $conn->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->bind_param('si', $role, $user_id);
        $stmt->execute();
    }

    header('Location: users.php');
    exit;
}

function e($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$users = $conn->query('SELECT id, login, name, email, role, created_at FROM users ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Uzytkownicy - Zegowska Szama</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="style.css" rel="stylesheet"/>
</head>
<body>
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin.php">Zegowska Szama - Admin</a>
    <div class="d-flex align-items-center gap-3">
      <span class="text-white-50" style="font-size:.85rem">Zalogowany: <?= e($_SESSION['name']) ?></span>
      <a href="index.php" class="btn btn-sm btn-outline-light">Wroc do sklepu</a>
      <a href="logout.php" class="btn btn-sm btn-warning">Wyloguj</a>
    </div>
  </div>
</nav>

<div class="container-fluid mt-4 mb-5">
  <div class="row g-4">
    <div class="col-12 col-md-2">
      <div class="bg-white rounded-3 border p-3">
        <div class="fw-bold mb-3" style="color:#1a3c5e; font-size:.85rem">MENU</div>
        <a href="admin.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none text-muted" style="font-size:.9rem">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="products.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none text-muted" style="font-size:.9rem">
          <i class="bi bi-box-seam"></i> Produkty
        </a>
        <a href="users.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none fw-semibold" style="background:#f0f4f8; color:#1a3c5e; font-size:.9rem">
          <i class="bi bi-people"></i> Uzytkownicy
        </a>
        <a href="orders.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none text-muted" style="font-size:.9rem">
          <i class="bi bi-receipt"></i> Zamowienia
        </a>
      </div>
    </div>

    <div class="col-12 col-md-10">
      <div class="bg-white rounded-3 border p-4">
        <h6 class="fw-bold mb-3" style="color:#1a3c5e">Uzytkownicy</h6>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead style="font-size:.85rem; color:#6b7a8d">
              <tr>
                <th>#</th>
                <th>Login</th>
                <th>Imie i nazwisko</th>
                <th>E-mail</th>
                <th>Rola</th>
                <th>Data rejestracji</th>
                <th>Akcje</th>
              </tr>
            </thead>
            <tbody style="font-size:.88rem">
              <?php while ($user = $users->fetch_assoc()): ?>
                <tr>
                  <td><?= (int)$user['id'] ?></td>
                  <td><?= e($user['login']) ?></td>
                  <td><?= e($user['name']) ?></td>
                  <td><?= e($user['email']) ?></td>
                  <td>
    <form method="POST" class="d-flex gap-2">
        <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>">

        <select name="role" class="form-select form-select-sm">
            <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>
                User
            </option>

            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
                Admin
            </option>
        </select>

        <button type="submit" class="btn btn-sm btn-primary">
            Zapisz
        </button>
    </form>
</td>
                  <td><?= e($user['created_at']) ?></td>
                  <td>
    <?php if ((int)$user['id'] !== (int)$_SESSION['user_id']): ?>
        <form method="POST" onsubmit="return confirm('Czy na pewno usunac uzytkownika?')">
            <input type="hidden" name="delete_user_id" value="<?= (int)$user['id'] ?>">
            <button type="submit" class="btn btn-sm btn-danger">
                Usun
            </button>
        </form>
    <?php else: ?>
        <span class="text-muted">Aktualne konto</span>
    <?php endif; ?>
</td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>
