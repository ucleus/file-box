// Ucleus Logo Delivery Portal - JavaScript

// ============================================
// Utility Functions
// ============================================

function showAlert(message, type = 'info', containerId = 'alertContainer') {
    const container = document.getElementById(containerId);
    if (!container) return;

    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.textContent = message;
    container.innerHTML = '';
    container.appendChild(alert);

    setTimeout(() => {
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

function apiCall(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        }
    };

    if (data && method !== 'GET') {
        options.body = JSON.stringify(data);
    }

    return fetch(url, options)
        .then(response => response.json())
        .catch(error => {
            console.error('API Error:', error);
            return { error: 'Network error occurred' };
        });
}

function apiCallFormData(url, formData) {
    return fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .catch(error => {
        console.error('API Error:', error);
        return { error: 'Network error occurred' };
    });
}

// ============================================
// Modal Functions
// ============================================

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Close modal when clicking outside
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
        closeModal(e.target.id);
    }
});

// ============================================
// Public Delivery Page Functions
// ============================================

function previewFile(filename) {
    const url = `/dl/${window.deliveryToken}/preview/${filename}`;
    window.open(url, '_blank');
}

function submitTweakRequest(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const message = formData.get('message');

    fetch(`/dl/${window.deliveryToken}/tweak`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `message=${encodeURIComponent(message)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            form.style.display = 'none';
            document.getElementById('tweakSuccess').classList.remove('hidden');
            setTimeout(() => closeModal('tweakModal'), 3000);
        } else {
            showAlert(data.error || 'Failed to send request', 'error', 'tweakAlert');
        }
    });
}

// ============================================
// Admin Login Functions
// ============================================

let userEmail = '';

function requestOTP(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    userEmail = formData.get('email');

    const btn = document.getElementById('emailBtn');
    btn.disabled = true;
    btn.textContent = 'Sending...';

    fetch('/admin/otp/request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `email=${encodeURIComponent(userEmail)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('emailStep').classList.add('hidden');
            document.getElementById('otpStep').classList.remove('hidden');
            showAlert('Check your email for the code', 'success');
        } else {
            showAlert(data.error || 'Failed to send code', 'error');
            btn.disabled = false;
            btn.textContent = 'Send Code';
        }
    });
}

function verifyOTP(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const code = formData.get('code');

    const btn = document.getElementById('otpBtn');
    btn.disabled = true;
    btn.textContent = 'Verifying...';

    fetch('/admin/otp/verify', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `email=${encodeURIComponent(userEmail)}&code=${encodeURIComponent(code)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Login successful! Redirecting...', 'success');
            setTimeout(() => window.location.href = '/admin', 1000);
        } else {
            showAlert(data.error || 'Invalid code', 'error');
            btn.disabled = false;
            btn.textContent = 'Verify & Login';
        }
    });
}

function backToEmail() {
    document.getElementById('otpStep').classList.add('hidden');
    document.getElementById('emailStep').classList.remove('hidden');
    document.getElementById('emailForm').reset();
}

// ============================================
// Admin Dashboard Functions
// ============================================

function copyLink(token) {
    const url = `${window.location.origin}/dl/${token}`;
    navigator.clipboard.writeText(url).then(() => {
        showAlert('Link copied to clipboard!', 'success');
    });
}

function showActions(deliveryId) {
    window.currentDeliveryId = deliveryId;
    openModal('actionsModal');
}

function emailClient() {
    if (!currentDeliveryId) return;

    fetch('/admin/deliveries/email', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `delivery_id=${currentDeliveryId}`
    })
    .then(response => response.json())
    .then(data => {
        const type = data.success ? 'success' : 'error';
        showAlert(data.success || data.error, type, 'actionAlert');
    });
}

function pauseDelivery() {
    if (!currentDeliveryId) return;
    updateDeliveryStatus('pause');
}

function resumeDelivery() {
    if (!currentDeliveryId) return;
    updateDeliveryStatus('resume');
}

function expireDelivery() {
    if (!currentDeliveryId) return;
    if (!confirm('Are you sure you want to expire this delivery?')) return;
    updateDeliveryStatus('expire');
}

function updateDeliveryStatus(action) {
    fetch(`/admin/deliveries/${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `delivery_id=${currentDeliveryId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.success, 'success', 'actionAlert');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert(data.error, 'error', 'actionAlert');
        }
    });
}

function regenerateToken() {
    if (!currentDeliveryId) return;
    if (!confirm('This will invalidate the old link. Continue?')) return;

    fetch('/admin/deliveries/regenerate-token', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `delivery_id=${currentDeliveryId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Token regenerated: ' + data.token, 'success', 'actionAlert');
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showAlert(data.error, 'error', 'actionAlert');
        }
    });
}

function repackageZip() {
    if (!currentDeliveryId) return;

    fetch('/admin/deliveries/repackage-zip', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `delivery_id=${currentDeliveryId}`
    })
    .then(response => response.json())
    .then(data => {
        const type = data.success ? 'success' : 'error';
        showAlert(data.success || data.error, type, 'actionAlert');
    });
}

function deleteDelivery() {
    if (!currentDeliveryId) return;
    if (!confirm('This will permanently delete this delivery and all files. Are you sure?')) return;

    fetch('/admin/deliveries/delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `delivery_id=${currentDeliveryId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Delivery deleted', 'success', 'actionAlert');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert(data.error, 'error', 'actionAlert');
        }
    });
}

// ============================================
// Create Delivery Functions
// ============================================

function createDelivery(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);
    const btn = document.getElementById('submitBtn');

    btn.disabled = true;
    btn.textContent = 'Creating...';

    fetch('/admin/deliveries/create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.createdDeliveryId = data.delivery_id;
            window.createdToken = data.token;

            // Show success card
            document.getElementById('deliveryForm').closest('.card').classList.add('hidden');
            document.getElementById('successCard').classList.remove('hidden');
            document.getElementById('deliveryLink').value = `${window.location.origin}/dl/${data.token}`;

            showAlert('Delivery created successfully!', 'success');
        } else {
            showAlert(data.error || 'Failed to create delivery', 'error');
            btn.disabled = false;
            btn.textContent = 'Create Delivery';
        }
    });
}

function copyDeliveryLink() {
    const input = document.getElementById('deliveryLink');
    input.select();
    navigator.clipboard.writeText(input.value);
    showAlert('Link copied to clipboard!', 'success');
}

function uploadFiles() {
    document.getElementById('uploadArea').classList.remove('hidden');
    document.getElementById('uploadArea').scrollIntoView({ behavior: 'smooth' });
}

function sendEmail() {
    if (!createdDeliveryId) return;

    fetch('/admin/deliveries/email', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `delivery_id=${createdDeliveryId}`
    })
    .then(response => response.json())
    .then(data => {
        const type = data.success ? 'success' : 'error';
        showAlert(data.success || data.error, type);
    });
}

// ============================================
// File Upload Functions
// ============================================

let selectedFiles = [];

// Setup drag and drop
document.addEventListener('DOMContentLoaded', () => {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');

    if (uploadZone && fileInput) {
        uploadZone.addEventListener('click', () => fileInput.click());

        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });
    }
});

function handleFiles(files) {
    selectedFiles = Array.from(files);
    displayFileList();
    document.getElementById('uploadBtn').classList.remove('hidden');
}

function displayFileList() {
    const fileList = document.getElementById('fileList');
    fileList.innerHTML = '<h4 style="margin-top: 1rem;">Selected Files:</h4>';

    selectedFiles.forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'file-list-item';
        item.innerHTML = `
            <span>${file.name} (${(file.size / 1024).toFixed(1)} KB)</span>
            <button class="btn btn-sm btn-secondary" onclick="removeFile(${index})">Remove</button>
        `;
        fileList.appendChild(item);
    });
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    displayFileList();

    if (selectedFiles.length === 0) {
        document.getElementById('uploadBtn').classList.add('hidden');
    }
}

function uploadSelectedFiles() {
    if (!createdDeliveryId || selectedFiles.length === 0) return;

    const formData = new FormData();
    formData.append('delivery_id', createdDeliveryId);

    selectedFiles.forEach(file => {
        formData.append('files[]', file);
    });

    const btn = document.getElementById('uploadBtn');
    btn.disabled = true;
    btn.textContent = 'Uploading...';

    fetch('/admin/uploads', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Files uploaded successfully!', 'success', 'uploadAlert');
            selectedFiles = [];
            document.getElementById('fileList').innerHTML = '';
            document.getElementById('fileInput').value = '';
            btn.classList.add('hidden');
        } else {
            showAlert(data.error || 'Upload failed', 'error', 'uploadAlert');
        }
        btn.disabled = false;
        btn.textContent = 'Upload Files';
    });
}
