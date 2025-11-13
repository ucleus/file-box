<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($pageTitle ?? 'Ucleus Logo Delivery') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="/" class="logo">Ucleus</a>
                <?php if (isset($showNav) && $showNav): ?>
                <nav class="nav">
                    <?php if (isset($_SESSION['authenticated'])): ?>
                        <a href="/admin">Dashboard</a>
                        <a href="/admin/logout">Logout</a>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main>
