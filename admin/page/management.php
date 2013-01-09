<?php
class page_management extends Page {
	function page_index(){

		$is_admin = $this->api->auth->model['is_admin'];

		if(!$is_admin)$this->api->redirect('login');

		// Creating management tabs
		$tabs = $this->add('Tabs');

		$tab = $tabs->addTab('Users');

		$user_crud=$tab->add('CRUD');
		
		$model = $this->add('Model_User');
		
		$user_crud->setModel($model, array('name','email','company','phone','is_approved','is_verified','is_admin'));
		$this->api->auth->addEncryptionHook($model);
		
		if($user_crud->grid){
			$user_crud->grid->addPaginator();
			$user_crud->grid->getColumn('name')->makeSortable();
			$user_crud->grid->getColumn('email')->makeSortable();
			
			// Generate new password functionality
			$user_crud->grid->addColumn('button','generate','Generate password');
			if($_GET['generate']){
				// Generating new temporary password
				$password = rand(100,999);
				
				// Getting user
				$um = $model->addCondition('id',$_GET['generate'])->tryLoadAny();
				// Generating hash and saving new password hash
				$um->set('password', $this->api->auth->encryptPassword($password,$um->get('email')));
				
				// Sending message information
				$mail = $this->add('TMail');
				$mail->loadTemplate('changepass');
				$mail->setTag('name',$um->get('name'));
				$mail->setTag('password',$password);
				$mail->send($um->get('email'));
				
				$um->saveAndUnload();
				
				$this->js()->univ()->successMessage('New password generated and sent to user.')->execute();
			}			
			$user_crud->grid->addClass('zebra bordered');
		}
	}

}
