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
