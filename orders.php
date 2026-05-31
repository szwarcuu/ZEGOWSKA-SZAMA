<?php
require_once 'auth.php';
require_admin();
require_once 'db.php';

function e($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$orders = $conn->query(
  'SELECT orders.id, orders.total_amount, orders.status, orders.created_at, users.name, users.email,
          GROUP_CONCAT(CONCAT(order_items.product_name, " x", order_items.quantity) SEPARATOR ", ") AS products
   FROM orders
   JOIN users ON orders.user_id = users.id
   LEFT JOIN order_items ON orders.id = order_items.order_id
   GROUP BY orders.id, orders.total_amount, orders.status, orders.created_at, users.name, users.email
   ORDER BY orders.created_at DESC'
);
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
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead style="font-size:.85rem; color:#6b7a8d">
              <tr>
                <th>#</th>
                <th>Uzytkownik</th>
                <th>E-mail</th>
                <th>Produkty</th>
                <th>Data</th>
                <th>Kwota</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody style="font-size:.88rem">
              <?php if ($orders->num_rows === 0): ?>
                <tr>
                  <td colspan="7" class="text-muted">Brak zamowien.</td>
                </tr>
              <?php endif; ?>

              <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                  <td><?= (int)$order['id'] ?></td>
                  <td><?= e($order['name']) ?></td>
                  <td><?= e($order['email']) ?></td>
                  <td><?= e($order['products'] ?? '') ?></td>
                  <td><?= e($order['created_at']) ?></td>
                  <td><?= number_format((float)$order['total_amount'], 2, ',', ' ') ?> zl</td>
                  <td><span class="badge text-bg-warning"><?= e($order['status']) ?></span></td>
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
