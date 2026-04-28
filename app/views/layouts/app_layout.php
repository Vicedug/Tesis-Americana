<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($title ?? 'Panel') . ' | ' . APP_NAME; ?></title>
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="/assets/css/<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="main-content">
        <?php include __DIR__ . '/nav.php'; ?>

        <div class="page-content">
            <?php
            $flashMessages = App\Helpers\Flash::get();
            if (!empty($flashMessages)):
                foreach ($flashMessages as $msg):
            ?>
                <div class="alert alert-<?php echo $msg['type']; ?>">
                    <i class="fas fa-<?php echo $msg['type'] === 'success' ? 'check-circle' : ($msg['type'] === 'danger' ? 'exclamation-circle' : ($msg['type'] === 'warning' ? 'exclamation-triangle' : 'info-circle')); ?>"></i>
                    <?php echo htmlspecialchars($msg['text']); ?>
                </div>
            <?php
                endforeach;
            endif;
            ?>

            <?php echo $content ?? ''; ?>
        </div>
    </div>

    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/validations.js"></script>
    <?php if (isset($extra_js)): ?>
        <?php foreach ($extra_js as $js): ?>
            <script src="/assets/js/<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>