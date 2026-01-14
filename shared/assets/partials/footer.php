    
    <!-- Shared JavaScript from shared/assets/js/ -->
    <?php 
    // Determine path to shared assets based on current location
    $sharedAssetsPath = defined('SHARED_ASSETS_PATH') ? SHARED_ASSETS_PATH : '../../shared/assets/';
    ?>
    <?php if (isset($includeValidationJS) && $includeValidationJS): ?>
    <script src="<?= $sharedAssetsPath ?>js/form-validation.js"></script>
    <?php endif; ?>
    <?php if (isset($includeAdminJS) && $includeAdminJS): ?>
    <script src="<?= $sharedAssetsPath ?>js/admin.js"></script>
    <?php endif; ?>
    
    <!-- Additional JavaScript if provided -->
    <?php if (isset($additionalJS)): ?>
        <?php foreach ((array)$additionalJS as $js): ?>
    <script src="<?= htmlspecialchars($js, ENT_QUOTES) ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
