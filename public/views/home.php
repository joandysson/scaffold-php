<?php
// Exemplo de view utilizando o layout principal
$title = 'Home';
$description = 'Pagina inicial de exemplo';
?>
<?php $content = section(function () { ?>
    <h1>Exemplo</h1>
    <p>Página simples para demonstrar o layout.</p>
<?php }); ?>
<?php include 'templates/main.php'; ?>
