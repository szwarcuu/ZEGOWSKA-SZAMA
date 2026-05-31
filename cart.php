<?php
session_start();
require_once 'db.php';

function e($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$errors = [];
$message = '';
$order_saved = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!isset($_SESSION['user_id'])) {
    $errors[] = 'Musisz sie zalogowac, aby zlozyc zamowienie.';
  }

  $cart = json_decode($_POST['cart_data'] ?? '', true);

  if (!is_array($cart) || count($cart) === 0) {
    $errors[] = 'Koszyk jest pusty.';
  }

  if (!$errors) {
    $items = [];
    $total_amount = 0;

    foreach ($cart as $cart_item) {
      $product_id = (int)($cart_item['id'] ?? 0);
      $quantity = (int)($cart_item['quantity'] ?? 0);

      if ($product_id <= 0 || $quantity <= 0) {
        $errors[] = 'Nieprawidlowe dane produktu w koszyku.';
        break;
      }

      $stmt = $conn->prepare('SELECT id, name, price FROM products WHERE id = ? LIMIT 1');
      $stmt->bind_param('i', $product_id);
      $stmt->execute();
      $product = $stmt->get_result()->fetch_assoc();

      if (!$product) {
        $errors[] = 'Jeden z produktow nie istnieje.';
        break;
      }

      $price = (float)$product['price'];
      $total_amount += $price * $quantity;
      $items[] = [
        'product_id' => (int)$product['id'],
        'product_name' => $product['name'],
        'quantity' => $quantity,
        'unit_price' => $price
      ];
    }
  }

  if (!$errors) {
    try {
      $conn->begin_transaction();

      $user_id = (int)$_SESSION['user_id'];
      $stmt = $conn->prepare('INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)');
      $status = 'pending';
      $stmt->bind_param('ids', $user_id, $total_amount, $status);
      $stmt->execute();
      $order_id = $conn->insert_id;

      $stmt = $conn->prepare('INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)');

      foreach ($items as $item) {
        $product_id = $item['product_id'];
        $product_name = $item['product_name'];
        $quantity = $item['quantity'];
        $unit_price = $item['unit_price'];

        $stmt->bind_param(
          'iisid',
          $order_id,
          $product_id,
          $product_name,
          $quantity,
          $unit_price
        );
        $stmt->execute();
      }

      $conn->commit();
      $order_saved = true;
      $message = 'Zamowienie zostalo zlozone.';
    } catch (Exception $exception) {
      $conn->rollback();
      $errors[] = 'Nie udalo sie zapisac zamowienia.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Koszyk - Zegowska Szama</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="style.css" rel="stylesheet"/>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">Zegowska Szama</a>
    <div class="d-flex gap-2 align-items-center ms-auto">
      <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Produkty</a>
      <?php if (isset($_SESSION['user_id'])): ?>
        <span class="text-white-50" style="font-size:.85rem"><?= e($_SESSION['name']) ?></span>
        <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Wyloguj</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Zaloguj sie</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<div class="container mt-4 mb-5">
  <div class="bg-white rounded-3 border p-4">
    <div class="d-flex justify-content-between align-items-center gap-3 mb-3 flex-wrap">
      <h5 class="fw-bold mb-0" style="color: var(--primary)">Koszyk</h5>
      <a href="index.php#produkty" class="btn btn-sm btn-outline-secondary">Wroc do produktow</a>
    </div>

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

    <div id="empty-cart" class="alert alert-info mb-0 d-none">
      Koszyk jest pusty.
    </div>

    <div id="cart-content" class="d-none">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead style="font-size:.85rem; color:#6b7a8d">
            <tr>
              <th>Produkt</th>
              <th>Ilosc</th>
              <th>Cena</th>
              <th>Razem</th>
              <th>Akcje</th>
            </tr>
          </thead>
          <tbody id="cart-items" style="font-size:.9rem"></tbody>
        </table>
      </div>

      <div class="d-flex justify-content-end mt-3">
        <div class="fw-bold" style="color: var(--primary)">
          Suma: <span id="cart-total">0,00 zl</span>
        </div>
      </div>

      <div class="border-top mt-4 pt-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <h6 class="fw-bold mb-3" style="color: var(--primary)">Dane zamowienia</h6>
          <form id="order-form" method="post" action="cart.php">
            <input type="hidden" name="cart_data" id="cart-data"/>

            <div class="mb-3">
              <label class="form-label fw-semibold" style="font-size:.85rem">Uwagi do zamowienia</label>
              <textarea name="notes" class="form-control" rows="3" placeholder="Np. odbior na dlugiej przerwie"></textarea>
            </div>

            <button type="submit" class="btn text-white fw-semibold" style="background: var(--primary); border-radius:8px">
              Zloz zamowienie
            </button>
          </form>
        <?php else: ?>
          <div class="alert alert-warning mb-0">
            Zaloguj sie, aby zlozyc zamowienie.
            <a href="login.php" class="alert-link">Przejdz do logowania</a>.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
const cartKey = 'zegowska_cart';
const emptyCart = document.getElementById('empty-cart');
const cartContent = document.getElementById('cart-content');
const cartItems = document.getElementById('cart-items');
const cartTotal = document.getElementById('cart-total');
const orderForm = document.getElementById('order-form');
const cartDataInput = document.getElementById('cart-data');
const orderSaved = <?= $order_saved ? 'true' : 'false' ?>;

function getCart() {
  const savedCart = localStorage.getItem(cartKey);

  if (!savedCart) {
    return [];
  }

  try {
    return JSON.parse(savedCart);
  } catch (error) {
    return [];
  }
}

function formatPrice(price) {
  return price.toFixed(2).replace('.', ',') + ' zl';
}

function saveCart(cart) {
  localStorage.setItem(cartKey, JSON.stringify(cart));
}

function renderCart() {
  const cart = getCart();
  cartItems.innerHTML = '';

  if (cart.length === 0) {
    emptyCart.classList.remove('d-none');
    cartContent.classList.add('d-none');
    return;
  }

  emptyCart.classList.add('d-none');
  cartContent.classList.remove('d-none');

  let total = 0;

  cart.forEach(function(item) {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    const row = document.createElement('tr');
    const nameCell = document.createElement('td');
    const quantityCell = document.createElement('td');
    const priceCell = document.createElement('td');
    const totalCell = document.createElement('td');
    const actionsCell = document.createElement('td');
    const quantityInput = document.createElement('input');
    const removeButton = document.createElement('button');

    nameCell.textContent = item.name;
    quantityInput.type = 'number';
    quantityInput.min = '1';
    quantityInput.value = item.quantity;
    quantityInput.className = 'form-control form-control-sm';
    quantityInput.style.width = '80px';

    quantityInput.addEventListener('change', function() {
      const newQuantity = parseInt(quantityInput.value, 10);

      if (newQuantity < 1 || isNaN(newQuantity)) {
        quantityInput.value = item.quantity;
        return;
      }

      item.quantity = newQuantity;
      saveCart(cart);
      renderCart();
    });

    quantityCell.appendChild(quantityInput);
    priceCell.textContent = formatPrice(item.price);
    totalCell.textContent = formatPrice(itemTotal);
    totalCell.className = 'fw-semibold';

    removeButton.type = 'button';
    removeButton.className = 'btn btn-sm btn-outline-danger';
    removeButton.textContent = 'Usun';
    removeButton.addEventListener('click', function() {
      const newCart = cart.filter(function(cartItem) {
        return cartItem.id !== item.id;
      });

      saveCart(newCart);
      renderCart();
    });

    actionsCell.appendChild(removeButton);

    row.appendChild(nameCell);
    row.appendChild(quantityCell);
    row.appendChild(priceCell);
    row.appendChild(totalCell);
    row.appendChild(actionsCell);

    cartItems.appendChild(row);
  });

  cartTotal.textContent = formatPrice(total);
}

if (orderSaved) {
  localStorage.removeItem(cartKey);
} else {
  renderCart();
}

if (orderForm) {
  orderForm.addEventListener('submit', function() {
    cartDataInput.value = JSON.stringify(getCart());
  });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
