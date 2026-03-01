# Payment System Documentation

## Overview
This is a **simulated payment system** designed for educational purposes. It does NOT process real payments or connect to actual payment gateways.

## Features

### 1. Card Validation
- **Luhn Algorithm**: Validates card numbers using the industry-standard Luhn algorithm
- **Expiry Date Validation**: Checks if the card is expired
- **CVV Validation**: Ensures CVV is 3-4 digits

### 2. User Experience
- Real-time card number formatting (adds spaces every 4 digits)
- Auto-formatting for expiry date (MM/YY format)
- CVV input restricted to numbers only
- Loading states during payment processing
- Clear error messages for validation failures

### 3. Payment Flow
1. User fills out checkout form with delivery and payment details
2. JavaScript validates and formats card information
3. Payment is processed via `api/process_payment.php`
4. If successful, order is created via `api/create_order.php`
5. User is redirected to success page with order and transaction details

## Test Cards

### Valid Test Card
- **Card Number**: 4532015112830366
- **Expiry**: Any future date (e.g., 12/25)
- **CVV**: Any 3 digits (e.g., 123)

### Other Valid Card Patterns
Any card number that passes the Luhn algorithm will work. You can generate test cards at: https://www.freeformatter.com/credit-card-number-generator-validator.html

## API Endpoints

### `/api/process_payment.php`
Simulates payment processing with validation.

**Request:**
```json
{
  "card_number": "4532015112830366",
  "expiry": "12/25",
  "cvv": "123",
  "amount": 50.00
}
```

**Success Response:**
```json
{
  "success": true,
  "message": "Payment processed successfully",
  "transaction_id": "TXN65f8a9b2c1d3e",
  "amount": 50.00,
  "last4": "0366"
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Invalid card number"
}
```

### `/api/create_order.php`
Creates an order in the database after successful payment.

**Request:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "address": "123 Main St",
  "city": "Kampala",
  "zip": "12345",
  "transaction_id": "TXN65f8a9b2c1d3e"
}
```

**Success Response:**
```json
{
  "success": true,
  "message": "Order created successfully",
  "order_id": 42
}
```

## Database Changes

Run the migration script to add transaction tracking:
```sql
mysql -u root -p smartdine < data/add_transaction_id.sql
```

This adds a `transaction_id` column to the `orders` table.

## Security Notes

### For Educational Use Only
- This system does NOT encrypt card data
- Card numbers are NOT stored in the database
- No PCI compliance measures are implemented
- Payment success is simulated (90% success rate)

### For Production Use
If you ever need to implement real payments:
1. Use a payment gateway (Stripe, PayPal, Flutterwave)
2. Never store raw card data
3. Use HTTPS for all transactions
4. Implement proper PCI compliance
5. Use tokenization for card data
6. Add fraud detection
7. Implement 3D Secure authentication

## Validation Rules

### Card Number
- Must be 13-19 digits
- Must pass Luhn algorithm check
- Spaces and dashes are automatically removed

### Expiry Date
- Format: MM/YY
- Month must be 01-12
- Date must be in the future

### CVV
- Must be 3-4 digits
- Only numeric characters allowed

## Error Handling

The system provides clear error messages for:
- Invalid card numbers
- Expired cards
- Invalid CVV
- Payment declined (simulated)
- Server errors
- Empty cart
- Database errors

## Future Enhancements

For a more realistic simulation, you could add:
- Different card types (Visa, Mastercard, Amex) with specific validation
- Billing address verification
- Payment retry logic
- Refund simulation
- Payment history tracking
- Email notifications with order details
- SMS notifications
- Multiple payment methods (mobile money, bank transfer)
