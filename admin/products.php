<?php
require_once 'includes/auth.php';
require_admin();
require_once 'includes/db.php';

function e($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$message = '';
$errors = [];
$edit_product = null;
$categories = [
  'kanapki' => 'Kanapki',
  'napoje' => 'Napoje',
  'slodycze' => 'Slodycze',
  'przekaski' => 'Przekaski'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $delete_id = (int)$_POST['delete_id'];
  $stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
  $stmt->bind_param('i', $delete_id);
  $stmt->execute();
  header('Location: products.php?deleted=1');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
  $id = (int)($_POST['id'] ?? 0);
  $name = trim($_POST['name'] ?? '');
  $category = $_POST['category'] ?? '';
  $description = trim($_POST['description'] ?? '');
  $price = (float)($_POST['price'] ?? 0);
  $stock = (int)($_POST['stock'] ?? 0);
  $is_promo = isset($_POST['is_promo']) ? 1 : 0;

  if ($name === '') {
    $errors[] = 'Podaj nazwe produktu.';
  }

  if (!isset($categories[$category])) {
    $errors[] = 'Wybierz poprawna kategorie.';
  }

  if ($price <= 0) {
    $errors[] = 'Cena musi byc wieksza od 0.';
  }

  if ($stock < 0) {
    $errors[] = 'Stan magazynowy nie moze byc ujemny.';
  }

  if (!$errors) {
    if ($id > 0) {
      $stmt = $conn->prepare('UPDATE products SET name = ?, category = ?, description = ?, price = ?, stock = ?, is_promo = ? WHERE id = ?');
      $stmt->bind_param('sssdiii', $name, $category, $description, $price, $stock, $is_promo, $id);
      $stmt->execute();
      header('Location: products.php?updated=1');
      exit;
    }

    $stmt = $conn->prepare('INSERT INTO products (name, category, description, price, stock, is_promo) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('sssdii', $name, $category, $description, $price, $stock, $is_promo);
    $stmt->execute();
    header('Location: products.php?added=1');
    exit;
  }
}

if (isset($_GET['edit'])) {
  $edit_id = (int)$_GET['edit'];
  $stmt = $conn->prepare('SELECT * FROM products WHERE id = ?');
  $stmt->bind_param('i', $edit_id);
  $stmt->execute();
  $edit_product = $stmt->get_result()->fetch_assoc();
}

if (isset($_GET['added'])) {
  $message = 'Produkt zostal dodany.';
} elseif (isset($_GET['updated'])) {
  $message = 'Produkt zostal zaktualizowany.';
} elseif (isset($_GET['deleted'])) {
  $message = 'Produkt zostal usuniety.';
}

$products = $conn->query('SELECT * FROM products ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Produkty - Zegowska Szama</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="css/style.css" rel="stylesheet"/>
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
        <a href="products.php" class="d-flex align-items-center gap-2 p-2 rounded mb-1 text-decoration-none fw-semibold" style="background:#f0f4f8; color:#1a3c5e; font-size:.9rem">
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

      <div class="bg-white rounded-3 border p-4 mb-4">
        <h6 class="fw-bold mb-3" style="color:#1a3c5e">
          <?= $edit_product ? 'Edytuj produkt' : 'Dodaj nowy produkt' ?>
        </h6>

        <form method="post" action="products.php">
          <input type="hidden" name="save_product" value="1"/>
          <input type="hidden" name="id" value="<?= $edit_product ? (int)$edit_product['id'] : 0 ?>"/>

          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem">Nazwa produktu</label>
              <input type="text" name="name" class="form-control" required
                     value="<?= $edit_product ? e($edit_product['name']) : '' ?>"/>
            </div>

            <div class="col-12 col-md-4">
              <label class="form-label fw-semibold" style="font-size:.85rem">Kategoria</label>
              <select name="category" class="form-select" required>
                <?php foreach ($categories as $value => $label): ?>
                  <option value="<?= e($value) ?>" <?= $edit_product && $edit_product['category'] === $value ? 'selected' : '' ?>>
                    <?= e($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-6 col-md-2">
              <label class="form-label fw-semibold" style="font-size:.85rem">Cena (zl)</label>
              <input type="number" name="price" class="form-control" step="0.01" min="0.01" required
                     value="<?= $edit_product ? e($edit_product['price']) : '' ?>"/>
            </div>

            <div class="col-6 col-md-2">
              <label class="form-label fw-semibold" style="font-size:.85rem">Stan</label>
              <input type="number" name="stock" class="form-control" min="0" required
                     value="<?= $edit_product ? (int)$edit_product['stock'] : '' ?>"/>
            </div>

            <div class="col-12">
              <label class="form-label fw-semibold" style="font-size:.85rem">Opis</label>
              <input type="text" name="description" class="form-control"
                     value="<?= $edit_product ? e($edit_product['description']) : '' ?>"/>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_promo" id="is_promo"
                       <?= $edit_product && (int)$edit_product['is_promo'] === 1 ? 'checked' : '' ?>/>
                <label class="form-check-label" for="is_promo">Produkt promocyjny</label>
              </div>
            </div>

            <div class="col-12 d-flex gap-2">
              <button class="btn text-white fw-semibold" style="background:#1a3c5e; border-radius:8px">
                <?= $edit_product ? 'Zapisz zmiany' : 'Dodaj produkt' ?>
              </button>
              <?php if ($edit_product): ?>
                <a href="products.php" class="btn btn-outline-secondary">Anuluj edycje</a>
              <?php endif; ?>
            </div>
          </div>
        </form>
      </div>

      <div class="bg-white rounded-3 border p-4">
        <h6 class="fw-bold mb-3" style="color:#1a3c5e">Lista produktow</h6>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead style="font-size:.85rem; color:#6b7a8d">
              <tr>
                <th>#</th>
                <th>Nazwa</th>
                <th>Kategoria</th>
                <th>Cena</th>
                <th>Stan</th>
                <th>Promocja</th>
                <th>Akcje</th>
              </tr>
            </thead>
            <tbody style="font-size:.88rem">
              <?php while ($product = $products->fetch_assoc()): ?>
                <tr>
                  <td><?= (int)$product['id'] ?></td>
                  <td><?= e($product['name']) ?></td>
                  <td><?= e($categories[$product['category']] ?? $product['category']) ?></td>
                  <td><?= number_format((float)$product['price'], 2, ',', ' ') ?> zl</td>
                  <td><span class="badge text-bg-success"><?= (int)$product['stock'] ?> szt.</span></td>
                  <td><?= (int)$product['is_promo'] === 1 ? 'Tak' : 'Nie' ?></td>
                  <td>
                    <div class="d-flex gap-2">
                      <a href="products.php?edit=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-secondary">Edytuj</a>
                      <form method="post" action="products.php" onsubmit="return confirm('Usunac produkt?');">
                        <input type="hidden" name="delete_id" value="<?= (int)$product['id'] ?>"/>
                        <button class="btn btn-sm btn-outline-danger">Usun</button>
                      </form>
                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
