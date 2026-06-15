import 'dart:async';
import 'package:flutter/material.dart';

class LiveBalanceWidget extends StatefulWidget {
  final double initialBalance;
  final Stream<double> balanceStream;

  const LiveBalanceWidget({
    super.key,
    required this.initialBalance,
    required this.balanceStream,
  });

  @override
  State<LiveBalanceWidget> createState() => _LiveBalanceWidgetState();
}

class _LiveBalanceWidgetState extends State<LiveBalanceWidget> {
  static const _debounceDuration = Duration(milliseconds: 300);

  late double _currentBalance;
  double? _previousBalance;
  StreamSubscription<double>? _subscription;
  Timer? _debounceTimer;

  @override
  void initState() {
    super.initState();
    _currentBalance = widget.initialBalance;

    _subscription = widget.balanceStream.listen((newBalance) {
      setState(() {
        _previousBalance = _currentBalance;
        _currentBalance = newBalance;
      });
    });
  }

  @override
  void dispose() {
    _subscription?.cancel();
    _debounceTimer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final variation = _previousBalance != null
        ? _currentBalance - _previousBalance!
        : null;

    return Scaffold(
      body: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              '€ ${_currentBalance.toStringAsFixed(2)}',
              style: const TextStyle(fontSize: 32, fontWeight: FontWeight.bold),
            ),
            if (variation != null)
              Text(
                '${variation.toStringAsFixed(2)} €',
                style: TextStyle(
                  fontSize: 16,
                  color: variation > 0 ? Colors.red : Colors.green,
                ),
              ),
          ],
        ),
      ),
    );
  }
}
