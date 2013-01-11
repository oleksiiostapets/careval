<?php
class Frontend extends ApiFrontend {
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
        	if (strpos($_SERVER['REQUEST_URI'],'evaluations')==0){
        		// Checking if the user entered at least one evaluation
        		$evaluation=$this->add('Model_Evaluation')->addCondition('user_id',$this->api->auth->model['id'])->tryLoadAny();
        		if (!$evaluation->loaded()){
        			$this->api->redirect('evaluations/add');
        		}
        	}
        	
        	// menu for registered user
       		$this->add('Menu',null,'Menu')
       			->addMenuItem('evaluations/list','My evaluations')
	            ->addMenuItem('cars/list','Cars')
	            ->addMenuItem('contact','Contact')
	            ->addMenuItem('logout')
        		;
        	
        } else { // Menu for guest
	        $this->add('Menu',null,'Menu')
	            ->addMenuItem('index','Welcome')
	            ;
        }
    }
    
}
