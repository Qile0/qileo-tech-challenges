import 'package:flutter/material.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:qileo_challenge/transaction_list.dart';

class FakeTransactionRepository implements TransactionRepository {
  final List<Transaction> _transactions;
  final bool shouldFail;

  FakeTransactionRepository({
    List<Transaction>? transactions,
    this.shouldFail = false,
  }) : _transactions = transactions ?? [];

  @override
  Future<List<Transaction>> fetchTransactions() async {
    await Future.delayed(const Duration(milliseconds: 50));
    if (shouldFail) throw Exception('Network error');
    return _transactions;
  }
}

final mockTransactions = [
  Transaction(id: 'TX001', label: 'Virement SFR',       amount: 29.99,  status: 'COMPLETED'),
  Transaction(id: 'TX002', label: 'Salaire juin',        amount: 2400.00, status: 'COMPLETED'),
  Transaction(id: 'TX003', label: 'Abonnement Netflix',  amount: 13.49,  status: 'PENDING'),
  Transaction(id: 'TX004', label: 'Remboursement Alice', amount: 50.00,  status: 'PENDING'),
];

void main() {
  testWidgets('shows_loading_indicator_while_fetching', (tester) async {
    final repo = FakeTransactionRepository(transactions: mockTransactions);

    await tester.pumpWidget(MaterialApp(
      home: TransactionListWidget(repository: repo),
    ));

    expect(find.byType(CircularProgressIndicator), findsOneWidget);
    expect(find.byType(ListTile), findsNothing);

    await tester.pumpAndSettle();
  });

  testWidgets('displays_transactions_after_load', (tester) async {
    final repo = FakeTransactionRepository(transactions: mockTransactions);

    await tester.pumpWidget(MaterialApp(
      home: TransactionListWidget(repository: repo),
    ));

    await tester.pumpAndSettle();

    expect(find.byType(CircularProgressIndicator), findsNothing);
    expect(find.byType(ListTile), findsNWidgets(4));
    expect(find.text('Virement SFR'), findsOneWidget);
    expect(find.text('Salaire juin'), findsOneWidget);
  });

  testWidgets('shows_error_message_on_failure', (tester) async {
    final repo = FakeTransactionRepository(shouldFail: true);

    await tester.pumpWidget(MaterialApp(
      home: TransactionListWidget(repository: repo),
    ));

    await tester.pumpAndSettle();

    expect(find.byType(CircularProgressIndicator), findsNothing);
    expect(find.byType(ListTile), findsNothing);
    expect(find.text('Une erreur est survenue'), findsOneWidget);
  });

  testWidgets('filters_transactions_by_status', (tester) async {
    final repo = FakeTransactionRepository(transactions: mockTransactions);

    await tester.pumpWidget(MaterialApp(
      home: TransactionListWidget(repository: repo),
    ));

    await tester.pumpAndSettle();

    // Tap sur le filtre PENDING
    await tester.tap(find.text('PENDING'));
    await tester.pump();

    expect(find.byType(ListTile), findsNWidgets(2));
    expect(find.text('Abonnement Netflix'), findsOneWidget);
    expect(find.text('Remboursement Alice'), findsOneWidget);
    expect(find.text('Salaire juin'), findsNothing);

    // Tap sur ALL pour réinitialiser
    await tester.tap(find.text('ALL'));
    await tester.pump();

    expect(find.byType(ListTile), findsNWidgets(4));
  });

  testWidgets('shows_empty_state_when_no_transactions', (tester) async {
    final repo = FakeTransactionRepository(transactions: []);

    await tester.pumpWidget(MaterialApp(
      home: TransactionListWidget(repository: repo),
    ));

    await tester.pumpAndSettle();

    expect(find.byType(ListTile), findsNothing);
    expect(find.text('Aucune transaction'), findsOneWidget);
  });
}