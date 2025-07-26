<?php
// Exemplo de view utilizando o layout principal
// Defina aqui o titulo e descricao da pagina
$title = 'Home';
$description = 'Pagina inicial de exemplo';
?>
<?php $content = section(function () { ?>
    <h1>Exemplo</h1>
    <p>Pagina simples para demonstrar o layout.</p>
<?php }); ?>

<?php // Inclui o template principal que usa os layouts de cabecalho e rodape ?>
<?php include 'templates/main.php'; ?>
