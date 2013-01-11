<?php
class page_evaluations_list extends Page {
    function init(){
        parent::init();
        if(!$this->api->auth->isLoggedIn()){
        	$this->api->redirect('login');
        }
        
        $model_eval=$this->add('Model_Evaluation');
        $model_eval->addCondition('user_id',$this->api->auth->model->id);
        $model_eval->addField('created at','created');
        $model_eval->setOrder('created','DESC');
        
//        $join_user=$model_eval->leftJoin('user.id','user_id');
//        $join_user->addField('created by','name');
//        $model_eval->removeElement('user');
        
        $car=$model_eval->leftJoin('car.id','car_id');
        $car->addField('car year','year');
        $car->addField('register number','regnumber');
        $model_eval->removeElement('car');
        
        $mod =$car->leftJoin('mod.id','mod_id');
        $mod->addField('model','name');
        
        $make =$mod->leftJoin('make.id','make_id');
        $make->addField('make','name');
        
// DON'T REMOVE
// Example how to use "expr"
/*
        $q=$this->api->db->dsql();
        $q->table(array('e'=>'evaluation','c'=>'car','mod'=>'mod'));
        $q->where('e.car_id',$q->expr('c.id'))->debug();
        //$q->where('c.mod_id',$q->dsql()->table('mod')->field('id'))->debug();
        //$rows=$q->getAll();
        echo"<pre>";
        while($row = $q->fetch()){
        	var_dump($row);
        	// Will loop through results fetching one row at a time. You can access your data through $row['fieldname'];
        }
        echo"</pre>";
        */

        $add_evaluation_button=$this->add('Button')->set("Add Evaluation");
        $add_evaluation_button->js('click',$this->js()->univ()->redirect('evaluations/add'));
        
        $grid=$this->add('Grid');
        $grid->setModel($model_eval,array('make','model','car year','register number','value','description','created at'));
        //$grid->addColumn('button','test','test');
        //$this->add('Grid')->setModel($model_eval);
    }
    function defaultTemplate(){
    	return array('page/evaluations/list');
    }
    
}
