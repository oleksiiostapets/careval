<?php
class page_cars_evals extends Page {
    function init(){
        parent::init();
        if(!$this->api->auth->isLoggedIn()){
        	$this->api->redirect('login');
        }
		
        if(!$_REQUEST['car_id']){
        	$this->api->redirect('cars/list');
        }

        $model_eval=$this->add('Model_Evaluation');
        $model_eval->addCondition('car_id',$_REQUEST['car_id']);
        $model_eval->addField('created at','created');
        $model_eval->setOrder('created','DESC');
        
        $car=$model_eval->leftJoin('car.id','car_id');
        $car->addField('car year','year');
        $car->addField('register number','regnumber');
        $model_eval->removeElement('car');
        
        $mod =$car->leftJoin('mod.id','mod_id');
        $mod->addField('model','name');
        
        $make =$mod->leftJoin('make.id','make_id');
        $make->addField('make','name');
        
        $car_entry=$model_eval->tryLoadAny();
        if (!$car_entry->loaded()) {
        	$this->api->redirect('cars/list');
        }
        
        $this->template->setHTML('Make',$car_entry->get('make'));
        $this->template->setHTML('Model',$car_entry->get('model'));
        $this->template->setHTML('Year',$car_entry->get('car year'));
        $this->template->setHTML('Regnumber',$car_entry->get('register number'));
        
        $grid=$this->add('Grid');
        $grid->setModel($model_eval,array('value','description','created at'));
        
    }
    function defaultTemplate(){
    	return array('page/cars/evals');
    }
    
}
