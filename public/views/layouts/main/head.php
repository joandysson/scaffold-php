<?php
// public/views/layouts/main/head.php
// Meta tags and global layout links
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= $title ?? 'Example' ?></title>
<meta name="description" content="<?= $description ?? '' ?>">

<!-- Favicons -->
<link rel="icon" type="image/png" href="<?= asset('images/icon/favicon-32x32.png') ?>">
<link rel="apple-touch-icon" href="<?= asset('images/icon/apple-touch-icon.png') ?>">

<!-- Open Graph / Facebook -->
<meta property="og:title" content="<?= $title ?? 'Example' ?>">
<meta property="og:description" content="<?= $description ?? '' ?>">
<meta property="og:image" content="<?= asset('images/jpg/share.jpg') ?>">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= $title ?? 'Example' ?>">
<meta name="twitter:description" content="<?= $description ?? '' ?>">
<meta name="twitter:image" content="<?= asset('images/jpg/share.jpg') ?>">

<link rel="stylesheet" href="<?= asset('css/main.css') ?>">
