<div class="file-card">
    <div class="file-thumbnail">
        <?php
        $ext = strtolower(pathinfo($asset['filename'], PATHINFO_EXTENSION));
        $previewable = in_array($ext, ['png', 'jpg', 'jpeg', 'svg']);
        ?>
        <?php if ($previewable): ?>
            <img src="/dl/<?= htmlspecialchars($delivery['token']) ?>/preview/<?= htmlspecialchars($asset['filename']) ?>"
                 alt="<?= htmlspecialchars($asset['original_filename']) ?>"
                 loading="lazy">
        <?php else: ?>
            <div class="file-icon">📄</div>
        <?php endif; ?>
    </div>
    <div class="file-info">
        <div class="file-name"><?= htmlspecialchars($asset['original_filename']) ?></div>
        <div class="file-meta">
            <span class="file-badge"><?= strtoupper($asset['file_type']) ?></span>
            <span><?= number_format($asset['file_size'] / 1024, 1) ?> KB</span>
        </div>
    </div>
    <div class="file-actions">
        <?php if ($previewable): ?>
        <button class="btn btn-secondary btn-sm" onclick="previewFile('<?= htmlspecialchars($asset['filename']) ?>')">
            Preview
        </button>
        <?php endif; ?>
        <a href="/dl/<?= htmlspecialchars($delivery['token']) ?>/download/<?= htmlspecialchars($asset['filename']) ?>"
           class="btn btn-primary btn-sm"
           download>
            Download
        </a>
    </div>
</div>
