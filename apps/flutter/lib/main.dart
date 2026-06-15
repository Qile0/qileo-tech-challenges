import 'dart:async';

import 'package:flutter/material.dart';
import 'package:qileo_challenge/live_balance.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Qileo Challenge',
      home: DemoLiveBalancePage(),
    );
  }
}

class DemoLiveBalancePage extends StatefulWidget {
  @override
  State<DemoLiveBalancePage> createState() => _DemoLiveBalancePageState();
}

class _DemoLiveBalancePageState extends State<DemoLiveBalancePage> {
  final _controller = StreamController<double>();
  double _balance = 1000.00;

  @override
  void dispose() {
    _controller.close();
    super.dispose();
  }

  void _emitBalance(double value) {
    setState(() => _balance = value);
    _controller.add(value);
  }

  @override
  Widget build(BuildContext context) {
    return LiveBalanceWidget(
      initialBalance: _balance,
      balanceStream: _controller.stream,
    );
  }
}
