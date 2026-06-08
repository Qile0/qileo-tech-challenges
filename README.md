# Qileo Tech Challenge — William Deriquehem

> Mid-level · 4 ans · Apps: laravel

## 🎯 Challenges

**laravel**: Virement SEPA — Validation et traitement des virements SEPA dans le cadre des opérations bancaires courantes.

## 🧪 Tests à faire passer

### laravel — Virement SEPA
1. `validate_iban()` — Valide un IBAN
2. `calculate_fee()` — Calcule les frais
3. `check_daily_limit()` — Vérifie le plafond
4. `create_transfer()` — Crée un virement
5. `reconcile_batch()` — Réconcilie un batch

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
