<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($title) ? $title : 'Dashboard | Arena Sport'); ?></title>
    <link rel="stylesheet" href="<?php echo e(app_asset_versioned('css/style.css')); ?>">
</head>
<body class="dashboard-body <?php echo e(isset($themeMode) ? $themeMode : 'dark'); ?>">
    <?php echo $content; ?>
</body>
</html>
