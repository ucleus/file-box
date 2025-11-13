<?php
$pageTitle = 'Create New Delivery - Ucleus';
$showNav = true;
$includeJS = true;
include __DIR__ . '/../partials/header.php';
?>

<div class="container-narrow">
    <div style="padding-top: 2rem;">
        <a href="/admin" class="btn btn-secondary btn-sm mb-md">← Back to Dashboard</a>
    </div>

    <h1>Create New Delivery</h1>

    <div class="card">
        <form id="deliveryForm" onsubmit="createDelivery(event)">
            <!-- Client Information -->
            <h3 class="mb-md">Client Information</h3>

            <div class="form-group">
                <label class="form-label">Client Name *</label>
                <input type="text" name="client_name" class="form-input" required placeholder="e.g., Acme Corp">
            </div>

            <div class="form-group">
                <label class="form-label">Client Email</label>
                <input type="email" name="client_email" class="form-input" placeholder="client@example.com">
                <span class="form-hint">Optional - for sending delivery link</span>
            </div>

            <!-- Project Information -->
            <h3 class="mb-md mt-lg">Project Information</h3>

            <div class="form-group">
                <label class="form-label">Project Name *</label>
                <input type="text" name="project_name" class="form-input" required placeholder="e.g., Brand Identity Package">
            </div>

            <div class="form-group">
                <label class="form-label">Version / Revision</label>
                <input type="text" name="project_version" class="form-input" placeholder="e.g., v1.0, Final">
            </div>

            <div class="form-group">
                <label class="form-label">Notes for Client</label>
                <textarea name="notes" class="form-textarea" placeholder="Any instructions or notes for the client..."></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Brand Notes</label>
                <textarea name="brand_notes" class="form-textarea" placeholder="Brand guidelines, color codes, usage instructions..."></textarea>
                <span class="form-hint">These can be viewed via "Read brand notes" button</span>
            </div>

            <!-- Expiry Settings -->
            <h3 class="mb-md mt-lg">Expiry Settings</h3>

            <div class="form-group">
                <label class="form-label">Expiry Date</label>
                <input type="datetime-local" name="expires_at" class="form-input">
                <span class="form-hint">Leave blank for no expiry</span>
            </div>

            <div class="form-group">
                <label class="form-label">Max Downloads</label>
                <input type="number" name="max_downloads" class="form-input" min="1" placeholder="e.g., 10">
                <span class="form-hint">Leave blank for unlimited downloads</span>
            </div>

            <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                Create Delivery
            </button>
        </form>

        <div id="alertContainer" class="mt-md"></div>
    </div>

    <!-- Success State -->
    <div id="successCard" class="card hidden" style="margin-top: 2rem;">
        <h3 class="text-center mb-md">Delivery Created Successfully! 🎉</h3>
        <div class="form-group">
            <label class="form-label">Delivery Link</label>
            <input type="text" id="deliveryLink" class="form-input" readonly>
        </div>
        <div class="flex gap-sm" style="flex-wrap: wrap;">
            <button class="btn btn-primary" onclick="copyDeliveryLink()">Copy Link</button>
            <button class="btn btn-secondary" onclick="uploadFiles()">Upload Files</button>
            <button class="btn btn-secondary" onclick="sendEmail()">Send Email to Client</button>
            <a href="/admin" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <!-- Upload Area (shown after creation) -->
    <div id="uploadArea" class="card hidden" style="margin-top: 2rem;">
        <h3 class="mb-md">Upload Files</h3>
        <div class="upload-zone" id="uploadZone">
            <div class="upload-icon">📤</div>
            <p><strong>Drag and drop files here</strong> or click to browse</p>
            <p style="color: var(--gray); font-size: 0.875rem;">
                Supported: PNG, JPG, SVG, PDF, AI, EPS, ZIP (Max 50MB)
            </p>
            <input type="file" id="fileInput" multiple accept=".png,.jpg,.jpeg,.svg,.pdf,.ai,.eps,.zip" style="display: none;">
        </div>
        <div id="fileList" class="file-list"></div>
        <button id="uploadBtn" class="btn btn-primary btn-block mt-md hidden" onclick="uploadSelectedFiles()">
            Upload Files
        </button>
        <div id="uploadAlert" class="mt-md"></div>
    </div>
</div>

<script>
    let createdDeliveryId = null;
    let createdToken = null;
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
