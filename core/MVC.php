<?php
class MVC extends BASE {
    public function __construct() {
    } 
    public function View($page_name, $data_array) {
        //Convert array into variable
        foreach ($data_array as $k=>$v) {
            ${$k} = $v;
        }
        //load the view
        require_once 'views/'.$page_name.'.php';
    }
}
?>