<?php
# Force script to be run locally ONLY, or via command line
if (isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '127.0.0.1' && $_SERVER['REMOTE_ADDR'] != '::1')) {
    die('<h1 style="color:red">This script <strong>MUST</strong> be run locally.</h1>');
}

echo "<h3>Updating composer...</h3>";
echo (trim(shell_exec("php composer.phar self-update")) == "You are using the latest composer version.") ? '<p style="color:green">Composer is up-to-date.</p>' : '<p style="color:orange">Composer has been updated to the latest version.</p>';

echo "<h3>Updating Facebook SDK...</h3>";
chdir("..");
$steps = explode("\n", shell_exec("php update/composer.phar update"));
echo ($steps[1] == "Nothing to install/update") ? '<p style="color:green">Facebook SDK is up-to-date.</p>' : '<p style="color:orange">Facebook SDK updated to latest version.</p>' ;

if (file_exists("sdk/.git/")) {
    echo '<h3>Cleaning up Facebook SDK submodule...</h3><p style="color:orange">Facebook SDK cleaned.</p>';
    shell_exec("rm -rf sdk/.git/");
}

echo '<h3>Done!</h3>';
?>
