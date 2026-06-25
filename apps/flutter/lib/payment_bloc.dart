import 'dart:async';

// ─── Events ───────────────────────────────────────────────────────────────────

abstract class PaymentEvent {}

class PaymentSubmitted extends PaymentEvent {
  final String transactionId;
  final double amount;
  final double balance;
  final String label;

  PaymentSubmitted({
    required this.transactionId,
    required this.amount,
    required this.balance,
    required this.label,
  });
}

class PaymentReset extends PaymentEvent {}

// ─── States ───────────────────────────────────────────────────────────────────

abstract class PaymentState {}

class PaymentInitial extends PaymentState {}

class PaymentLoading extends PaymentState {}

class PaymentSuccess extends PaymentState {
  final String transactionId;
  final double newBalance;

  PaymentSuccess({required this.transactionId, required this.newBalance});
}

class PaymentFailure extends PaymentState {
  final String reason;

  PaymentFailure({required this.reason});
}

// ─── BLoC ─────────────────────────────────────────────────────────────────────

class PaymentBloc {
  final _stateController = StreamController<PaymentState>.broadcast();
  final Set<String> _processedTransactions = {};

  PaymentState _state = PaymentInitial();

  Stream<PaymentState> get stream => _stateController.stream;
  PaymentState get state => _state;

  void add(PaymentEvent event) {
    if (event is PaymentSubmitted) {
      _handlePayment(event);
    } else if (event is PaymentReset) {
      _emit(PaymentInitial());
    }
  }

  void _handlePayment(PaymentSubmitted event) {
    if (_processedTransactions.contains(event.transactionId)) {
      return;
    }

    _emit(PaymentLoading());

    if (event.amount < 0) {
      _emit(PaymentFailure(reason: 'INVALID_AMOUNT'));
      return;
    }

    if (event.amount > event.balance) {
      _emit(PaymentFailure(reason: 'INSUFFICIENT_BALANCE'));
      return;
    }

    _processedTransactions.add(event.transactionId);

    final newBalance = event.balance - event.amount;
    _emit(PaymentSuccess(
      transactionId: event.transactionId,
      newBalance: newBalance,
    ));
  }

  void _emit(PaymentState state) {
    _state = state;
    _stateController.add(state);
  }

  void close() {
    _stateController.close();
  }
}