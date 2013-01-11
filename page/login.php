<?php
class page_login extends Page {
    function init(){
        parent::init();
        
        $form = $this->add('Form',null,'LoginForm');
        $form->setFormClass('vertical');
        $form->addField('line','login');
        $form->addField('password','password');
        $form->addSubmit('Login');
        $form->addButton('Forgot password')->js('click')->univ()->location($this->api->getDestinationURL('forgot'));
        $form->addButton('Sign up')->js('click')->univ()->location($this->api->getDestinationURL('register/index'));
        
        if($form->isSubmitted()){
        
        	$auth=$this->api->auth;
        	$l=$form->get('login');
        	$p=$form->get('password');
        	
        	$enc_p = $auth->encryptPassword($p,$l);
        	// Manually encrypt password
        	if($auth->verifyCredentials($l,$enc_p)){
        		// Checking if the account's email is verified 
        		$um = $this->add('Model_User')->addCondition('email',$l)->tryLoadAny();
        		if ($um->get('is_verified')) {
        			// if account approved by admin or the account is admin - login
        			if ($um->get('is_approved') || $um->get('is_admin')) {
        				// Manually log-in
        				$auth->login($l);
        				$form->js()->univ()->redirect('index')->execute();
        			}
        		} else {
        			$form->getElement('login')->displayFieldError('Email of this account is not verified!');
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
