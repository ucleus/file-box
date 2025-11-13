<?php
$pageTitle = 'Delivery Paused';
include __DIR__ . '/../partials/header.php';
?>

<div class="container-sm">
    <div class="greeting">
        <h1>This delivery is temporarily paused</h1>
        <p>Please check back soon or reach out for an update.</p>
    </div>

    <div class="card text-center">
        <p>This delivery has been temporarily paused. It will be available again soon.</p>
        <p class="mt-md">
            <a href="mailto:admin@ucleus.com" class="btn btn-primary">
                Contact Ucleus
            </a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
