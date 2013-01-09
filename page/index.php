<?php
class page_index extends Page {
    function init(){
        parent::init();
        if($this->api->auth->isLoggedIn()){
        	$this->add('View_Info')->set('Hello, ' . $this->api->auth->model['name']);
        }else{
        	$this->api->redirect('login');
        }
    }
    function defaultTemplate(){
    	return array('page/index');
    }
    
}
