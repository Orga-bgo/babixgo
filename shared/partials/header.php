<?php
/**
 * Header partial
 */
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'babixgo'); ?></title>
    <link rel="stylesheet" href="/shared/assets/css/main.css">
</head>
<body>
    <?php include __DIR__ . '/nav.php'; ?>
    <main class="container">
