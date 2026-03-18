    <!-- Close main content area (opened in header.php) -->
    </main>
    
    <!-- Footer Section -->
    <footer>
        <p><i class="ri-phone-line"></i> Contact us: 0766191751 | SmartDine Hub - Taste the Difference <i class="ri-restaurant-line"></i> | <a href="<?php echo $basePath; ?>pages/terms.php" style="color:inherit;text-decoration:underline;">Terms &amp; Privacy</a></p>
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
