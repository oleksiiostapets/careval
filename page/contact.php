<?php
class page_contact extends Page {
    function init(){
        parent::init();

        $form = $this->add('Form');
        $form->setFormClass('vertical');
        $form->addField('Text','message');

        $form->addSubmit('Send');
        
        if($form->isSubmitted()){
        	
        	if (trim($form->get('message'))!='') {
        		$from=$this->api->auth->model['email'];
        		$name=$this->api->auth->model['name'];
        		$to=$this->api->getConfig('admin/email','test@test.com');
         
        		// Sending message for verification email
        		$mail = $this->add('TMail');
        		$mail->loadTemplate('contact');
        		$mail->setTag('email',$from);
        		$mail->setTag('name',$name);
        		$mail->setTag('content',$form->get('message'));
        		$mail->send($to);
        			
        		$this->api->redirect('/');
        	}
        	 
        }
        
    }
    function defaultTemplate(){
    	return array('page/contact');
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