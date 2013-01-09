<?php
class page_register_index extends Page {
    function init(){
        parent::init();

        // Check if user is already logged in and redirect to home
        if ($this->api->auth->isLoggedIn()) {
        
        	$this->api->redirect('/');
        }

        // Local variables
        $form=$this->add('Form_Register');
    }
    function defaultTemplate(){
    	return array('page/register/index');
    }
}

class Form_Register extends Form {
	function init(){
		parent::init();

		$model = $this->add('Model_User');
		
		// Form fields from the table user
		$this->setModel($model, array('name','email','company','phone'));
		$this->addField('password', 'password_1', 'Password');
		$this->addField('password', 'password_2', 'Confirmation');
		
		$this->api->auth->addEncryptionHook($model);
		
		// Form submit button
		$this->addSubmit('Register');
		
		$this->onSubmit(array($this,'checkForm'));
	}
	function checkForm(){
		$js=array();
		$is_form_valid=true;
		
		// Check if passwords not empty
		if (trim($this->get('password_1')) <> "" | trim($this->get('password_2') <> "")) {
			// Check if passwords match
			if ($this->get('password_1') != $this->get('password_2')) {
				$is_form_valid=false;
				$this->js()->atk4_form('fieldError','password_2','Passwords does not match')->execute();
			}
		} else { // If password fields are empty
			$is_form_valid=false;
			$this->js()->atk4_form('fieldError','password_1','Please enter a password')->execute();
		}

		// Check if email format is correct
		if(!filter_var($this->get('email'),FILTER_VALIDATE_EMAIL)){
			$is_form_valid=false;
			$this->js()->atk4_form('fieldError','email','Wrong email format')->execute();
		} else {
			// Validate if email exists in the table
			$um = $this->add('Model_User')->addCondition('email',$this->get('email'))->tryLoadAny();
			if ($um->loaded()) {
				$this->js()->atk4_form('fieldError','email','This email exist in our database.')->execute();
				$is_form_valid=false;
			}
		}
		
		// If form valid - save user
		if ($is_form_valid) {
			$this->getModel()->set('created', date('Y-m-d G:i:s',time()));
			
			// Set encrypted password
	        $this->getModel()->set('password', $this->api->auth->encryptPassword($this->get('password_1'),$this->get('email')));

	        // Generating hash for email verification
			$confirm_hash = md5(uniqid(rand(), true));
			$this->getModel()->set('verification', $confirm_hash);
			
			$this->update();

			// Sending message for verification email
			$mail = $this->add('TMail');
			$mail->loadTemplate('verification');
			$mail->setTag('name',$this->get('name'));
			$mail->setTag('link','http://'.$_SERVER['HTTP_HOST'].'/?page=register/confirm&uid='.$this->getModel()->get('id').'&confirm='.$confirm_hash);
			$mail->send($this->get('email'));
			
			
			$this->api->redirect('register/success');
		}
	}
}