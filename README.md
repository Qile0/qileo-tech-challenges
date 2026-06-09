# Qileo Tech Challenge — Matthieu Borde

> Senior · 8 ans · Apps: laravel

## 🎯 Challenges

**laravel**: Wallet Balance — Gestion du solde d'un portefeuille fintech : disponibilité, débit, crédit et plafond journalier.

## 🧪 Tests à faire passer

### laravel — Wallet Balance
1. `get_available_balance()` — Solde disponible
2. `debit()` — Débit réduit le solde
3. `debit_throws_on_insufficient_funds()` — Exception si fonds insuffisants
4. `credit()` — Crédit augmente le solde
5. `daily_debit_limit()` — Plafond journalier de 5000€

## 🚀 Démarrage

### Option A — Dev Container (recommandé)

Prérequis : [Docker Desktop](https://www.docker.com/products/docker-desktop/) + [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) (VS Code / Cursor).

1. Ouvrir ce repo dans VS Code ou Cursor
2. `Cmd/Ctrl + Shift + P` → **Dev Containers: Reopen in Container**
3. Attendre la fin de `composer install` (automatique au premier lancement)

```bash
cd apps/laravel
php artisan test --filter=WalletTest   # lancer les tests du challenge
php artisan serve                    # app sur http://localhost:8000
```

### Option B — Installation locale

Prérequis : PHP 8.4+, Composer

```bash
cd apps/laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan test --filter=WalletTest
```

## ⚠️ Règles

- ✅ Corrigez les fichiers buggés pour faire passer les tests
- ❌ Ne modifiez pas les fichiers de test
- ❌ Ne supprimez pas les assertions existantes

---
*Qileo Tech Challenges Platform*
