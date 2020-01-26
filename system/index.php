<?php
function autoloadClasses($className) {
    $filename = "classes/" . $className . ".php";
    if (is_readable($filename)) {
        include $filename;
    }
}

spl_autoload_register("autoloadClasses");

