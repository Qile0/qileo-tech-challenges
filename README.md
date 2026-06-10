# Qileo Tech Challenge — Louis Zerri

> Senior · 7 ans · Apps: laravel

## 🎯 Challenges

**laravel**: Transaction Rate Limiter — Limitation du taux de transactions par compte dans une fenêtre glissante de 60 secondes, pour protéger un système de paiement fintech contre les abus.

## 🧪 Tests à faire passer

### laravel — Transaction Rate Limiter
1. `allows_transaction_within_rate_limit()` — Autorise si sous le seuil
2. `blocks_transaction_exceeding_rate_limit()` — Bloque si seuil dépassé
3. `sliding_window_expires_old_transactions()` — Les vieilles transactions expirent
4. `different_accounts_have_independent_limits()` — Compteurs indépendants par compte
5. `computes_remaining_allowance()` — Retourne le nombre restant autorisé

## 🚀 Démarrage

### Option A — Dev Container (recommandé)

Prérequis : [Docker Desktop](https://www.docker.com/products/docker-desktop/) + [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) (VS Code / Cursor).

1. Ouvrir ce repo dans VS Code ou Cursor
2. `Cmd/Ctrl + Shift + P` → **Dev Containers: Reopen in Container**
3. Attendre la fin de `composer install` (automatique au premier lancement)

```bash
cd apps/laravel
php artisan test --filter=RateLimiterTest   # lancer les tests du challenge
php artisan serve                         # app sur http://localhost:8000
```

### Option B — Installation locale

Prérequis : PHP 8.4+, Composer

```bash
cd apps/laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan test --filter=RateLimiterTest
```

## ⚠️ Règles

- ✅ Corrigez les fichiers buggés pour faire passer les tests
- ❌ Ne modifiez pas les fichiers de test
- ❌ Ne supprimez pas les assertions existantes

---
*Qileo Tech Challenges Platform*
