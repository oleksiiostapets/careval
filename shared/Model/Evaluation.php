<?php
class Model_Evaluation extends Model_Table {
	public $table="evaluation";
	function init(){
		parent::init();
		
		$this->addField('value')->mandatory('Value cannot be empty');
		$this->hasOne('Car');
		$this->hasOne('User');
		$this->hasOne('Make','make_id')->display(array('form'=>'autocomplete/basic'));
		$this->hasOne('Mod','mod_id')->display(array('form'=>'autocomplete/basic'));
		
	}
}