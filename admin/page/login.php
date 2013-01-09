<?php
class page_login extends Page {
    function init(){
        parent::init();
        
        $form = $this->add('Form',null,'LoginForm');
        $form->setFormClass('vertical');
        $form->addField('line','login');
        $form->addField('password','password');
        $form->addSubmit('Login');
        
        if($form->isSubmitted()){
        
        	$auth=$this->api->auth;
        	$l=$form->get('login');
        	$p=$form->get('password');

        	$enc_p = $auth->encryptPassword($p,$l);
        	// Manually encrypt password
        	if($auth->verifyCredentials($l,$enc_p)){
        		// Checking if the account's email is verified 
        		$um = $this->add('Model_User')->addCondition('email',$l)->tryLoadAny();
       			// if the account is admin - login
        		if ($um->get('is_admin')) {
        			// Manually log-in
        			$auth->login($l);
        			$form->js()->univ()->redirect('index')->execute();
        		} else {
        			$form->getElement('login')->displayFieldError('You are not allowed for sign in!');
        		}
        	} else {
        		$form->getElement('password')->displayFieldError('Incorrect login');
        	}
        }
        
    }
    function defaultTemplate(){
    	return array('page/login');
    }
    
}
