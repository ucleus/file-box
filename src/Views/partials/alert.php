<?php if (isset($alert)): ?>
<div class="alert alert-<?= htmlspecialchars($alert['type'] ?? 'info') ?>">
    <?= htmlspecialchars($alert['message']) ?>
</div>
<?php endif; ?>
