<?php
class page_management extends Page {
	function page_index(){

		$is_admin = $this->api->auth->model['is_admin'];

		if(!$is_admin)$this->api->redirect('login');

		// Creating management tabs
		$tabs = $this->add('Tabs');

// Tab users *************************/
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
				$mail->setTag('link','http://'.$_SERVER['HTTP_HOST'].'/');
				$mail->send($um->get('email'));
				
				$um->saveAndUnload();
				
				$this->js()->univ()->successMessage('New password generated and sent to user.')->execute();
			}			
			$user_crud->grid->addClass('zebra bordered');
		}

// Tab makes *************************/
		
		$tab = $tabs->addTab('Makes');
		$make_crud=$tab->add('CRUD',array('allow_add'=>true,'allow_edit'=>true,'allow_del'=>true));

		$model_make = $this->add('Model_Make');
		
		$make_crud->setModel($model_make, array('name','models_count'));
		
		if($make_crud->grid){
			$make_crud->grid->addPaginator();

			$make_crud->grid->addClass('zebra bordered');
		}
				
// Tab models *************************/
		
		$tab = $tabs->addTab('Models');
		$mod_crud=$tab->add('CRUD',array('allow_add'=>false,'allow_edit'=>true,'allow_del'=>true));
		
		$model_mod = $this->add('Model_Mod');
		
		$mod_crud->setModel($model_mod, array('make_name','name','cars_count'));
		
		if($mod_crud->grid){
			$mod_crud->grid->addPaginator();
		
			$mod_crud->grid->addClass('zebra bordered');
		}
		
// Tab cars *************************/
		
		$tab = $tabs->addTab('Cars');
		$car_crud=$tab->add('CRUD',array('allow_add'=>false,'allow_edit'=>true,'allow_del'=>true));
		
		$model_car = $this->add('Model_Car');
		
		$car_crud->setModel($model_car, array('make_name','model_name','year','regnumber','evaluations_count'));
		
		if($car_crud->grid){
			$car_crud->grid->addPaginator();
		
			$car_crud->grid->addClass('zebra bordered');
		}
		
// Tab evaluations *************************/
		
		$tab = $tabs->addTab('Evaluations');
		$evaluations_crud=$tab->add('CRUD',array('allow_add'=>false,'allow_edit'=>true,'allow_del'=>true));
		
		$model_evaluations = $this->add('Model_Evaluation');
		
		$evaluations_crud->setModel($model_evaluations, array('user','make_name','model_name','year','regnumber','value','description','created'));
		
		if($evaluations_crud->grid){
			$evaluations_crud->grid->addPaginator();
		
			$evaluations_crud->grid->addClass('zebra bordered');
		}
		
	}

	
}
