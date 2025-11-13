<tr data-delivery-id="<?= $delivery['id'] ?>">
    <td>
        <strong><?= htmlspecialchars($delivery['project_name']) ?></strong><br>
        <small><?= htmlspecialchars($delivery['client_name']) ?></small>
    </td>
    <td>
        <span class="status-badge status-<?= htmlspecialchars($delivery['status']) ?>">
            <?= htmlspecialchars(ucfirst($delivery['status'])) ?>
        </span>
    </td>
    <td><?= htmlspecialchars($delivery['download_count']) ?></td>
    <td>
        <small><?= date('M j, Y', strtotime($delivery['created_at'])) ?></small>
    </td>
    <td>
        <?php if ($delivery['expires_at']): ?>
            <small><?= date('M j, Y', strtotime($delivery['expires_at'])) ?></small>
        <?php else: ?>
            <small>—</small>
        <?php endif; ?>
    </td>
    <td>
        <div class="action-menu">
            <button class="btn btn-sm btn-secondary" onclick="copyLink('<?= htmlspecialchars($delivery['token']) ?>')">
                Copy Link
            </button>
            <button class="btn btn-sm btn-secondary" onclick="showActions(<?= $delivery['id'] ?>)">
                Actions
            </button>
        </div>
    </td>
</tr>
