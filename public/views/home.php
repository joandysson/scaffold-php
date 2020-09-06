<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>test</title>
    <link rel="stylesheet" href="<?php echo asset('css/all.min.css') ?>">
</head>

<body>
    <form action="/test" method="post" enctype="multipart/form-data">
        <input type="text" name="one" id="one">
        <input type="text" name="two" id="two"><br>
        <input type="text" name="three" id="three"><br>
        <label for="fileToUpload">add file</label>
        <input hidden type="file" name="fileToUpload" value="" id="fileToUpload">
        <button type="submit">Test <?php echo $name ?></button>
    </form>
</body>

</html>