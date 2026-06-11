import 'package:flutter/material.dart';

class Transaction {
  final String id;
  final String label;
  final double amount;
  final String status;

  const Transaction({
    required this.id,
    required this.label,
    required this.amount,
    required this.status,
  });
}

abstract class TransactionRepository {
  Future<List<Transaction>> fetchTransactions();
}

class TransactionListWidget extends StatefulWidget {
  final TransactionRepository repository;

  const TransactionListWidget({super.key, required this.repository});

  @override
  State<TransactionListWidget> createState() => _TransactionListWidgetState();
}

class _TransactionListWidgetState extends State<TransactionListWidget> {
  List<Transaction> _allTransactions = [];
  List<Transaction> _filtered = [];
  bool _loading = true;
  String? _error;
  String _activeFilter = 'ALL';

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final data = await widget.repository.fetchTransactions();
      setState(() {
        _allTransactions = data;
        _filtered = data;
      });
    } catch (e) {
      setState(() {
        _error = 'Une erreur est survenue';
      });
    }
  }

  void _applyFilter(String status) {
    setState(() {
      _activeFilter = status;
      if (status == 'ALL') {
        _filtered = _allTransactions;
      } else {
        _filtered = _allTransactions
            .where((tx) => tx.status == status)
            .toList();
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    if (_error != null) {
      return Scaffold(
        body: Center(child: Text(_error!)),
      );
    }

    if (_filtered.isEmpty) {
      return Scaffold(
        body: Column(
          children: [
            _buildFilterBar(),
            const Expanded(
              child: Center(child: Text('Aucune transaction')),
            ),
          ],
        ),
      );
    }

    return Scaffold(
      body: Column(
        children: [
          _buildFilterBar(),
          Expanded(
            child: ListView(
              children: _filtered
                  .map((tx) => ListTile(
                        title: Text(tx.label),
                        subtitle: Text(tx.status),
                        trailing: Text('${tx.amount.toStringAsFixed(2)} €'),
                      ))
                  .toList(),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFilterBar() {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: ['ALL', 'COMPLETED', 'PENDING'].map((f) {
        return TextButton(
          onPressed: () => _applyFilter(f),
          child: Text(f),
        );
      }).toList(),
    );
  }
}