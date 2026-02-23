    </main>
    
    <footer>
        <p>📞 Contact us: +123-456-7890 | Smart Dine - Taste the Difference 🍽️</p>
    </footer>
    
    <script src="<?php echo $basePath; ?>assets/js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            updateCartCount();
        });
        
        function showNotification(message, duration = 3000) {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, duration);
        }
    </script>
</body>
</html>
