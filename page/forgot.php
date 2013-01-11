<?php
class page_forgot extends Page {
    function init(){
        parent::init();

        // Check if user is already logged in and redirect to home
        if ($this->api->auth->isLoggedIn()) {
        
        	$this->api->redirect('/');
        }

        $form = $this->add('Form');
        $form->setFormClass('vertical');
        $form->addField('line','email');
        
        $form->addSubmit('Send');
        
        if($form->isSubmitted()){
        	$is_form_valid=true;
        	
        	$model = $this->add('Model_User');
        	$auth=$this->api->auth;
        	$auth->addEncryptionHook($model);
        	
        	$email=$form->get('email');
        	$password = rand(100,999);
        	
        	// Validate if email exists in the table
        	$um = $model->addCondition('email',$email)->tryLoadAny();
        	if (!$um->loaded()) {
        		$this->js()->atk4_form('fieldError','email','This email doesn`t exist in our database.')->execute();
        		$is_form_valid=false;
        	}
        	
        	// If form valid - save user
        	if ($is_form_valid) {
        		// Set encrypted password
        		$um->set('password',$auth->encryptPassword($password,$email));
        	
        		$um->save();
        	
        		// Sending message for verification email
        		$mail = $this->add('TMail');
        		$mail->loadTemplate('forgot');
        		$mail->setTag('password',$password);
        		$mail->setTag('link','http://'.$_SERVER['HTTP_HOST'].'/');
        		$mail->send($email);
        			
        		$this->api->redirect('/');
        	}
        	 
        }
        
    }
    function defaultTemplate(){
    	return array('page/forgot');
    }
}

class Form_Forgot extends Form {
	function init(){
		parent::init();

		$model = $this->add('Model_User');
		
		// Form fields from the table user
		$this->setModel($model, array('email'));
		
		$this->api->auth->addEncryptionHook($model);
		
		// Form submit button
		$this->addSubmit('Send');
		
		$this->onSubmit(array($this,'checkForm'));
	}
	function checkForm(){
		$js=array();
		$is_form_valid=true;
		
		$model = $this->add('Model_User');
		$this->api->auth->addEncryptionHook($model);
		
		// Validate if email exists in the table
		$um = $model->addCondition('email',$this->get('email'))->tryLoadAny();
		if (!$um->loaded()) {
			$this->js()->atk4_form('fieldError','email','This email doesn`t exist in our database.')->execute();
			$is_form_valid=false;
		}
		
		// If form valid - save user
		if ($is_form_valid) {
			$password=1;
			
			// Set encrypted password
	        $um->set('password', $this->api->auth->encryptPassword($password,$this->get('email')));

			$um->save();

			// Sending message for verification email
			$mail = $this->add('TMail');
			$mail->loadTemplate('forgot');
			$mail->setTag('password',$password);
			$mail->setTag('link','http://'.$_SERVER['HTTP_HOST'].'/');
			$mail->send($this->get('email'));
			
			//$this->api->redirect('/');
		}
	}
}