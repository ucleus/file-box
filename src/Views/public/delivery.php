<?php
$pageTitle = $delivery['project_name'] . ' - Logo Package';
$includeJS = true;
include __DIR__ . '/../partials/header.php';
?>

<div class="container-narrow">
    <!-- Greeting -->
    <section class="greeting">
        <h1>Hey <?= htmlspecialchars($delivery['client_name']) ?>—your logo package is ready.</h1>
        <p>Download the files below. If anything looks off, I'll fix it fast.</p>
    </section>

    <!-- Project Summary -->
    <div class="card project-summary mb-lg">
        <div class="summary-item">
            <div class="summary-label">Project</div>
            <div class="summary-value"><?= htmlspecialchars($delivery['project_name']) ?></div>
        </div>
        <?php if ($delivery['project_version']): ?>
        <div class="summary-item">
            <div class="summary-label">Version</div>
            <div class="summary-value"><?= htmlspecialchars($delivery['project_version']) ?></div>
        </div>
        <?php endif; ?>
        <div class="summary-item">
            <div class="summary-label">Delivered</div>
            <div class="summary-value"><?= date('M j, Y', strtotime($delivery['created_at'])) ?></div>
        </div>
        <?php if ($delivery['expires_at']): ?>
        <div class="summary-item">
            <div class="summary-label">Expires</div>
            <div class="summary-value"><?= date('M j', strtotime($delivery['expires_at'])) ?></div>
        </div>
        <?php endif; ?>
    </div>

    <?php if ($delivery['notes']): ?>
    <div class="alert alert-info mb-lg">
        <strong>Note:</strong> <?= nl2br(htmlspecialchars($delivery['notes'])) ?>
    </div>
    <?php endif; ?>

    <!-- File Grid -->
    <div class="file-grid mb-lg">
        <?php foreach ($assets as $asset): ?>
            <?php include __DIR__ . '/../partials/file-card.php'; ?>
        <?php endforeach; ?>
    </div>

    <!-- CTA Row -->
    <div class="cta-row">
        <a href="/dl/<?= htmlspecialchars($delivery['token']) ?>/download-all" class="btn btn-primary">
            Download everything (.zip)
        </a>
        <?php if ($delivery['brand_notes']): ?>
        <button class="btn btn-secondary" onclick="openModal('brandNotesModal')">
            Read brand notes →
        </button>
        <?php endif; ?>
        <button class="btn btn-accent" onclick="openModal('tweakModal')">
            Request a Tweak
        </button>
    </div>
</div>

<!-- Modals -->
<?php include __DIR__ . '/../partials/modal.php'; ?>

<script>
    // Pass delivery token to JS
    window.deliveryToken = '<?= htmlspecialchars($delivery['token']) ?>';
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
