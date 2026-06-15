import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:qileo_challenge/live_balance.dart';

void main() {
  testWidgets('shows_initial_balance', (tester) async {
    final controller = StreamController<double>();

    await tester.pumpWidget(MaterialApp(
      home: LiveBalanceWidget(
        initialBalance: 1500.00,
        balanceStream: controller.stream,
      ),
    ));

    expect(find.text('1500.00 €'), findsOneWidget);

    await controller.close();
  });

  testWidgets('updates_balance_on_stream_event', (tester) async {
    final controller = StreamController<double>();

    await tester.pumpWidget(MaterialApp(
      home: LiveBalanceWidget(
        initialBalance: 1000.00,
        balanceStream: controller.stream,
      ),
    ));

    expect(find.text('1000.00 €'), findsOneWidget);

    controller.add(1250.00);
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 350));

    expect(find.text('1250.00 €'), findsOneWidget);
    expect(find.text('1000.00 €'), findsNothing);

    await controller.close();
  });

  testWidgets('shows_positive_variation_in_green', (tester) async {
    final controller = StreamController<double>();

    await tester.pumpWidget(MaterialApp(
      home: LiveBalanceWidget(
        initialBalance: 1000.00,
        balanceStream: controller.stream,
      ),
    ));

    controller.add(1250.50);
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 350));

    expect(find.text('+250.50 €'), findsOneWidget);

    final variationText = tester.widget<Text>(find.text('+250.50 €'));
    expect(variationText.style?.color, Colors.green);

    await controller.close();
  });

  testWidgets('shows_negative_variation_in_red', (tester) async {
    final controller = StreamController<double>();

    await tester.pumpWidget(MaterialApp(
      home: LiveBalanceWidget(
        initialBalance: 1000.00,
        balanceStream: controller.stream,
      ),
    ));

    controller.add(820.00);
    await tester.pump();
    await tester.pump(const Duration(milliseconds: 350));

    expect(find.text('-180.00 €'), findsOneWidget);

    final variationText = tester.widget<Text>(find.text('-180.00 €'));
    expect(variationText.style?.color, Colors.red);

    await controller.close();
  });

  testWidgets('debounces_rapid_stream_updates', (tester) async {
    final controller = StreamController<double>();

    await tester.pumpWidget(MaterialApp(
      home: LiveBalanceWidget(
        initialBalance: 1000.00,
        balanceStream: controller.stream,
      ),
    ));

    // 3 émissions rapprochées (< 300ms entre chacune)
    controller.add(1100.00);
    await tester.pump(const Duration(milliseconds: 50));

    controller.add(1200.00);
    await tester.pump(const Duration(milliseconds: 50));

    controller.add(1300.00);
    await tester.pump(const Duration(milliseconds: 50));

    // Avant la fin du debounce, l'ancien solde est toujours affiché
    expect(find.text('1000.00 €'), findsOneWidget);

    // Après le délai de debounce (300ms), seule la dernière valeur est affichée
    await tester.pump(const Duration(milliseconds: 350));

    expect(find.text('1300.00 €'), findsOneWidget);
    expect(find.text('1100.00 €'), findsNothing);
    expect(find.text('1200.00 €'), findsNothing);

    await controller.close();
  });
}