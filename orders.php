<?php
require_once 'auth.php';
require_admin();

function e($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Zamowienia - Zegowska Szama</title>
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
        <a href="users.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none text-muted" style="font-size:.9rem">
          <i class="bi bi-people"></i> Uzytkownicy
        </a>
        <a href="orders.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none fw-semibold" style="background:#f0f4f8; color:#1a3c5e; font-size:.9rem">
          <i class="bi bi-receipt"></i> Zamowienia
        </a>
      </div>
    </div>

    <div class="col-12 col-md-10">
      <div class="bg-white rounded-3 border p-4">
        <h6 class="fw-bold mb-3" style="color:#1a3c5e">Zamowienia</h6>
        <p class="mb-0 text-muted">Widok zamowien zostanie rozbudowany po dodaniu koszyka i skladania zamowien.</p>
      </div>
    </div>
  </div>
</div>
</body>
</html>
