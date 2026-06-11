# Corrigé — Laravel · Order Processing (Eddie)

> Fichier réservé aux évaluateurs. Ne pas partager avec les candidats.

## Fichier à corriger

`apps/laravel/app/Services/OrderService.php`

---

## Vue d'ensemble

| Test | Bug | Correction |
|------|-----|------------|
| `test_calculate_order_total` | Quantité ignorée dans le total | `$price * $quantity` |
| `test_apply_discount` | Remise ajoutée au lieu d'être soustraite | `$total - $discount` |
| `test_reject_order_with_out_of_stock_item` | Aucune exception levée | `throw` si `quantity > stock` |
| `test_compute_order_status` | Statuts inversés / annulation ignorée | Logique PENDING → CONFIRMED → SHIPPED → CANCELLED |
| `test_calculate_shipping_cost` | Seuil strict `>` au lieu de `>=` | Livraison gratuite dès 100 € |

---

## Solution complète

```php
<?php

namespace App\Services;

class OrderService
{
    private const FREE_SHIPPING_THRESHOLD = 100.00;
    private const SHIPPING_COST          = 5.90;

    public function calculate_order_total(array $items): float
    {
        $total = 0.0;
        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return round($total, 2);
    }

    public function apply_discount(float $total, int $percent): float
    {
        $discount = $total * $percent / 100;
        return round($total - $discount, 2);
    }

    public function reject_order_with_out_of_stock_item(array $items): void
    {
        foreach ($items as $item) {
            if ($item['quantity'] > $item['stock']) {
                throw new \InvalidArgumentException(
                    'Out of stock: ' . $item['name'] . ' (requested ' . $item['quantity'] . ', available ' . $item['stock'] . ')'
                );
            }
        }
    }

    public function compute_order_status(bool $paid, bool $shipped, bool $cancelled): string
    {
        if ($cancelled) {
            return 'CANCELLED';
        }

        if ($paid && $shipped) {
            return 'SHIPPED';
        }

        if ($paid) {
            return 'CONFIRMED';
        }

        return 'PENDING';
    }

    public function calculate_shipping_cost(float $total): float
    {
        if ($total >= self::FREE_SHIPPING_THRESHOLD) {
            return 0.00;
        }
        return self::SHIPPING_COST;
    }
}
```

---

## Détail bug par bug

### 1. `test_calculate_order_total`

**Bug :** `$total += $item['price']` — la quantité n'est pas prise en compte.

**Fix :**
```php
$total += $item['price'] * $item['quantity'];
```

**Exemple :** 2×12,90 + 3×14,50 + 1×8,00 = **77,30 €**

---

### 2. `test_apply_discount`

**Bug :** `round($total + $discount, 2)` — la remise augmente le total.

**Fix :**
```php
return round($total - $discount, 2);
```

**Exemple :** 10 % sur 77,30 € → **69,57 €**

---

### 3. `test_reject_order_with_out_of_stock_item`

**Bug :** la boucle ne lève jamais d'exception (simple `continue`).

**Fix :**
```php
if ($item['quantity'] > $item['stock']) {
    throw new \InvalidArgumentException(
        'Out of stock: ' . $item['name'] . ' (requested ' . $item['quantity'] . ', available ' . $item['stock'] . ')'
    );
}
```

> PHPUnit 12 : `expectExceptionMessage('Out of stock')` fait un match partiel.

---

### 4. `test_compute_order_status`

**Bug :** statuts mélangés — annulation retourne `PENDING`, payé+expédié retourne `CONFIRMED`, etc.

**Logique correcte (par priorité) :**

| paid | shipped | cancelled | Statut |
|------|---------|-----------|--------|
| * | * | true | `CANCELLED` |
| true | true | false | `SHIPPED` |
| true | false | false | `CONFIRMED` |
| false | * | false | `PENDING` |

---

### 5. `test_calculate_shipping_cost`

**Bug :** `$total > 100` — une commande à **exactement 100 €** paie encore 5,90 € de port.

**Fix :**
```php
if ($total >= self::FREE_SHIPPING_THRESHOLD) {
    return 0.00;
}
```

| Total | Frais |
|-------|-------|
| 120 € | 0 € |
| 100 € | 0 € |
| 99,99 € | 5,90 € |
| 77,30 € | 5,90 € |

---

## Vérification

```bash
cd apps/laravel
php artisan test --filter=OrderTest
```

Résultat attendu : **5 tests, 14 assertions, tous au vert**.

---

## Parcours candidat (ordre suggéré)

1. `calculate_order_total` — bug le plus visible
2. `apply_discount` — symétrie avec le total
3. `calculate_shipping_cost` — cas limite à 100 €
4. `compute_order_status` — table de vérité
5. `reject_order_with_out_of_stock_item` — exception métier

---

## Critères d'évaluation

| Critère | Attendu |
|---------|---------|
| Tests OrderTest | 5/5 |
| Précision monétaire | `round(..., 2)` |
| Cas limites | 100 € port gratuit, remise 0 % / 100 % |
| Gestion stock | Exception avec message explicite |
| Durée Senior (7 ans) | 15–25 min |
