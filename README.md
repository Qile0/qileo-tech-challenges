# Qileo Tech Challenge — Aymen

> Senior · 7 ans · Apps: flutter

## 🎯 Challenges

**flutter**: Payment BLoC — Gestion des paiements fintech via BLoC : soumission, validation, détection de doublons et reset d'état.

## 🧪 Tests à faire passer

### flutter — Payment BLoC
1. `emits_loading_then_success_on_payment_submitted()` — Émet Loading puis Success
2. `emits_failure_on_invalid_amount()` — Émet Failure si montant invalide
3. `emits_failure_on_insufficient_balance()` — Émet Failure si solde insuffisant
4. `does_not_emit_on_duplicate_transaction()` — Pas de Success en double
5. `resets_to_initial_state_on_reset_event()` — Reset remet l'état initial

## 🚀 Démarrage

```bash
cd apps/flutter
flutter pub get
flutter test
```

## ⚠️ Règles

- ✅ Corrigez les fichiers buggés pour faire passer les tests
- ❌ Ne modifiez pas les fichiers de test
- ❌ Ne supprimez pas les assertions existantes

---
*Qileo Tech Challenges Platform*
