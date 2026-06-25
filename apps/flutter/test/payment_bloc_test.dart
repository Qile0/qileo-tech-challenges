import 'package:flutter_test/flutter_test.dart';
import 'package:qileo_challenge/payment_bloc.dart';

void main() {
  late PaymentBloc bloc;

  setUp(() {
    bloc = PaymentBloc();
  });

  tearDown(() {
    bloc.close();
  });

  test('emits_loading_then_success_on_payment_submitted', () async {
    final states = <PaymentState>[];
    bloc.stream.listen(states.add);

    bloc.add(PaymentSubmitted(
      transactionId: 'TX-001',
      amount: 250.00,
      balance: 1000.00,
      label: 'Virement SFR',
    ));

    await Future.delayed(const Duration(milliseconds: 100));

    expect(states.length, 2);
    expect(states[0], isA<PaymentLoading>());
    expect(states[1], isA<PaymentSuccess>());

    final success = states[1] as PaymentSuccess;
    expect(success.transactionId, 'TX-001');
    expect(success.newBalance, 750.00);
  });

  test('emits_failure_on_invalid_amount', () async {
    final states = <PaymentState>[];
    bloc.stream.listen(states.add);

    bloc.add(PaymentSubmitted(
      transactionId: 'TX-002',
      amount: -50.00,
      balance: 1000.00,
      label: 'Montant invalide',
    ));

    await Future.delayed(const Duration(milliseconds: 100));

    expect(states.length, 2);
    expect(states[0], isA<PaymentLoading>());
    expect(states[1], isA<PaymentFailure>());

    final failure = states[1] as PaymentFailure;
    expect(failure.reason, 'INVALID_AMOUNT');

    // Montant nul
    final bloc2 = PaymentBloc();
    final states2 = <PaymentState>[];
    bloc2.stream.listen(states2.add);

    bloc2.add(PaymentSubmitted(
      transactionId: 'TX-003',
      amount: 0.00,
      balance: 1000.00,
      label: 'Montant nul',
    ));

    await Future.delayed(const Duration(milliseconds: 100));
    expect(states2[1], isA<PaymentFailure>());
    expect((states2[1] as PaymentFailure).reason, 'INVALID_AMOUNT');
    bloc2.close();
  });

  test('emits_failure_on_insufficient_balance', () async {
    final states = <PaymentState>[];
    bloc.stream.listen(states.add);

    bloc.add(PaymentSubmitted(
      transactionId: 'TX-004',
      amount: 1500.00,
      balance: 1000.00,
      label: 'Dépassement solde',
    ));

    await Future.delayed(const Duration(milliseconds: 100));

    expect(states.length, 2);
    expect(states[0], isA<PaymentLoading>());
    expect(states[1], isA<PaymentFailure>());

    final failure = states[1] as PaymentFailure;
    expect(failure.reason, 'INSUFFICIENT_BALANCE');
  });

  test('does_not_emit_on_duplicate_transaction', () async {
    final states = <PaymentState>[];
    bloc.stream.listen(states.add);

    // Premier paiement
    bloc.add(PaymentSubmitted(
      transactionId: 'TX-005',
      amount: 100.00,
      balance: 1000.00,
      label: 'Premier paiement',
    ));

    await Future.delayed(const Duration(milliseconds: 100));

    final countAfterFirst = states.length;

    // Même transactionId
    bloc.add(PaymentSubmitted(
      transactionId: 'TX-005',
      amount: 100.00,
      balance: 900.00,
      label: 'Doublon',
    ));

    await Future.delayed(const Duration(milliseconds: 100));

    // Aucun nouvel état émis pour le doublon
    expect(states.length, countAfterFirst);
    expect(states.where((s) => s is PaymentSuccess).length, 1);
  });

  test('resets_to_initial_state_on_reset_event', () async {
    final states = <PaymentState>[];
    bloc.stream.listen(states.add);

    // Soumettre un paiement
    bloc.add(PaymentSubmitted(
      transactionId: 'TX-006',
      amount: 200.00,
      balance: 1000.00,
      label: 'Paiement avant reset',
    ));

    await Future.delayed(const Duration(milliseconds: 100));
    expect(states.last, isA<PaymentSuccess>());

    // Reset
    bloc.add(PaymentReset());
    await Future.delayed(const Duration(milliseconds: 50));

    expect(bloc.state, isA<PaymentInitial>());
  });
}