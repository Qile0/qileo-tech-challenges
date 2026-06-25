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
