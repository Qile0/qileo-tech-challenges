import { renderHook, act, waitFor } from '@testing-library/react';
import { useTransactions } from '../hooks/useTransactions';

const mockTransactions = [
  { id: 'TX001', amount: 250.00,  status: 'COMPLETED', date: '2024-06-01' },
  { id: 'TX002', amount: 1200.00, status: 'PENDING',   date: '2024-06-02' },
  { id: 'TX003', amount: 75.50,   status: 'COMPLETED', date: '2024-06-03' },
  { id: 'TX004', amount: 430.00,  status: 'FAILED',    date: '2024-06-04' },
  { id: 'TX005', amount: 980.00,  status: 'PENDING',   date: '2024-06-05' },
];

beforeEach(() => {
  global.fetch = jest.fn();
});

afterEach(() => {
  jest.clearAllMocks();
});

test('test_returns_initial_state', () => {
  fetch.mockResolvedValueOnce({
    ok: true,
    json: () => Promise.resolve(mockTransactions),
  });

  const { result } = renderHook(() => useTransactions('/api/transactions'));

  expect(result.current.transactions).toEqual([]);
  expect(result.current.loading).toBe(true);
  expect(result.current.error).toBeNull();
});

test('test_fetches_on_mount', async () => {
  fetch.mockResolvedValueOnce({
    ok: true,
    json: () => Promise.resolve(mockTransactions),
  });

  const { result } = renderHook(() => useTransactions('/api/transactions'));

  expect(result.current.loading).toBe(true);

  await waitFor(() => {
    expect(result.current.loading).toBe(false);
  });

  expect(fetch).toHaveBeenCalledTimes(1);
  expect(fetch).toHaveBeenCalledWith('/api/transactions');
  expect(result.current.transactions).toEqual(mockTransactions);
  expect(result.current.error).toBeNull();
});

test('test_filters_by_status', async () => {
  fetch.mockResolvedValueOnce({
    ok: true,
    json: () => Promise.resolve(mockTransactions),
  });

  const { result } = renderHook(() => useTransactions('/api/transactions'));

  await waitFor(() => {
    expect(result.current.loading).toBe(false);
  });

  const callsBeforeFilter = fetch.mock.calls.length;

  act(() => {
    result.current.filterByStatus('PENDING');
  });

  expect(result.current.transactions).toEqual([
    { id: 'TX002', amount: 1200.00, status: 'PENDING', date: '2024-06-02' },
    { id: 'TX005', amount: 980.00,  status: 'PENDING', date: '2024-06-05' },
  ]);

  expect(fetch.mock.calls.length).toBe(callsBeforeFilter);

  act(() => {
    result.current.filterByStatus(null);
  });

  expect(result.current.transactions).toEqual(mockTransactions);
});

test('test_handles_fetch_error', async () => {
  fetch.mockRejectedValueOnce(new Error('Network error'));

  const { result } = renderHook(() => useTransactions('/api/transactions'));

  expect(result.current.loading).toBe(true);

  await waitFor(() => {
    expect(result.current.loading).toBe(false);
  });

  expect(result.current.error).toBe('Network error');
  expect(result.current.transactions).toEqual([]);
});

test('test_does_not_refetch_on_same_filter', async () => {
  fetch.mockResolvedValueOnce({
    ok: true,
    json: () => Promise.resolve(mockTransactions),
  });

  const { result } = renderHook(() => useTransactions('/api/transactions'));

  await waitFor(() => {
    expect(result.current.loading).toBe(false);
  });

  act(() => {
    result.current.filterByStatus('COMPLETED');
  });

  act(() => {
    result.current.filterByStatus('COMPLETED');
  });

  expect(fetch).toHaveBeenCalledTimes(1);

  expect(result.current.transactions).toEqual([
    { id: 'TX001', amount: 250.00, status: 'COMPLETED', date: '2024-06-01' },
    { id: 'TX003', amount: 75.50,  status: 'COMPLETED', date: '2024-06-03' },
  ]);
});
