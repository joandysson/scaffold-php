<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include dirname(__DIR__) . '/layouts/main/head.php'; ?>
    <?php if (isset($headExtra)) { echo $headExtra; } ?>
</head>
<body>
    <?php include dirname(__DIR__) . '/layouts/main/header.php'; ?>
    <?php echo $content; ?>
    <?php include dirname(__DIR__) . '/layouts/main/footer.php'; ?>
    <?php include dirname(__DIR__) . '/layouts/scripts.php'; ?>
    <?php if (isset($scriptsExtra)) { echo $scriptsExtra; } ?>
</body>
</html>
