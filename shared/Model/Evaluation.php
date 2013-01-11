<?php
class Model_Evaluation extends Model_Table {
	public $table="evaluation";
	function init(){
		parent::init();
		
		$this->addField('value')->mandatory('Value cannot be empty');
		$this->addField('created');
		$this->addField('description');

		$this->hasOne('Car');
		$this->hasOne('User');

		$this->addExpression('make_name')->set(function($m,$q){
			return $q->dsql()
			->table('make')
			->table('mod')
			->table('car')
			->field('make.name')
			->where('make.id',$q->expr('mod.make_id'))
			->where('mod.id',$q->expr('car.mod_id'))
			->where('car.id',$q->getField('car_id'))
			;
		});
		
		$this->addExpression('model_name')->set(function($m,$q){
			return $q->dsql()
			->table('mod')
			->table('car')
			->field('mod.name')
			->where('mod.id',$q->expr('car.mod_id'))
			->where('car.id',$q->getField('car_id'))
			;
		});
			
		$this->addExpression('year')->set(function($m,$q){
			return $q->dsql()
			->table('car')
			->field('car.year')
			->where('car.id',$q->getField('car_id'))
			;
		});
		
		$this->addExpression('regnumber')->set(function($m,$q){
			return $q->dsql()
			->table('car')
			->field('car.regnumber')
			->where('car.id',$q->getField('car_id'))
			;
		});
			
		$this->addExpression('user')->set(function($m,$q){
			return $q->dsql()
			->table('user')
			->field('user.name')
			->where('user.id',$q->getField('user_id'))
			;
		});
			
		$this->addHook('beforeSave',function($m){
			$m['created']=date('Y-m-d G:i:s',time());
		});
	}
}