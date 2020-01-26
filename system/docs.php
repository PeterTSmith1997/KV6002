<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 09/12/2019
 * Time: 19:24
 */
function autoloadClasses($className) {
    $filename = "classes/" . $className . ".php";
    if (is_readable($filename)) {
        include $filename;
    }
}

spl_autoload_register("autoloadClasses");
$navItems = Array("home"=>"index.php","other page"=>"docs.php","more"=>"more.php");
/**
$page = new SectionedWebpage('Docs', 'API Docs', $navItems, 'footer');
$page->addToBody("This page shows the API endpoits");
$page->addApi('test','words','wvwtjyw8yky8w 7krwe');
echo $page->getPage();**/