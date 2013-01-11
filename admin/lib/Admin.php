<?php
class Admin extends ApiFrontend {
    function init(){
        parent::init();
        $this->dbConnect();
        $this->requires('atk','4.2.0');


        $this->pathfinder->addLocation('.',array(
        		'addons'=>array('atk4-addons','addons'),
        		'php'=>array('shared'),
        		'js'=>'templates/js',
	        ))
	        ->setParent($this->pathfinder->base_location);
        
        $this->add('jUI');
        
        $this->auth=$this->add('DealerAuth');

        // Create different menus
        if($this->auth->isLoggedIn()){
        	// For admin separated menu
        	if ($this->auth->model['is_admin']){
        		$this->add('Menu',null,'Menu')
		        	->addMenuItem('index','Dashboard')
		        	->addMenuItem('management')
		        	->addMenuItem('logout')
	        		;
        	}
        }else{
        	$this->add('Menu',null,'Menu')
   		    	->addMenuItem('login')
	        	;
        }
        
    }
}
