<?php
$pageTitle = 'Dashboard - Ucleus';
$showNav = true;
$includeJS = true;
include __DIR__ . '/../partials/header.php';
?>

<div class="container">
    <div class="dashboard-header">
        <h1>Deliveries Dashboard</h1>
        <a href="/admin/deliveries/new" class="btn btn-primary">Create New Delivery</a>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= $stats['total_deliveries'] ?></div>
            <div class="stat-label">Total Deliveries</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['active_deliveries'] ?></div>
            <div class="stat-label">Active</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= $stats['expiring_soon'] ?></div>
            <div class="stat-label">Expiring Soon</div>
        </div>
    </div>

    <!-- Deliveries Table -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Project / Client</th>
                    <th>Status</th>
                    <th>Downloads</th>
                    <th>Created</th>
                    <th>Expires</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($deliveries)): ?>
                <tr>
                    <td colspan="6" class="text-center" style="padding: 3rem;">
                        <p style="color: var(--gray);">No deliveries yet. Create your first link and make a client's day.</p>
                        <a href="/admin/deliveries/new" class="btn btn-primary mt-md">Create Delivery</a>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($deliveries as $delivery): ?>
                        <?php include __DIR__ . '/../partials/table-row.php'; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Action Modal -->
<div id="actionsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Delivery Actions</h3>
            <button class="modal-close" onclick="closeModal('actionsModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="flex flex-between gap-sm" style="flex-wrap: wrap;">
                <button class="btn btn-secondary btn-sm" onclick="emailClient()">Email Client</button>
                <button class="btn btn-secondary btn-sm" onclick="pauseDelivery()">Pause</button>
                <button class="btn btn-secondary btn-sm" onclick="resumeDelivery()">Resume</button>
                <button class="btn btn-secondary btn-sm" onclick="expireDelivery()">Expire</button>
                <button class="btn btn-secondary btn-sm" onclick="regenerateToken()">Regenerate Token</button>
                <button class="btn btn-secondary btn-sm" onclick="repackageZip()">Repackage ZIP</button>
                <button class="btn btn-accent btn-sm" onclick="deleteDelivery()">Delete</button>
            </div>
            <div id="actionAlert" class="mt-md"></div>
        </div>
    </div>
</div>

<script>
    let currentDeliveryId = null;
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
