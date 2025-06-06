    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Activate current page button
        const currentPage = '<?php echo basename($_SERVER['PHP_SELF']); ?>';
        document.querySelectorAll('.nav-button').forEach(button => {
            if(button.textContent.trim() === '<?php echo $page_title; ?>') {
                button.classList.add('active');
            }
        });
    </script>
</body>
</html>