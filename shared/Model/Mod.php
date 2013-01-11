<?php
class Model_Mod extends Model_Table {
	public $table="mod";
	function init(){
		parent::init();
		
		$this->addField('name')->mandatory('Name cannot be empty');
		$this->addField('reviewed');
		
		$this->hasOne('Make');
		$this->hasMany('Car');
		
		$this->addExpression('make_name')->set(function($m,$q){
			return $q->dsql()
					->table('make')
					->field('name')
					->where('id',$q->getField('make_id'));
		});
		
		$this->addExpression('cars_count')->set(function($m,$q){
			return $m->refSQL('Car')->count();
		});
		
		$this->addHook('beforeSave',function($m){
			$m['name']=ucfirst($m['name']);
			if($m->api->auth->model['is_admin']){
				$m['reviewed']=true;
			}
		});
	}
}