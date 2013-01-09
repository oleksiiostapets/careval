<?php
class Model_Make extends Model_Table {
	public $table="make";
	function init(){
		parent::init();
		
		$this->addField('name')->mandatory('Name cannot be empty');
		$this->hasMany('Mod');
		
		$this->addHook('beforeSave',function($m){
			$m['name']=ucfirst($m['name']);
		});
	}
}