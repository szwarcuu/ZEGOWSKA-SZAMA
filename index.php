<?php
session_start();
require_once 'db.php';

function e($text) {
  return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

$products = $conn->query('SELECT * FROM products ORDER BY id DESC');
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Zegowska Szama</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet"/>
  <link href="style.css" rel="stylesheet"/>
</head>
<body>

<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">Zegowska Szama</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto ms-3">
        <li class="nav-item"><a class="nav-link" href="index.php">Strona glowna</a></li>
        <li class="nav-item"><a class="nav-link" href="#produkty">Produkty</a></li>
        <li class="nav-item"><a class="nav-link" href="#promocje">Promocje</a></li>
        <?php if ($is_admin): ?>
          <li class="nav-item"><a class="nav-link" href="products.php">Panel admina</a></li>
        <?php endif; ?>
      </ul>
      <div class="d-flex gap-2 align-items-center">
        <a href="#" class="nav-link text-white-50">
          <i class="bi bi-cart3 fs-5"></i>
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
          <span class="text-white-50" style="font-size:.85rem"><?= e($_SESSION['name']) ?></span>
          <a href="logout.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
            Wyloguj
          </a>
        <?php else: ?>
          <a href="login.php" class="btn btn-sm btn-outline-light rounded-pill px-3">
            Zaloguj sie
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="container">
    <div class="row align-items-center g-4">
      <div class="col-md-7">
        <h1>Zamow online,<br/>odbierz w sklepiku!</h1>
        <p class="mt-2 mb-4">Przegladaj kanapki, przekaski i napoje. Zloz zamowienie przed przerwa i odbierz bez kolejki.</p>
        <a href="#produkty" class="btn-accent">Zobacz produkty</a>
      </div>
      <div class="col-md-5 text-center d-none d-md-block">
        <div style="font-size: 5rem;">🥪🥤🍫</div>
      </div>
    </div>
  </div>
</section>

<div class="container mt-4" id="promocje">
  <div class="p-3 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3"
       style="background:#fff8e1; border: 1px solid #f5bc45;">
    <div>
      <span class="fw-semibold" style="color:#7d4f00">Promocje tylko dla zalogowanych!</span>
      <span class="ms-2 text-muted" style="font-size:.88rem">Po zalogowaniu rabat zostanie policzony w koszyku.</span>
    </div>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="logout.php" class="btn btn-sm btn-outline-secondary fw-semibold px-4">Wyloguj</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-sm btn-warning fw-semibold px-4">Zaloguj sie</a>
    <?php endif; ?>
  </div>
</div>

<div class="container mt-4" id="produkty">
  <div class="row g-2">
    <div class="col-12 col-md-8">
      <div class="input-group">
        <span class="input-group-text bg-white">
          <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" class="form-control" id="search" placeholder="Szukaj produktu..."/>
      </div>
    </div>
    <div class="col-12 col-md-4">
      <select class="form-select" id="category">
        <option value="">Wszystkie kategorie</option>
        <option value="kanapki">Kanapki</option>
        <option value="napoje">Napoje</option>
        <option value="slodycze">Slodycze</option>
        <option value="przekaski">Przekaski</option>
      </select>
    </div>
  </div>
</div>

<div class="container mt-3 mb-5">
  <h5 class="fw-bold mb-3" style="color: var(--primary)">Dostepne produkty</h5>
  <div class="row g-3" id="product-list">
    <?php while ($product = $products->fetch_assoc()): ?>
      <div class="col-6 col-md-4 col-lg-3 product-card"
           data-category="<?= e($product['category']) ?>"
           data-name="<?= e($product['name']) ?>">
        <div class="card h-100">
          <div class="card-img">🍽️</div>
          <div class="card-body d-flex flex-column">
            <div class="d-flex align-items-center gap-2 mb-1">
              <span class="fw-semibold"><?= e($product['name']) ?></span>
              <?php if ((int)$product['is_promo'] === 1): ?>
                <span class="badge-promo">Promo</span>
              <?php endif; ?>
            </div>
            <div class="text-muted" style="font-size:.82rem; flex:1"><?= e($product['description']) ?></div>
            <div class="text-muted mt-2" style="font-size:.82rem">
              Dostepne: <?= (int)$product['stock'] ?> szt.
            </div>
            <div class="d-flex align-items-center justify-content-between mt-2">
              <span class="price"><?= number_format((float)$product['price'], 2, ',', ' ') ?> zl</span>
              <button class="btn-add"
                      type="button"
                      data-id="<?= (int)$product['id'] ?>"
                      data-name="<?= e($product['name']) ?>"
                      data-price="<?= number_format((float)$product['price'], 2, '.', '') ?>">
                Dodaj
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>

<footer>
  <div class="container">
    <div class="row g-3">
      <div class="col-12 col-md-4">
        <div class="fw-bold text-white mb-2">Zegowska Szama</div>
        <div>Szkolny sklepik ZS Zegowska</div>
        <div>Korytarz glowny, parter</div>
      </div>
      <div class="col-6 col-md-4">
        <div class="fw-semibold text-white mb-2">Linki</div>
        <a href="index.php" class="d-block mb-1">Strona glowna</a>
        <a href="#produkty" class="d-block mb-1">Produkty</a>
        <a href="#promocje" class="d-block">Promocje</a>
      </div>
      <div class="col-6 col-md-4">
        <div class="fw-semibold text-white mb-2">Godziny otwarcia</div>
        <div>Poniedzialek - Piatek</div>
        <div>7:30 - 15:00</div>
      </div>
    </div>
  </div>
</footer>

<script>
const search = document.getElementById('search');
const category = document.getElementById('category');
const cards = document.querySelectorAll('.product-card');

function filterProducts() {
  const phrase = search.value.toLowerCase();
  const selectedCategory = category.value;

  cards.forEach(function(card) {
    const name = card.dataset.name;
    const lowerName = name.toLowerCase();
    const cardCategory = card.dataset.category;
    const matchesName = lowerName.includes(phrase);
    const matchesCategory = selectedCategory === '' || selectedCategory === cardCategory;
    card.style.display = matchesName && matchesCategory ? '' : 'none';
  });
}

search.addEventListener('input', filterProducts);
category.addEventListener('change', filterProducts);

const cartKey = 'zegowska_cart';
const addButtons = document.querySelectorAll('.btn-add');

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

function saveCart(cart) {
  localStorage.setItem(cartKey, JSON.stringify(cart));
}

function addToCart(product) {
  const cart = getCart();
  const existingProduct = cart.find(function(item) {
    return item.id === product.id;
  });

  if (existingProduct) {
    existingProduct.quantity += 1;
  } else {
    cart.push({
      id: product.id,
      name: product.name,
      price: product.price,
      quantity: 1
    });
  }

  saveCart(cart);
}

addButtons.forEach(function(button) {
  button.addEventListener('click', function() {
    addToCart({
      id: button.dataset.id,
      name: button.dataset.name,
      price: parseFloat(button.dataset.price)
    });

    button.textContent = 'Dodano';

    setTimeout(function() {
      button.textContent = 'Dodaj';
    }, 900);
  });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
