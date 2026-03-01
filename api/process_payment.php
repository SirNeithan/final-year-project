<?php
/**
 * Payment Processing API
 * 
 * This file simulates payment processing for educational purposes.
 * It validates credit card information and generates simulated transaction responses.
 * 
 * IMPORTANT: This does NOT process real payments or connect to actual payment gateways.
 * Never use this in production or with real credit card information.
 */

// Start session to maintain user state
session_start();

// Set response type to JSON for API communication
header('Content-Type: application/json');

/**
 * Validates credit card number using the Luhn algorithm
 * 
 * The Luhn algorithm is an industry-standard checksum formula used to validate
 * credit card numbers and detect simple errors in typing or transmission.
 * 
 * @param string $cardNumber The credit card number to validate
 * @return bool True if valid, false otherwise
 */
function validateCardNumber($cardNumber) {
    // Remove spaces and dashes from the card number
    $cardNumber = preg_replace('/[\s\-]/', '', $cardNumber);
    
    // Check if it's numeric and has valid length (13-19 digits)
    if (!ctype_digit($cardNumber) || strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
        return false;
    }
    
    // Apply Luhn algorithm for card validation
    $sum = 0;
    $numDigits = strlen($cardNumber);
    $parity = $numDigits % 2;
    
    // Loop through each digit
    for ($i = 0; $i < $numDigits; $i++) {
        $digit = intval($cardNumber[$i]);
        
        // Double every second digit
        if ($i % 2 == $parity) {
            $digit *= 2;
        }
        
        // If doubled digit is greater than 9, subtract 9
        if ($digit > 9) {
            $digit -= 9;
        }
        
        // Add to sum
        $sum += $digit;
    }
    
    // Valid if sum is divisible by 10
    return ($sum % 10) == 0;
}

/**
 * Validates credit card expiry date
 * 
 * Checks if the expiry date is in the correct format (MM/YY) and
 * ensures the card has not expired.
 * 
 * @param string $expiry The expiry date in MM/YY format
 * @return bool True if valid and not expired, false otherwise
 */
function validateExpiryDate($expiry) {
    // Expected format: MM/YY (e.g., 12/25)
    // Month must be 01-12, year must be 2 digits
    if (!preg_match('/^(0[1-9]|1[0-2])\/([0-9]{2})$/', $expiry, $matches)) {
        return false;
    }
    
    // Extract month and year from the matched pattern
    $month = intval($matches[1]);
    $year = intval('20' . $matches[2]); // Convert YY to 20YY
    
    // Get current date for comparison
    $currentYear = intval(date('Y'));
    $currentMonth = intval(date('m'));
    
    // Check if card is expired
    if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
        return false;
    }
    
    return true;
}

/**
 * Validates CVV (Card Verification Value)
 * 
 * CVV is the 3 or 4 digit security code on the back of credit cards.
 * 
 * @param string $cvv The CVV to validate
 * @return bool True if valid (3-4 digits), false otherwise
 */
function validateCVV($cvv) {
    // CVV must be 3 or 4 digits (Amex uses 4, most others use 3)
    return preg_match('/^[0-9]{3,4}$/', $cvv);
}

// Get POST data sent from the checkout form
$data = json_decode(file_get_contents('php://input'), true);

// Check if data was received properly
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

// Extract payment information from the request
$cardNumber = $data['card_number'] ?? '';
$expiry = $data['expiry'] ?? '';
$cvv = $data['cvv'] ?? '';
$amount = $data['amount'] ?? 0;

// Validate card number using Luhn algorithm
if (!validateCardNumber($cardNumber)) {
    echo json_encode(['success' => false, 'message' => 'Invalid card number']);
    exit;
}

// Validate expiry date (format and not expired)
if (!validateExpiryDate($expiry)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired card']);
    exit;
}

// Validate CVV (3-4 digits)
if (!validateCVV($cvv)) {
    echo json_encode(['success' => false, 'message' => 'Invalid CVV']);
    exit;
}

// Simulate payment processing delay (real payment gateways take time)
usleep(500000); // 0.5 second delay

// Generate a simulated transaction ID (unique identifier for this payment)
$transactionId = 'TXN' . strtoupper(uniqid());

// Simulate success (90% success rate for realism)
// In real systems, payments can fail for various reasons
$success = (rand(1, 10) <= 9);

if ($success) {
    // Payment successful - return success response with transaction details
    echo json_encode([
        'success' => true,
        'message' => 'Payment processed successfully',
        'transaction_id' => $transactionId,
        'amount' => $amount,
        'last4' => substr(preg_replace('/[\s\-]/', '', $cardNumber), -4) // Last 4 digits for confirmation
    ]);
} else {
    // Simulate occasional payment failures (insufficient funds, bank decline, etc.)
    echo json_encode([
        'success' => false,
        'message' => 'Payment declined. Please try another card or contact your bank.'
    ]);
}
