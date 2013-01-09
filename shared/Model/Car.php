<?php
class Model_Car extends Model_Table {
	public $table="car";
	function init(){
		parent::init();
		
		$this->addField('regnumber')->mandatory('Regnumber cannot be empty');
		$this->hasOne('Mod')->display(array('form'=>'autocomplete/basic'));
		$this->hasMany('Evaluation');
		
	}
}