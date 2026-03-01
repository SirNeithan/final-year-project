<?php
/**
 * Email Functions for Smart Dine
 * 
 * This file contains functions for sending email notifications to customers.
 * 
 * NOTE: For production use, consider using:
 * - PHPMailer library for better email handling
 * - SMTP server for reliable delivery
 * - Email templates for professional appearance
 */

/**
 * Send order confirmation email to customer
 * 
 * @param string $customerEmail Customer's email address
 * @param string $customerName Customer's name
 * @param int $orderId Order ID
 * @param string $transactionId Payment transaction ID
 * @param float $totalAmount Total order amount
 * @param array $orderItems Array of order items
 * @param string $deliveryAddress Delivery address
 * @param string $deliveryCity Delivery city
 * @param string $customerPhone Customer's phone number
 * @return bool True if email sent successfully, false otherwise
 */
function sendOrderConfirmationEmail(
    $customerEmail, 
    $customerName, 
    $orderId, 
    $transactionId, 
    $totalAmount, 
    $orderItems, 
    $deliveryAddress, 
    $deliveryCity,
    $customerPhone
) {
    // Email subject
    $subject = "Order Confirmation #$orderId - Smart Dine";
    
    // Build order items list
    $itemsList = "";
    foreach ($orderItems as $item) {
        $itemsList .= "- {$item['product_name']} x {$item['quantity']} - {$item['product_price']}\n";
    }
    
    // Email body (plain text)
    $message = "
Dear $customerName,

Thank you for your order at Smart Dine!

ORDER DETAILS
=============
Order Number: #$orderId
Transaction ID: $transactionId
Order Date: " . date('F j, Y, g:i a') . "

ITEMS ORDERED
=============
$itemsList

TOTAL AMOUNT: UGX " . number_format($totalAmount, 0) . "

DELIVERY INFORMATION
====================
Address: $deliveryAddress
City: $deliveryCity
Phone: $customerPhone

WHAT'S NEXT?
============
✓ Your order has been confirmed
✓ We'll contact you shortly on $customerPhone for delivery coordination
✓ Expected delivery time: 30-45 minutes
✓ Payment has been processed successfully

If you have any questions, please contact us at:
Phone: 0766191751
Email: support@smartdine.com

Thank you for choosing Smart Dine!

Best regards,
The Smart Dine Team

---
This is an automated message. Please do not reply to this email.
";

    // Email headers
    $headers = "From: Smart Dine <noreply@smartdine.com>\r\n";
    $headers .= "Reply-To: support@smartdine.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Send email
    // Note: mail() function may not work on all servers
    // For production, use PHPMailer or similar library
    $sent = @mail($customerEmail, $subject, $message, $headers);
    
    // Log email attempt (for debugging)
    error_log("Order confirmation email " . ($sent ? "sent" : "failed") . " to: $customerEmail for order #$orderId");
    
    return $sent;
}

/**
 * Send order confirmation email (HTML version)
 * 
 * This is a more professional HTML email version
 * 
 * @param string $customerEmail Customer's email address
 * @param string $customerName Customer's name
 * @param int $orderId Order ID
 * @param string $transactionId Payment transaction ID
 * @param float $totalAmount Total order amount
 * @param array $orderItems Array of order items
 * @param string $deliveryAddress Delivery address
 * @param string $deliveryCity Delivery city
 * @param string $customerPhone Customer's phone number
 * @return bool True if email sent successfully, false otherwise
 */
function sendOrderConfirmationEmailHTML(
    $customerEmail, 
    $customerName, 
    $orderId, 
    $transactionId, 
    $totalAmount, 
    $orderItems, 
    $deliveryAddress, 
    $deliveryCity,
    $customerPhone
) {
    // Email subject
    $subject = "Order Confirmation #$orderId - Smart Dine";
    
    // Build order items HTML
    $itemsHTML = "";
    foreach ($orderItems as $item) {
        $itemsHTML .= "
        <tr>
            <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$item['product_name']}</td>
            <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: center;'>{$item['quantity']}</td>
            <td style='padding: 10px; border-bottom: 1px solid #eee; text-align: right;'>{$item['product_price']}</td>
        </tr>";
    }
    
    // HTML email body
    $htmlMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
        <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
            <h1 style='color: white; margin: 0; font-size: 28px;'>Smart Dine</h1>
            <p style='color: white; margin: 10px 0 0 0;'>Order Confirmation</p>
        </div>
        
        <div style='background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;'>
            <p style='font-size: 18px; margin-bottom: 20px;'>Dear $customerName,</p>
            
            <p>Thank you for your order! We're excited to prepare your delicious meal.</p>
            
            <div style='background: white; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #667eea;'>
                <h2 style='color: #667eea; margin-top: 0;'>Order Details</h2>
                <p><strong>Order Number:</strong> #$orderId</p>
                <p><strong>Transaction ID:</strong> $transactionId</p>
                <p><strong>Order Date:</strong> " . date('F j, Y, g:i a') . "</p>
            </div>
            
            <h3 style='color: #667eea;'>Items Ordered</h3>
            <table style='width: 100%; border-collapse: collapse; background: white; border-radius: 10px; overflow: hidden;'>
                <thead>
                    <tr style='background: #667eea; color: white;'>
                        <th style='padding: 12px; text-align: left;'>Item</th>
                        <th style='padding: 12px; text-align: center;'>Qty</th>
                        <th style='padding: 12px; text-align: right;'>Price</th>
                    </tr>
                </thead>
                <tbody>
                    $itemsHTML
                    <tr style='background: #f0f0f0; font-weight: bold;'>
                        <td colspan='2' style='padding: 15px; text-align: right;'>Total:</td>
                        <td style='padding: 15px; text-align: right;'>UGX " . number_format($totalAmount, 0) . "</td>
                    </tr>
                </tbody>
            </table>
            
            <div style='background: white; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                <h3 style='color: #667eea; margin-top: 0;'>Delivery Information</h3>
                <p><strong>Address:</strong> $deliveryAddress</p>
                <p><strong>City:</strong> $deliveryCity</p>
                <p><strong>Phone:</strong> $customerPhone</p>
            </div>
            
            <div style='background: #e8f5e9; padding: 20px; border-radius: 10px; border-left: 4px solid #4caf50;'>
                <h3 style='color: #2e7d32; margin-top: 0;'>What's Next?</h3>
                <p style='margin: 5px 0;'>✓ Your order has been confirmed</p>
                <p style='margin: 5px 0;'>✓ We'll contact you shortly on $customerPhone</p>
                <p style='margin: 5px 0;'>✓ Expected delivery: 30-45 minutes</p>
                <p style='margin: 5px 0;'>✓ Payment processed successfully</p>
            </div>
            
            <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;'>
                <p style='color: #666;'>Questions? Contact us:</p>
                <p style='margin: 5px 0;'>📞 Phone: 0766191751</p>
                <p style='margin: 5px 0;'>📧 Email: support@smartdine.com</p>
            </div>
            
            <p style='text-align: center; color: #999; font-size: 12px; margin-top: 30px;'>
                This is an automated message. Please do not reply to this email.<br>
                © " . date('Y') . " Smart Dine. All rights reserved.
            </p>
        </div>
    </body>
    </html>
    ";
    
    // Plain text version (fallback)
    $textMessage = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlMessage));
    
    // Email headers for HTML
    $headers = "From: Smart Dine <noreply@smartdine.com>\r\n";
    $headers .= "Reply-To: support@smartdine.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    // Send email
    $sent = @mail($customerEmail, $subject, $htmlMessage, $headers);
    
    // Log email attempt
    error_log("HTML order confirmation email " . ($sent ? "sent" : "failed") . " to: $customerEmail for order #$orderId");
    
    return $sent;
}

/**
 * Send SMS notification (placeholder function)
 * 
 * For production, integrate with SMS gateway like:
 * - Africa's Talking
 * - Twilio
 * - Nexmo
 * 
 * @param string $phoneNumber Customer's phone number
 * @param string $message SMS message
 * @return bool True if SMS sent successfully
 */
function sendSMSNotification($phoneNumber, $message) {
    // This is a placeholder function
    // In production, integrate with an SMS gateway
    
    // Example with Africa's Talking (commented out):
    /*
    require_once 'path/to/AfricasTalkingGateway.php';
    
    $username = "your_username";
    $apiKey = "your_api_key";
    $gateway = new AfricasTalkingGateway($username, $apiKey);
    
    try {
        $results = $gateway->sendMessage($phoneNumber, $message);
        return true;
    } catch (Exception $e) {
        error_log("SMS sending failed: " . $e->getMessage());
        return false;
    }
    */
    
    // For now, just log the SMS
    error_log("SMS to $phoneNumber: $message");
    
    return true; // Simulate success
}
