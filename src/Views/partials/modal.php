<!-- Brand Notes Modal -->
<div id="brandNotesModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Brand Notes</h3>
            <button class="modal-close" onclick="closeModal('brandNotesModal')">&times;</button>
        </div>
        <div class="modal-body">
            <?php if (!empty($delivery['brand_notes'])): ?>
                <?= nl2br(htmlspecialchars($delivery['brand_notes'])) ?>
            <?php else: ?>
                <p>No brand notes available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tweak Request Modal -->
<div id="tweakModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Request a Tweak</h3>
            <button class="modal-close" onclick="closeModal('tweakModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="tweakForm" onsubmit="submitTweakRequest(event)">
                <div class="form-group">
                    <label class="form-label">What needs adjusting?</label>
                    <textarea name="message" class="form-textarea" required placeholder="Describe the changes you'd like..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Send Request</button>
            </form>
            <div id="tweakSuccess" class="alert alert-success hidden" style="margin-top: 1rem;">
                Thanks! I'll respond shortly.
            </div>
        </div>
    </div>
</div>
