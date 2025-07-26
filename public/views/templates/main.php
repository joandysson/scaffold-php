<!DOCTYPE html>
<html lang="<?= getHtmlLang() ?>">
<head>
    <?php // Global site header ?>
    <?php include dirname(__DIR__) . '/layouts/main/head.php'; ?>
    <?php // Area to add extra elements to <head> ?>
    <?php if (isset($headExtra)) { echo $headExtra; } ?>
</head>
<body>
    <?php // Site header ?>
    <?php include dirname(__DIR__) . '/layouts/main/header.php'; ?>
    <?php // Main content rendered by the view ?>
    <?php echo $content; ?>
    <?php // Site footer ?>
    <?php include dirname(__DIR__) . '/layouts/main/footer.php'; ?>
    <?php // Global scripts ?>
    <?php include dirname(__DIR__) . '/layouts/scripts.php'; ?>
    <?php // Allows injecting extra scripts if needed ?>
    <?php if (isset($scriptsExtra)) { echo $scriptsExtra; } ?>
</body>
</html>
