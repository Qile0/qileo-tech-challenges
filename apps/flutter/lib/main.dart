import 'package:flutter/material.dart';
import 'package:qileo_challenge/transaction_list.dart';

void main() {
  runApp(const QileoApp());
}

class QileoApp extends StatelessWidget {
  const QileoApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Qileo Challenge',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.indigo),
        useMaterial3: true,
      ),
      home: TransactionListWidget(repository: DemoTransactionRepository()),
    );
  }
}

class DemoTransactionRepository implements TransactionRepository {
  @override
  Future<List<Transaction>> fetchTransactions() async {
    await Future.delayed(const Duration(milliseconds: 500));
    return const [
      Transaction(
        id: 'TX001',
        label: 'Virement SFR',
        amount: 29.99,
        status: 'COMPLETED',
      ),
      Transaction(
        id: 'TX002',
        label: 'Salaire juin',
        amount: 2400.00,
        status: 'COMPLETED',
      ),
      Transaction(
        id: 'TX003',
        label: 'Abonnement Netflix',
        amount: 13.49,
        status: 'PENDING',
      ),
      Transaction(
        id: 'TX004',
        label: 'Remboursement Alice',
        amount: 50.00,
        status: 'PENDING',
      ),
    ];
  }
}
