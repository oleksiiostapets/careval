<?php
class page_cars_list extends Page {
    function init(){
        parent::init();
        if(!$this->api->auth->isLoggedIn()){
        	$this->api->redirect('login');
        }

        $model_car=$this->add('Model_Car');
        $model_car->addField('register number','regnumber');
        $model_car->setOrder('year','DESC');
        
        $mod =$model_car->leftJoin('mod.id','mod_id');
        $mod->addField('model','name');
        
        $make =$mod->leftJoin('make.id','make_id');
        $make->addField('make','name');
        
        $grid=$this->add('Grid');
        $grid->setModel($model_car,array('make','model','year','register number','evaluations_count'));
        $grid->addColumn('button','evaluations','See evaluations');
        
        if($_GET['evaluations']){
        	$this->js()->univ()->redirect('cars/evals?car_id='.$_GET['evaluations'])->execute();
        }
    }
    function defaultTemplate(){
    	return array('page/cars/list');
    }
    
}
