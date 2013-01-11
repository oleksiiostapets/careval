<?php
class Model_Car extends Model_Table {
	public $table="car";
	function init(){
		parent::init();
		
		$this->addField('regnumber')->mandatory('Regnumber cannot be empty');
		$this->addField('year');

		$this->hasOne('Mod');
		$this->hasMany('Evaluation');

		$this->addExpression('make_name')->set(function($m,$q){
			return $q->dsql()
					->table('make')
					->table('mod')
					->field('make.name')
					->where('make.id',$q->expr('mod.make_id'))
					->where('mod.id',$q->getField('mod_id'))
			;
		});
		
		$this->addExpression('model_name')->set(function($m,$q){
			return $q->dsql()
					->table('mod')
					->field('name')
					->where('id',$q->getField('mod_id'));
		});
		
		$this->addExpression('evaluations_count')->set(function($m,$q){
            return $m->refSQL('Evaluation')->count();
        });		
	}
}