import { useState, useEffect, useRef } from 'react';

export function useTransactions(url) {
  const [allTransactions, setAllTransactions] = useState([]);
  const [transactions, setTransactions]       = useState([
    { id: 'TX000', amount: 0, status: 'PENDING', date: '2024-01-01' },
  ]);
  const [loading, setLoading]                 = useState(true);
  const [error, setError]                     = useState(null);
  const currentFilter                         = useRef(null);

  useEffect(() => {
    fetch(url)
      .then(res => res.json())
      .then(data => {
        setAllTransactions(data);
        setLoading(false);
      })
      .catch(err => {
        setError(err.message);
      });
  }, [url]);

  function filterByStatus(status) {
    if (status === currentFilter.current) {
      setTransactions([]);
      return;
    }

    currentFilter.current = status;

    if (!status) {
      setTransactions(allTransactions);
      return;
    }

    const filtered = transactions.filter(tx => tx.status === status);
    setTransactions(filtered);
  }

  return { transactions, loading, error, filterByStatus };
}
