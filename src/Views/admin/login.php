<?php
$pageTitle = 'Admin Login - Ucleus';
$includeJS = true;
include __DIR__ . '/../partials/header.php';
?>

<div class="container-sm" style="padding-top: 4rem;">
    <div class="card">
        <div class="card-header text-center">
            <h2>Admin Login</h2>
            <p class="card-subtitle">Enter your email to receive a sign-in code</p>
        </div>

        <div id="emailStep">
            <form id="emailForm" onsubmit="requestOTP(event)">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-input" required placeholder="admin@ucleus.com">
                </div>
                <button type="submit" class="btn btn-primary btn-block" id="emailBtn">
                    Send Code
                </button>
            </form>
        </div>

        <div id="otpStep" class="hidden">
            <form id="otpForm" onsubmit="verifyOTP(event)">
                <div class="form-group">
                    <label class="form-label">Enter 6-digit code</label>
                    <input type="text" name="code" class="form-input" required placeholder="123456" maxlength="6" pattern="[0-9]{6}">
                    <span class="form-hint">Check your email for the code</span>
                </div>
                <button type="submit" class="btn btn-primary btn-block" id="otpBtn">
                    Verify & Login
                </button>
                <button type="button" class="btn btn-secondary btn-block mt-sm" onclick="backToEmail()">
                    Back
                </button>
            </form>
        </div>

        <div id="alertContainer"></div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
