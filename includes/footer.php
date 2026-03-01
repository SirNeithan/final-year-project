    <!-- Close main content area (opened in header.php) -->
    </main>
    
    <!-- Footer Section -->
    <footer>
        <p>📞 Contact us: 0766191751| Smart Dine - Taste the Difference 🍽️</p>
    </footer>
    
    <!-- Include main JavaScript file -->
    <script src="<?php echo $basePath; ?>assets/js/script.js"></script>
    
    <script>
        /**
         * Initialize page when DOM is fully loaded
         * Updates the cart count badge in the navigation
         */
        document.addEventListener("DOMContentLoaded", function() {
            updateCartCount();
        });
        
        /**
         * Show a notification toast message to the user
         * 
         * @param {string} message - The message to display
         * @param {number} duration - How long to show the message (in milliseconds)
         */
        function showNotification(message, duration = 3000) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.style.display = 'block';
            
            // Hide notification after specified duration
            setTimeout(() => {
                notification.style.display = 'none';
            }, duration);
        }
    </script>
</body>
</html>
