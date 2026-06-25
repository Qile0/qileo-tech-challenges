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
flutter test test/payment_bloc_test.dart
```

## ⚠️ Règles

- ✅ Implémentez `lib/bloc/payment_bloc.dart` pour faire passer les tests (approche TDD — tous rouges au départ)
- ❌ Ne modifiez pas les fichiers de test
- ❌ Ne supprimez pas les assertions existantes

---
*Qileo Tech Challenges Platform*
