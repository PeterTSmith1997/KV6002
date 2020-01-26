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
/**$navItems = Array("home"=>"index.php","other page"=>"docs.php","more"=>"more.php");
$page = new WebPageWithNav('Home','Welcome', $navItems, 'footer');
echo $page->getPage();

 */

$navItems = Array("home"=>"/WAIAssiginment","docs"=>"docs", "about"=>"about");
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
echo $path;
$base = "WAIAssiginment/";
if (strpos($path,$base)){
    $path = substr($path,strlen($base));
}
$options = array('action' => "",'subject' => "", 'param1' => "");

$path = explode("/", $path);

if (isset($path[0])) {
    $options['action'] = $path[0];

    if (isset($path[1])) {
        $options['subject'] = $path[1];

        if (isset($path[2])) {
            $options['param1'] = $path[2];
        }
    }
}

print_r($options);

switch ($options['subject']){
    case '':
        $page = new WebPageWithNav('Home','Welcome', $navItems, 'footer');
        echo $page->getPage();
        break;
    case 'docs':
        $page = new SectionedWebpage('page2', 'second page', $navItems, 'footer');
        $page->addToBody("This page shows a 2nd page");
        $page->addApi('div test','words','wvwtjyw8yky8w 7krwe');
        echo $page->getPage();
        break;
    case 'about':
        $page = new WebPageWithNav('about', 'about', $navItems, 'footer');
        echo $page->getPage();
        break;
    default:
        $page = new WebPageWithNav('404', '404 ', $navItems, 'footer');
        echo $page->getPage();

        break;
}

