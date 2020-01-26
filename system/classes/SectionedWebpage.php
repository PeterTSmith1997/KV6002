<?php

/**
 * Created by PhpStorm.
 * User: peter
 * Date: 09/12/2019
 * Time: 19:57
 */
class SectionedWebpage extends WebPageWithNav
{
    /**
     * SectionedWebpage constructor.
     * @param $pageTitle
     * @param $pageHeading1
     * @param $navItems
     * @param $footerText
     */
    function __construct($pageTitle, $pageHeading1, $navItems, $footerText) {
        $this->pageStart = $this->makePageStart($pageTitle);
        $this->header = $this->makeHeader($pageHeading1);
        $this->nav = $this->makeNav($navItems);
        $this->main = "";
        $this->footer = $this->makeFooter($footerText);
        $this->pageEnd = $this->makePageEnd();

    }

    public function addApi($endpont, $name, $text){
        $section = "<div class=\"row bg-light text-dark\">\n
        <h2>$name</h2>\n 
        <div class='col-md-12 bg-info'>$endpont</div>
        <div class='col-md-12'>$text</div>
        </div>
        ";
        $this->addToBody($section);
    }

    /**
     * Override the parents method
     */
    public function getPage() {

        $this->main = $this->makeMain($this->main);

        return 	$this->pageStart.
        $this->header.
        $this->nav.
        $this->main.
        $this->footer.
        $this->pageEnd;
    }


}