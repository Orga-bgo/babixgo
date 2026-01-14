<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'babixgo.de' ?></title>
    
    <!-- Shared CSS from shared/assets/css/ -->
    <?php 
    // Determine path to shared assets based on current location
    $sharedAssetsPath = defined('SHARED_ASSETS_PATH') ? SHARED_ASSETS_PATH : '../../shared/assets/';
    ?>
    <link rel="stylesheet" href="<?= $sharedAssetsPath ?>css/main.css">
    <?php if (isset($includeAdminCSS) && $includeAdminCSS): ?>
    <link rel="stylesheet" href="<?= $sharedAssetsPath ?>css/admin.css">
    <?php endif; ?>
    
    <!-- Additional CSS if provided -->
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ((array)$additionalCSS as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
