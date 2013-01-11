<?php
class page_evaluations_add extends Page {
    function init(){
        parent::init();
        if(!$this->api->auth->isLoggedIn()){
        	$this->api->redirect('login');
        }
        
        $form=$this->add('Form_Evaluation');
    }
    function defaultTemplate(){
    	return array('page/evaluations/add');
    }
    
}

class Form_Evaluation extends Form {
	function init(){
		parent::init();

		// Model for makes to get tree of makes and models
		$model = $this->add('Model_Make');

		// Model for form
		$model_form = $this->add('Model_Evaluation');
		
		// Form fields from the table
		$this->setModel($model_form,array('value'));
		$this->addField('line','year','Year');
		$this->addField('Text','description');
		$this->addField('line','regnumber','Reg number');
		
		// Getting all makes ordered
		$a_makes=$model->tree();

		// We have to set selected value for make dropdown
		if($_REQUEST['make_id'] && $_REQUEST['make_id']>0){
			$first_make_id=$_REQUEST['make_id'];
		}else{
			$first_make_id=$model->first_make_id();
		}
		
		// Creating dropdown with makes
		$this->f_make=$this->addField('dropdown','make')
    					->setValueList($a_makes)
    					->set($first_make_id)
    					->validateNotNull()
						->addClass('f_dropdown');
		
		// Button for adding new make
		$add_make_button=$this->add('Button')->set("+")->addClass('add_make');
		$add_make_button->js('click', $this->js()->univ()->redirect('evaluations/addmake'));
		
		// Getting models
		$a_models=$model->mods_tree();

		// if we get AJAX request (when make was changed)
		if($_REQUEST['mod']){
			$a_models=$a_models[$_REQUEST['mod']]?:array();
		// Else if form was submitted we are setting array for models. Otherwise - error
		}elseif ($_REQUEST['CarEval_evaluations_add_form_evaluation_make']){
			$a_models=$a_models[$_REQUEST['CarEval_evaluations_add_form_evaluation_make']];
		}else{
			$a_models=$a_models[$first_make_id];
		}

		// If we came from adding model page - set default model
		$first_model_id='';
		if($_REQUEST['mod_id'] && $_REQUEST['mod_id']>0){
			$first_model_id=$_REQUEST['mod_id'];
		}
		//echo "<pre>";
		//var_dump($a_models[$first_make_id]);
		//echo "</pre>";
		$this->f_model=$this->addField('dropdown','mod','Model')
						->setValueList($a_models)
						->set($first_model_id)
						->validateNotNull()
						->addClass('f_dropdown');

		// Button for adding new model
		$add_model_button=$this->add('Button')->set("+")->addClass('add_model');
		$add_model_button->js('click')->_load('add_model')
    					->univ()->addmod_page($this->f_make->js()->val());
		
		// Script for reloading models with selected make
		$this->f_make->js('change',
				$this->js()->atk4_form('reloadField',
						'mod',
						array($this->api->getDestinationURL(),'mod'=>$this->f_make->js()->val())
		));
		
		$this->add('Order')->move('year','after','mod')->now();
		$this->add('Order')->move('regnumber','after','year')->now();
		$this->add('Order')->move('value','after','regnumber')->now();
		$this->add('Order')->move('description','after','value')->now();
		
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
			// Checking if car with this regnumber exist
			$car = $this->add('Model_Car')->addCondition('regnumber',$this->get('regnumber'))->tryLoadAny();
			// if car is not found - insert to DB
			if (!$car->loaded()) {
				$car=$this->add('Model_Car');
				$car['regnumber']=$this->get('regnumber');
				$car['mod_id']=$this->get('mod');
				$car['year']=$this->get('year');
				$car->save();
			}
			$this->getModel()->set('car_id',$car->get('id'));
			$car->unload();

			$this->getModel()->set('description',$this->get('description'));
			$this->getModel()->set('user_id',$this->api->auth->model->id);
			
			$this->update();
				
			$this->api->redirect('evaluations/list');
		}
	}
}