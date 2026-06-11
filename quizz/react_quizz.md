# Quizz React

> Durée estimée : 30 min  
> Répondez librement et **justifiez** vos réponses.

---

## Partie 1 — useState et rendu

**Q1.** L'utilisateur clique **3 fois rapidement** sur le bouton, puis attend 2 secondes. Quelle valeur de `count` s'affiche ? Expliquez.

```jsx
function Counter() {
  const [count, setCount] = useState(0);

  function handleClick() {
    setTimeout(() => {
      setCount(count + 1);
    }, 2000);
  }

  return <button onClick={handleClick}>{count}</button>;
}
```

---

**Q2.** Ce composant n'ajoute pas d'élément visible à l'écran après un clic. Pourquoi ? Comment corriger ?

```jsx
function TodoList() {
  const [todos, setTodos] = useState(['Appeler le client', 'Envoyer le devis']);

  function addTodo() {
    todos.push('Relancer le prospect');
    setTodos(todos);
  }

  return (
    <>
      <button onClick={addTodo}>Ajouter</button>
      <ul>{todos.map(t => <li key={t}>{t}</li>)}</ul>
    </>
  );
}
```

---

**Q3.** Vous devez incrémenter un compteur **5 fois d'un coup** dans un seul handler (sans boucle `for` sur `setCount` direct). Quelle forme de `setCount` utilisez-vous et pourquoi ?

---

## Partie 2 — useEffect

**Q4.** À quels moments ce code met-il à jour le titre de l'onglet du navigateur ?

```jsx
function Wizard({ step }) {
  useEffect(() => {
    document.title = `Étape ${step} sur 4`;
  }, [step]);

  return <div>...</div>;
}
```

Que se passe-t-il si on retire le tableau de dépendances ?

---

**Q5.** Quel problème ce code peut-il causer, et comment le corriger ?

```jsx
function Layout() {
  const [width, setWidth] = useState(window.innerWidth);

  useEffect(() => {
    const handleResize = () => setWidth(window.innerWidth);
    window.addEventListener('resize', handleResize);
  }, []);

  return <p>Largeur : {width}px</p>;
}
```

---

**Q6.** Un champ de recherche déclenche un fetch à chaque frappe. Expliquez le bug possible si l'utilisateur tape vite « react » et comment l'éviter.

```jsx
function Search() {
  const [query, setQuery] = useState('');
  const [results, setResults] = useState([]);

  useEffect(() => {
    if (!query) return;
    fetch(`/api/search?q=${query}`)
      .then(res => res.json())
      .then(data => setResults(data));
  }, [query]);

  return (
    <>
      <input value={query} onChange={e => setQuery(e.target.value)} />
      <ul>{results.map(r => <li key={r.id}>{r.label}</li>)}</ul>
    </>
  );
}
```

---

## Partie 3 — Custom Hooks & architecture

**Q7.** Citez **deux règles** des Hooks React et expliquez pourquoi un custom hook doit commencer par `use`.

---

**Q8.** Vous voyez cette logique dans plusieurs écrans. Comment la factoriser ? Que retourne votre hook ?

```jsx
function SettingsPage() {
  const [value, setValue] = useState(() => localStorage.getItem('theme') ?? 'light');

  useEffect(() => {
    localStorage.setItem('theme', value);
  }, [value]);

  // ...
}
```

---

**Q9.** Quelle est la différence entre **lever la logique dans un custom hook** et **mettre la donnée dans un Context** ? Dans quels cas préférez-vous l'un ou l'autre ?

---

## Partie 4 — useContext & listes

**Q10.** Ce code affiche toujours `Locale: fr` alors que le bouton devrait basculer en `en`. Expliquez le bug.

```jsx
const LocaleContext = createContext('fr');

function App() {
  const [locale, setLocale] = useState('fr');

  return (
    <LocaleContext value={{ locale, setLocale }}>
      <Toolbar />
    </LocaleContext>
  );
}

function Toolbar() {
  const { locale, setLocale } = useContext(LocaleContext);
  return (
    <button onClick={() => setLocale(l => (l === 'fr' ? 'en' : 'fr'))}>
      Locale: {locale}
    </button>
  );
}
```

---

**Q11.** On supprime le premier élément d'une liste triée. Pourquoi utiliser l'**index** comme `key` peut produire un comportement incorrect (valeurs d'input, focus, animations) ?

```jsx
{rows.map((row, index) => (
  <input key={index} defaultValue={row.label} />
))}
```

---

## Partie 5 — Performance & useRef

**Q12.** `ExpensiveChild` se re-rend à chaque frappe dans le champ texte, malgré `React.memo`. Pourquoi ? Proposez une correction.

```jsx
const ExpensiveChild = React.memo(function ExpensiveChild({ config }) {
  console.log('render child');
  return <p>{config.label}</p>;
});

function Form() {
  const [name, setName] = useState('');
  const [count, setCount] = useState(0);

  return (
    <>
      <input value={name} onChange={e => setName(e.target.value)} />
      <button onClick={() => setCount(c => c + 1)}>+</button>
      <ExpensiveChild config={{ label: `Clics : ${count}` }} />
    </>
  );
}
```

---

**Q13.** Pourquoi stocke-t-on la **valeur précédente** d'une prop dans un `useRef` plutôt que dans un `useState` ?

```jsx
function Chat({ messageId }) {
  const prevId = useRef(messageId);

  useEffect(() => {
    if (prevId.current !== messageId) {
      scrollToBottom();
      prevId.current = messageId;
    }
  }, [messageId]);
}
```

---

**Q14.** Sans écrire de code : dans quels cas `useMemo` **n'apporte rien** ou peut même être contre-productif ?

---

*Qileo Tech Interview*
