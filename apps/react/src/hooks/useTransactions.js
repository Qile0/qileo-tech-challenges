import { useState, useEffect, useRef } from 'react';

export function useTransactions(url) {
  const [allTransactions, setAllTransactions] = useState([]);
  const [transactions, setTransactions]       = useState([]);
  const [loading, setLoading]                 = useState(true);
  const [error, setError]                     = useState(null);
  const currentFilter                         = useRef(null);

  useEffect(() => {
    fetch(url)
      .then(res => res.json())
      .then(data => {
        setAllTransactions(data);
        setTransactions(data);
        setLoading(false);
      })
      .catch(err => {
        setError(err.message);
      });
  }, [url]);

  function filterByStatus(status) {
    if (status === currentFilter.current) {
      return;
    }

    currentFilter.current = status;

    if (!status) {
      setTransactions(allTransactions);
      return;
    }

    const filtered = allTransactions.filter(tx => tx.status === status);
    setTransactions(filtered);
  }

  return { transactions, loading, error, filterByStatus };
}
