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

```bash
cd apps/laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan test
```

## ⚠️ Règles

- ✅ Corrigez les fichiers buggés pour faire passer les tests
- ❌ Ne modifiez pas les fichiers de test
- ❌ Ne supprimez pas les assertions existantes

---
*Qileo Tech Challenges Platform*
