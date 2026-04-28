<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? APP_NAME; ?> - Trazabilidad IPS</title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="/assets/css/<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php echo $content ?? ''; ?>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="/assets/js/<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="/assets/js/main.js"></script>
</body>
</html>