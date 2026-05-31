<?php
require_once '../includes/auth.php';
require_admin();
require_once '../includes/db.php';

$product_count = $conn->query('SELECT COUNT(*) AS total FROM products')->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Panel admina - Zegowska Szama</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="../css/style.css" rel="stylesheet"/>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="admin.php">Zegowska Szama - Admin</a>
    <div class="d-flex align-items-center gap-3">
      <span class="text-white-50" style="font-size:.85rem">Zalogowany: <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8') ?></span>
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
        <a href="admin.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none fw-semibold" style="background:#f0f4f8; color:#1a3c5e; font-size:.9rem">
          <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="products.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none text-muted" style="font-size:.9rem">
          <i class="bi bi-box-seam"></i> Produkty
        </a>
        <a href="users.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none text-muted" style="font-size:.9rem">
          <i class="bi bi-people"></i> Uzytkownicy
        </a>
        <a href="orders.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none text-muted" style="font-size:.9rem">
          <i class="bi bi-receipt"></i> Zamowienia
        </a>
      </div>
    </div>

    <div class="col-12 col-md-10">
      <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
          <div class="bg-white rounded-3 border p-3 text-center">
            <div style="font-size:2rem">📦</div>
            <div class="fw-bold mt-1" style="font-size:1.5rem; color:#1a3c5e"><?= (int)$product_count ?></div>
            <div class="text-muted" style="font-size:.82rem">Produkty</div>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3 border p-4">
        <h6 class="fw-bold mb-3" style="color:#1a3c5e">Szybkie akcje</h6>
        <a href="products.php" class="btn text-white" style="background:#1a3c5e; border-radius:8px">
          <i class="bi bi-plus-lg me-1"></i>Dodaj produkt
        </a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
