<?php

/**
 * Created by PhpStorm.
 * User: peter
 * Date: 26/01/2020
 * Time: 20:31
 */
class Loginpage extends BootstrapWebPage {
    
    function __construct($pageTitle, $pageHeading1, $footerText) {
        $this->pageStart = $this->makePageStart($pageTitle);
        $this->header = $this->makeHeader($pageHeading1);
        $this->main = "";
        $this->footer = $this->makeFooter($footerText);
        $this->pageEnd = $this->makePageEnd();

    }

    /**
     * @return string
     */
    public function addLogin(){
        $loginForm = <<<LOGIN

LOGIN;
        return $loginForm;

    }public function getPage() {

		$this->main = $this->makeMain($this->main);

		return 	$this->pageStart.
				$this->header.
				
				$this->main.
				$this->footer.
				$this->pageEnd; 
	}

}
