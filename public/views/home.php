<?php
// Example view using the main layout
// Set the page title and description here
$title = 'Home';
$description = 'Sample home page';
?>
<?php $content = section(function () { ?>
    <h1>Example</h1>
    <p>Simple page to demonstrate the layout.</p>
<?php }); ?>

<?php // Include the main template which loads header and footer layouts ?>
<?php include 'templates/main.php'; ?>
