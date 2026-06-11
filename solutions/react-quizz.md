# Corrigé — Quizz React

> Fichier réservé aux évaluateurs. Ne pas partager avec les candidats.

## Barème indicatif

| Niveau | Score | Profil |
|--------|-------|--------|
| Junior confirmé | 7–9 / 14 | Bases OK, lacunes closure / cleanup |
| Confirmé / Senior | 10–12 / 14 | Réponses structurées avec exemples |
| Lead | 13–14 / 14 | Nuances (AbortController, trade-offs memo, Context) |

---

## Partie 1 — useState

### Q1 — Closure stale dans setTimeout

**Réponse attendue :** affiche **1** (pas 3).

Chaque `setTimeout` capture la valeur de `count` **au moment du clic** (0). Trois clics → trois callbacks avec `count === 0` → trois fois `setCount(1)`.

**Correction :** updater fonctionnel dans le timeout :
```jsx
setCount(prev => prev + 1);
```

**Bonus :** même problème dans les fetch, les abonnements, les event handlers async.

---

### Q2 — Mutation du tableau en state

**Problème :** `todos.push(...)` mute le tableau existant ; `setTodos(todos)` passe la **même référence** → React peut ignorer la mise à jour.

**Correction :**
```jsx
setTodos([...todos, 'Relancer le prospect']);
// ou setTodos(prev => [...prev, 'Relancer le prospect']);
```

---

### Q3 — Cinq incréments d'un coup

**Réponse attendue :** enchaîner des updaters fonctionnels :
```jsx
setCount(prev => prev + 1);
setCount(prev => prev + 1);
setCount(prev => prev + 1);
setCount(prev => prev + 1);
setCount(prev => prev + 1);
```
Ou une seule fois : `setCount(prev => prev + 5)`.

La forme `setCount(count + 1)` répétée 5 fois ne fonctionne pas (même valeur capturée).

---

## Partie 2 — useEffect

### Q4 — Titre document + dépendances

**Exécution :** au montage, puis **à chaque changement de `step`**.

**Sans tableau de dépendances :** à **chaque render** → titre mis à jour en boucle si autre state change ; coût inutile.

---

### Q5 — Event listener sans cleanup

**Problème :** listener jamais retiré → fuite, callbacks multiples après remount (Strict Mode), updates sur composant démonté.

**Correction :**
```jsx
useEffect(() => {
  const handleResize = () => setWidth(window.innerWidth);
  window.addEventListener('resize', handleResize);
  return () => window.removeEventListener('resize', handleResize);
}, []);
```

---

### Q6 — Race condition recherche

**Problème :** réponse de `q=re` peut arriver **après** `q=react` → résultats incohérents.

**Corrections acceptées :**
- flag `cancelled` / `ignore` dans cleanup
- `AbortController`
- debounce sur `query`
- librairie (TanStack Query, SWR, etc.)

---

## Partie 3 — Custom Hooks & architecture

### Q7 — Règles des Hooks

**Règles (2 parmi) :**
- appeler les hooks uniquement au **top level** (pas dans if/boucles)
- appeler les hooks uniquement depuis des **composants React** ou d'autres **custom hooks**

**Préfixe `use` :** convention + lint (`eslint-plugin-react-hooks`) pour appliquer les règles.

---

### Q8 — Factorisation localStorage

**Hook typique :** `useLocalStorage('theme', 'light')`

**Retour :** `[value, setValue]` (tuple type useState), synchronisation read/write dans le hook.

**Bonus :** gestion SSR (`typeof window`), sérialisation JSON, événement `storage` pour onglets multiples.

---

### Q9 — Hook vs Context

| Custom hook | Context |
|-------------|---------|
| Réutilise logique **par composant** | Partage une valeur à **tout un sous-arbre** |
| Chaque instance a son propre state | Une source unique pour plusieurs consommateurs |

**Hook seul :** formulaire, fetch local, préférence locale.  
**Context :** thème, auth session, i18n globale.  
**Les deux :** `AuthProvider` expose un hook `useAuth()` qui wrappe le Context.

---

## Partie 4 — Context & listes

### Q10 — Provider manquant

**Bug :** il faut `<LocaleContext.Provider value={...}>`, pas `<LocaleContext value={...}>`.

Sans `.Provider`, React ignore la prop `value` → consommateur reçoit la valeur par défaut `'fr'`.  
Erreur secondaire possible : destructurer `{ locale, setLocale }` sur la string `'fr'`.

---

### Q11 — key={index}

**Problème :** React réutilise les **instances DOM** par position. Supprimer le 1er élément décale les index : le 2e item réutilise le nœud du 1er → `defaultValue`, focus, state interne **restent associés à la mauvaise ligne**.

**Correction :** `key={row.id}` (identifiant stable).

---

## Partie 5 — Performance & useRef

### Q12 — Objet inline et memo

**Cause :** `config={{ label: ... }}` crée un **nouvel objet à chaque render** → référence différente → `React.memo` ne bloque pas (comparaison shallow).

**Corrections :**
```jsx
const config = useMemo(() => ({ label: `Clics : ${count}` }), [count]);
// ou passer label={...} directement comme prop primitive
```

**Note :** les re-renders sont déclenchés par le parent de toute façon ; memo ne sert que si le parent re-rend sans changer les props utiles.

---

### Q13 — useRef pour valeur précédente

**useRef :** mutation de `.current` **sans re-render**.

**useState :** chaque mise à jour déclencherait un render supplémentaire juste pour mémoriser l'ancienne valeur — inutile pour de la logique interne / comparaison dans un effet.

---

### Q14 — Quand useMemo est inutile

**Exemples acceptés :**
- calcul **trivial** (coût < re-render du memo lui-même)
- valeur **primitive** déjà cheap
- dépendances qui changent **à chaque render** → recalcul systématique
- micro-optimisation prématurée sans mesure
- mauvaise compréhension : useMemo ne remplace pas la mémoïsation de composant (`memo`)

**Bonus :** en React Compiler / certaines versions, le compilateur peut rendre certains useMemo redondants.

---

## Red flags

- Répond « 3 » à Q1 sans parler de closure
- Ne connaît pas le cleanup des listeners
- Propose Context pour tout état partagé
- Pense que `memo` suffit sans stabiliser les props objet/fonction
- Index comme key « parce que c'est plus simple » sans voir le risque

## Green flags

- AbortController ou debounce sur Q6
- Distingue hook (logique) et Context (portée arbre)
- Mentionne Strict Mode / double mount en bonus Q5
- Explique shallow compare de `memo` sur Q12
