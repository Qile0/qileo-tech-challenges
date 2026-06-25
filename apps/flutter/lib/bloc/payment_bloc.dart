import 'payment_event.dart';
import 'payment_state.dart';

// TODO : implémenter pour faire passer test/payment_bloc_test.dart (approche TDD)

class PaymentBloc {
  PaymentState _state = PaymentInitial();

  Stream<PaymentState> get stream => const Stream.empty();
  PaymentState get state => _state;

  void add(PaymentEvent event) {}

  void close() {}
}
