# Zegowska Szama

Internetowa aplikacja sklepu szkolnego umożliwiająca przeglądanie produktów, składanie zamówień oraz zarządzanie sklepem przez administratora.

## Autorzy

- Jan Szwarc
- Tymon Jaszke

## Technologie

- PHP 8
- MySQL
- HTML5
- CSS3
- Bootstrap 5
- JavaScript
- Git
- GitHub

## Funkcjonalności

### Użytkownik

- Rejestracja konta
- Logowanie i wylogowywanie
- Przeglądanie produktów
- Dodawanie produktów do koszyka
- Zmiana ilości produktów w koszyku
- Usuwanie produktów z koszyka
- Składanie zamówień
- Dostęp do promocji po zalogowaniu

### Administrator

- Panel administracyjny
- Zarządzanie produktami
- Dodawanie produktów
- Edycja produktów
- Usuwanie produktów
- Zarządzanie użytkownikami
- Zmiana roli użytkowników
- Usuwanie użytkowników
- Zarządzanie zamówieniami
- Zmiana statusów zamówień

## Statusy zamówień

- Oczekujące
- Przyjęte
- Zrealizowane
- Anulowane

## Struktura projektu

```text
ZEGOWSKA_SZAMA
│
├── admin
│   ├── admin.php
│   ├── orders.php
│   ├── products.php
│   └── users.php
│
├── assets
│   └── images
│
├── css
│   └── style.css
│
├── database
│   └── zegowskaszama.sql
│
├── docs
│   ├── orders.html
│   └── users.html
│
├── includes
│   ├── auth.php
│   └── db.php
│
├── cart.php
├── index.php
├── login.php
├── logout.php
└── register.php
```

## Instalacja

1. Sklonuj repozytorium:

```bash
git clone https://github.com/szwarcuu/ZEGOWSKA-SZAMA.git
```

2. Umieść projekt w katalogu serwera WWW (np. `htdocs` w XAMPP).

3. Utwórz bazę danych MySQL.

4. Zaimportuj plik:

```text
database/zegowskaszama.sql
```

5. Skonfiguruj połączenie z bazą danych w pliku:

```text
includes/db.php
```

6. Uruchom Apache i MySQL.

7. Otwórz projekt w przeglądarce:

```text
http://localhost/ZEGOWSKA_SZAMA/
```

## Baza danych

Projekt wykorzystuje następujące tabele:

- users
- products
- orders
- order_items

## Bezpieczeństwo

- Hasła przechowywane są w postaci zaszyfrowanej przy użyciu `password_hash()`
- Dostęp do panelu administratora jest ograniczony do kont z rolą administratora
- Dane wyświetlane użytkownikowi są filtrowane za pomocą `htmlspecialchars()`

## Wersja

Wersja projektu: 1.0

## Licencja

Projekt wykonany w celach edukacyjnych.
