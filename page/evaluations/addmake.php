<?php
class page_evaluations_addmake extends Page {
    function init(){
        parent::init();
        if(!$this->api->auth->isLoggedIn()){
        	$this->api->redirect('login');
        }
        
        $form=$this->add('Form_Addmake');
    }
    
    function defaultTemplate(){
    	return array('page/evaluations/addmake');
    }
    
}

class Form_Addmake extends Form {
	function init(){
		parent::init();

		$model = $this->add('Model_Make');

		// Form fields from the table
		$this->setModel($model,array('name'));
		
		$this->addSubmit('Submit');

		$this->onSubmit(array($this,'checkForm'));
	}
	function checkForm(){
		$js=array();
		$is_form_valid=true;

		// Checking if the make exists in DB
		$make = $this->add('Model_Make')->addCondition('name',$this->get('name'))->tryLoadAny();
		if ($make->loaded()) {
			$is_form_valid=false;
		}
		
		if ($is_form_valid) {
			$this->update();
				
			$make = $this->add('Model_Make')->addCondition('name',$this->get('name'))->tryLoadAny();
		}
		
		$this->api->redirect('evaluations/add',array('make_id'=>$make->get('id')));
	}
}