# Qileo Tech Challenge — Yann Butscher

> Senior · 7 ans · Apps: laravel

## 🎯 Challenges

**laravel**: Fraud Detection — Détection de transactions frauduleuses dans un système de paiement fintech : scoring de risque, règles métier combinées et gestion des seuils d'alerte.

## 🧪 Tests à faire passer

### laravel — Fraud Detection
1. `compute_risk_score()` — Calcule un score de risque
2. `is_velocity_breach()` — Détecte un dépassement de vélocité
3. `flag_suspicious_country()` — Flagge un pays à risque
4. `evaluate_transaction()` — Évalue une transaction complète
5. `reconcile_alerts()` — Réconcilie les alertes d'un batch

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
