<?php
class Model_User extends Model_Table {
	public $table="user";
	function init(){
		parent::init();
		
		$this->addField('name')->mandatory('Name cannot be empty');
		$this->addField('email')->mandatory('Email cannot be empty');
		$this->addField('password')->display(array('form'=>'password'))->mandatory('Type your password');
		$this->addField('company');
		$this->addField('phone');
		$this->addField('created');
		$this->addField('is_admin')->type('boolean');
		$this->addField('verification');
		$this->addField('is_verified')->type('boolean');
		$this->addField('is_approved')->type('boolean');
		$this->hasMany('Evaluation');
		
		$this->addHook('beforeSave',function($m){
			$m['created']=date('Y-m-d G:i:s',time());
			$m['email']=strtolower($m['email']);
		});
	}

	function setVerify($is_verify){
		$this->set('is_verified',$is_verify);
		return $this->saveAndUnload();
	}
}