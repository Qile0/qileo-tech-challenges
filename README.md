# Qileo Tech Challenge — Pierre

> Senior · 5 ans · Apps: flutter

## 🎯 Challenges

**flutter**: Live Balance Widget — Widget affichant le solde d'un compte fintech en temps réel via un flux de données, avec calcul de variation et debounce des mises à jour rapprochées.

## 🧪 Tests à faire passer

### flutter — Live Balance Widget
1. `shows_initial_balance()` — Affiche le solde initial
2. `updates_balance_on_stream_event()` — Met à jour le solde sur événement stream
3. `shows_positive_variation_in_green()` — Variation positive en vert avec +
4. `shows_negative_variation_in_red()` — Variation négative en rouge avec -
5. `debounces_rapid_stream_updates()` — Debounce des mises à jour rapprochées

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
