<?php 
class Main  extends SWI_CONTROLLER {
	public static $viewparams;
	function Main($viewparams) {
		$this->viewparams = $viewparams;
	}
	function index() {
		echo "hello from controller"; 
	}	
	function _systemview() {
		
	}
}