<?php
class Model_Mod extends Model_Table {
	public $table="mod";
	function init(){
		parent::init();
		
		$this->addField('name')->mandatory('Name cannot be empty');
		//$this->hasOne('Make','make_id','name');
		$this->hasOne('Make')->display(array('form'=>'autocomplete/basic'));
		//$this->hasMany('Car');
		
		$this->addHook('beforeSave',function($m){
			$m['name']=ucfirst($m['name']);
		});
	}
}