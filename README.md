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
