# Quizz React

> Durée estimée : 30 min  
> Répondez librement, il n'y a pas de piège.

---

## Partie 1 — useState et rendu

**Q1.** Que va afficher ce composant quand on clique sur le bouton ? Expliquez pourquoi.

```jsx
function Counter() {
  const [count, setCount] = useState(0);

  function handleClick() {
    setCount(count + 1);
    setCount(count + 1);
  }

  return <button onClick={handleClick}>{count}</button>;
}
```

---

**Q2.** Ce code a un problème. Lequel, et comment le corrigez-vous ?

```jsx
function App() {
  const [user, setUser] = useState({ name: 'Alice', age: 25 });

  function birthday() {
    user.age = user.age + 1;
    setUser(user);
  }

  return <button onClick={birthday}>{user.age}</button>;
}
```

---

**Q3.** Quelle est la différence entre ces deux façons de mettre à jour le state ?

```jsx
// A
setCount(count + 1);

// B
setCount(prev => prev + 1);
```

Dans quel cas la forme B est-elle nécessaire ?

---

## Partie 2 — useEffect

**Q4.** Expliquez ce que fait ce `useEffect` et quand il s'exécute.

```jsx
useEffect(() => {
  console.log('userId changed:', userId);
}, [userId]);
```

Que se passerait-il si le tableau de dépendances était vide `[]` ? Et s'il était absent ?

---

**Q5.** Ce code peut causer un problème en production. Lequel ?

```jsx
useEffect(() => {
  const interval = setInterval(() => {
    setCount(c => c + 1);
  }, 1000);
}, []);
```

Comment le corrigez-vous ?

---

**Q6.** Ce composant fetch des données. Quel bug peut apparaître si l'utilisateur change d'onglet rapidement ? Comment le corriger ?

```jsx
function UserProfile({ userId }) {
  const [user, setUser] = useState(null);

  useEffect(() => {
    fetch(`/api/user/${userId}`)
      .then(res => res.json())
      .then(data => setUser(data));
  }, [userId]);

  return <p>{user?.name}</p>;
}
```

---

## Partie 3 — Custom Hooks

**Q7.** Qu'est-ce qu'un custom hook ? Pourquoi doit-il commencer par `use` ?

---

**Q8.** Comment organiseriez-vous ce composant pour rendre la logique de fetch réutilisable dans d'autres composants ?

```jsx
function UserProfile({ userId }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    fetch(`/api/user/${userId}`)
      .then(res => res.json())
      .then(data => { setUser(data); setLoading(false); })
      .catch(err => { setError(err); setLoading(false); });
  }, [userId]);

  if (loading) return <p>Chargement...</p>;
  if (error) return <p>Erreur</p>;
  return <p>{user.name}</p>;
}
```

---

## Partie 4 — useContext

**Q9.** À quoi sert `useContext` ? Donnez un exemple de donnée qu'il est pertinent de mettre dans un Context.

---

**Q10.** Ce code ne fonctionne pas comme prévu. Pourquoi ?

```jsx
const ThemeContext = createContext('light');

function App() {
  return (
    <ThemeContext value="dark">
      <Page />
    </ThemeContext>
  );
}

function Page() {
  const theme = useContext(ThemeContext);
  return <p>Theme: {theme}</p>;
}
```

---

## Partie 5 — Performance

**Q11.** Quelle est la différence entre `useMemo` et `useCallback` ? Donnez un exemple concret pour chacun.

---

**Q12.** Ce composant se re-rend trop souvent. Pourquoi, et comment l'optimiser ?

```jsx
function Parent() {
  const [count, setCount] = useState(0);

  const handleClick = () => {
    console.log('clicked');
  };

  return (
    <>
      <button onClick={() => setCount(c => c + 1)}>+</button>
      <Child onClick={handleClick} />
    </>
  );
}

const Child = React.memo(({ onClick }) => {
  console.log('Child rendered');
  return <button onClick={onClick}>Action</button>;
});
```

---

*Qileo Tech Interview*
