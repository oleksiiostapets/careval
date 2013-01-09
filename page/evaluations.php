<?php
class page_evaluations extends Page {
    function init(){
        parent::init();
        if(!$this->api->auth->isLoggedIn()){
        	$this->api->redirect('login');
        }
        
        $form=$this->add('Form_Evaluation');
    }
    function defaultTemplate(){
    	return array('page/evaluations');
    }
    
}

class Form_Evaluation extends Form {
	function init(){
		parent::init();

		$model = $this->add('Model_Evaluation');

		// Form fields from the table
		$this->setModel($model,array('value'));

		$this->f_make=$this->addField('autocomplete/plus','make_id');
		$this->f_make->setModel('Make');
		$this->f_model=$this->addField('autocomplete/plus','mod_id','Model');
		$this->f_model->setModel('Mod');
		
		$this->add('Order')->move('value','after','mod')->now();
		
		
		$this->addSubmit('Submit');
		

		$this->onSubmit(array($this,'checkForm'));
	}
	function checkForm(){
		$js=array();
		$is_form_valid=true;

		// Check reg number not empty
		if (trim($this->get('regnumber')) == "") {
			$is_form_valid=false;
			$this->js()->atk4_form('fieldError','regnumber','Please enter a Reg number')->execute();
		}

		if ($is_form_valid) {
			//$this->js()->atk4_form('fieldError','regnumber',$this->get('regnumber'))->execute();
			
			$car = $this->add('Model_Car')->addCondition('regnumber',$this->get('regnumber'))->tryLoadAny();
			if ($car->loaded()) {
				$this->getModel()->set('car_id',$car->get('id'));
			}
			$car->unload();
			$this->getModel()->set('user_id',$this->api->auth->model['id']);
			//$this->getModel()->set('value',$this->get('value'));
			$this->update();
				
//			$this->api->redirect('register/success');
		}
	}
}