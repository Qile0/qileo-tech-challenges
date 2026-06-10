# Qileo Tech Challenge — Haritiana

> Senior · 9 ans · Apps: laravel

## 🎯 Challenges

**laravel**: KYC Verification — Vérification d'identité et scoring de risque dans un contexte fintech : validation de documents, calcul du niveau de risque et décision KYC.

## 🧪 Tests à faire passer

### laravel — KYC Verification
1. `validate_identity_document()` — Valide un document d'identité
2. `rejects_expired_document()` — Rejette un document expiré
3. `compute_kyc_risk_level()` — Calcule le niveau de risque
4. `approve_kyc_when_all_checks_pass()` — Approuve si tout est valide
5. `reject_kyc_on_sanctioned_country()` — Rejette un pays sanctionné

## 🚀 Démarrage

### Option A — Dev Container (recommandé)

Prérequis : [Docker Desktop](https://www.docker.com/products/docker-desktop/) + [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) (VS Code / Cursor).

1. Ouvrir ce repo dans VS Code ou Cursor
2. `Cmd/Ctrl + Shift + P` → **Dev Containers: Reopen in Container**
3. Attendre la fin de `composer install` (automatique au premier lancement)

```bash
cd apps/laravel
php artisan test --filter=KycTest   # lancer les tests du challenge
php artisan serve                 # app sur http://localhost:8000
```

### Option B — Installation locale

Prérequis : PHP 8.4+, Composer

```bash
cd apps/laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan test --filter=KycTest
```

## ⚠️ Règles

- ✅ Corrigez les fichiers buggés pour faire passer les tests
- ❌ Ne modifiez pas les fichiers de test
- ❌ Ne supprimez pas les assertions existantes

---
*Qileo Tech Challenges Platform*
