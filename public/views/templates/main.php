<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php // Cabecalho global do site ?>
    <?php include dirname(__DIR__) . '/layouts/main/head.php'; ?>
    <?php // Area para adicionar elementos extras ao <head> ?>
    <?php if (isset($headExtra)) { echo $headExtra; } ?>
</head>
<body>
    <?php // Topo do site ?>
    <?php include dirname(__DIR__) . '/layouts/main/header.php'; ?>
    <?php // Conteudo principal renderizado pela view ?>
    <?php echo $content; ?>
    <?php // Rodape do site ?>
    <?php include dirname(__DIR__) . '/layouts/main/footer.php'; ?>
    <?php // Scripts globais ?>
    <?php include dirname(__DIR__) . '/layouts/scripts.php'; ?>
    <?php // Permite injetar scripts extras se necessario ?>
    <?php if (isset($scriptsExtra)) { echo $scriptsExtra; } ?>
</body>
</html>
