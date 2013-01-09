<?php
class Frontend extends ApiFrontend {
    function init(){
        parent::init();
        $this->dbConnect();
        $this->requires('atk','4.2.0');



        $this->pathfinder->addLocation('.',array(
        		'addons'=>array('atk4-addons','addons'),
        		'php'=>array('shared'),
        //		'css'=>'templates/thevillagesite2/css',
        //		'js'=>'templates/js',
        ))
        ->setParent($this->pathfinder->base_location);
        
        $this->add('jUI');
        
        $this->auth=$this->add('DealerAuth');

        // Create different menus
        if($this->auth->isLoggedIn()){
        	// menu for registered user
       		$this->add('Menu',null,'Menu')
	            ->addMenuItem('evaluations','My evaluations')
       			->addMenuItem('cars','Cars')
       			->addMenuItem('logout')
        		;
        	
        } else { // Menu for guest
	        $this->add('Menu',null,'Menu')
	            ->addMenuItem('index','Welcome')
	            ;
        }
    }
}
