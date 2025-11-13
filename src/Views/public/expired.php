<?php
$pageTitle = 'Link Expired';
include __DIR__ . '/../partials/header.php';
?>

<div class="container-sm">
    <div class="greeting">
        <h1>This link has expired</h1>
        <p>Reach out and I'll refresh it for you.</p>
    </div>

    <div class="card text-center">
        <p>This delivery link is no longer active. It may have expired or reached its download limit.</p>
        <p class="mt-md">
            <a href="mailto:admin@ucleus.com" class="btn btn-primary">
                Contact Ucleus
            </a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
