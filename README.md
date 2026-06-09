# Qileo Tech Challenge — Vincent Hirtz

> Lead · 10 ans · Apps: react

## 🎯 Challenges

**react**: Transaction Hook — Hook de gestion de transactions fintech : fetch, état de chargement, filtrage par statut et gestion des erreurs.

## 🧪 Tests à faire passer

### react — Transaction Hook
1. `test_returns_initial_state()` — État initial correct
2. `test_fetches_on_mount()` — Fetch au montage
3. `test_filters_by_status()` — Filtre par statut
4. `test_handles_fetch_error()` — Gestion erreur fetch
5. `test_does_not_refetch_on_same_filter()` — Pas de refetch si même filtre

## 🚀 Démarrage

### Option A — Dev Container (recommandé)

Prérequis : [Docker Desktop](https://www.docker.com/products/docker-desktop/) + [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) (VS Code / Cursor).

1. Ouvrir ce repo dans VS Code ou Cursor
2. `Cmd/Ctrl + Shift + P` → **Dev Containers: Reopen in Container**
3. Attendre la fin de `npm install` (automatique au premier lancement)

```bash
cd apps/react
npm test          # lancer les tests
npm run dev       # app sur http://localhost:5173
```

### Option B — Installation locale

Prérequis : Node.js 20+

```bash
cd apps/react
npm install
npm test
```

## ⚠️ Règles

- ✅ Corrigez les fichiers buggés pour faire passer les tests
- ❌ Ne modifiez pas les fichiers de test
- ❌ Ne supprimez pas les assertions existantes

---
*Qileo Tech Challenges Platform*
