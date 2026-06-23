# Qileo Tech Challenge — Haritiana

> Senior · 9 ans · Apps: laravel

## 🎯 Challenges

**laravel**: Transaction Monitoring — Surveillance AML des transactions fintech : détection de montants élevés, vélocité anormale, structuration et contreparties blacklistées.

## 🧪 Tests à faire passer

### laravel — Transaction Monitoring
1. `flags_high_value_transaction()` — Détecte un montant ≥ 10 000 €
2. `detects_velocity_breach()` — Détecte un seuil de vélocité (5 tx / 24 h)
3. `detects_structuring_pattern()` — Détecte une structuration (3+ montants entre 9 000 € et 10 000 €)
4. `evaluate_allows_low_risk_transaction()` — Autorise une transaction à faible risque
5. `evaluate_blocks_blacklisted_counterparty()` — Bloque une contrepartie blacklistée

## 🚀 Démarrage

### Option A — Dev Container (recommandé)

Prérequis : [Docker Desktop](https://www.docker.com/products/docker-desktop/) + [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) (VS Code / Cursor).

1. Ouvrir ce repo dans VS Code ou Cursor
2. `Cmd/Ctrl + Shift + P` → **Dev Containers: Reopen in Container**
3. Attendre la fin de `composer install` (automatique au premier lancement)

```bash
cd apps/laravel
php artisan test --filter=TransactionMonitoringTest
php artisan serve                 # app sur http://localhost:8000
```

### Option B — Installation locale

Prérequis : PHP 8.4+, Composer

```bash
cd apps/laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan test --filter=TransactionMonitoringTest
```

## ⚠️ Règles

- ✅ Corrigez les fichiers buggés pour faire passer les tests
- ❌ Ne modifiez pas les fichiers de test
- ❌ Ne supprimez pas les assertions existantes

---
*Qileo Tech Challenges Platform*
