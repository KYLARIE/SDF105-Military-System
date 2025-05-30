    </div> <!-- End of dashboard-content -->

    <script>
        // Mobile menu toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const mobileToggle = document.getElementById('mobile-toggle');
            if (mobileToggle) {
                mobileToggle.addEventListener('click', function() {
                    document.body.classList.toggle('sidebar-visible');
                });
            }
        });
    </script>
</body>
</html> 