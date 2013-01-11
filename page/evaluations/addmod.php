<?php
class page_evaluations_addmod extends Page {
    function init(){
        parent::init();
        if(!$this->api->auth->isLoggedIn()){
        	$this->api->redirect('login');
        }
        
        $form=$this->add('Form_Addmod');
    }
    
    function defaultTemplate(){
    	return array('page/evaluations/addmod');
    }
    
}

class Form_Addmod extends Form {
	function init(){
		parent::init();

		$model_make = $this->add('Model_Make');
		$model = $this->add('Model_Mod');

		// Form fields from the table
		$this->setModel($model,array('name'));
		
		// Getting all makes ordered
		$a_makes=$model_make->tree();
		
		// We have to set selected value for make dropdown
		if($_GET['make_id'] && $_GET['make_id']>0){
			$first_make_id=$_GET['make_id'];
		}else{
			$first_make_id=$model_make->first_make_id();
		}
		
		// Creating dropdown with makes
		$this->f_make=$this->addField('dropdown','make_id')
    					->setValueList($a_makes)
    					->set($first_make_id)
    					->validateNotNull()
						->addClass('f_dropdown');
		
		$this->add('Order')->move('name','after','make_id')->now();
		
		$this->addSubmit('Submit');

		$this->onSubmit(array($this,'checkForm'));
	}
	function checkForm(){
		$js=array();
		$is_form_valid=true;

		// Checking if the make exists in DB
		$mod = $this->add('Model_Mod')->addCondition('name',$this->get('name'))->addCondition('make_id',$this->get('make_id'))->tryLoadAny();
		if ($mod->loaded()) {
			$is_form_valid=false;
		}
		
		if ($is_form_valid) {
			$this->getModel()->set('make_id',$this->get('make_id'));
			$this->update();
				
			$mod = $this->add('Model_Mod')->addCondition('name',$this->get('name'))->addCondition('make_id',$this->get('make_id'))->tryLoadAny();
		}
		
		$this->api->redirect('evaluations/add',array('make_id'=>$this->get('make_id'),'mod_id'=>$mod->get('id')));
	}
}