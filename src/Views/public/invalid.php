<?php
$pageTitle = 'Page Not Found';
include __DIR__ . '/../partials/header.php';
?>

<div class="container-sm">
    <div class="greeting">
        <h1>We can't find that page</h1>
        <p>Check the link or contact me.</p>
    </div>

    <div class="card text-center">
        <p>The delivery link you're looking for doesn't exist or may have been removed.</p>
        <p class="mt-md">
            <a href="mailto:admin@ucleus.com" class="btn btn-primary">
                Contact Ucleus
            </a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
