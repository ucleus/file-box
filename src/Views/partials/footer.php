    </main>
    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Ucleus. All files are provided solely for client use.</p>
            <p>
                <a href="mailto:admin@ucleus.com">Contact</a> •
                <a href="#">Privacy</a> •
                <a href="#">Terms</a>
            </p>
        </div>
    </footer>
    <?php if (isset($includeJS) && $includeJS): ?>
    <script src="/assets/js/app.js"></script>
    <?php endif; ?>
</body>
</html>
