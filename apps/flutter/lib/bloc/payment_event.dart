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
