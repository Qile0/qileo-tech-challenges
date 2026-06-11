# Qileo Tech Challenge — Eddie

> Senior · 7 ans · Apps: laravel

## 🎯 Challenges

**laravel**: Order Processing — Traitement de commandes e-commerce : calcul du total, application de remises, gestion du stock, statut de commande et frais de port.

## 🧪 Tests à faire passer

### laravel — Order Processing
1. `calculate_order_total()` — Calcule le total d'une commande
2. `apply_discount()` — Applique une remise en pourcentage
3. `reject_order_with_out_of_stock_item()` — Rejette si stock insuffisant
4. `compute_order_status()` — Retourne le bon statut de commande
5. `calculate_shipping_cost()` — Calcule les frais de port

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
