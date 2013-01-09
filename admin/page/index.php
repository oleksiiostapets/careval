<?php
class page_index extends Page {
    function init(){
        parent::init();

   		$is_admin = $this->api->auth->model['is_admin'];

		if(!$is_admin)$this->api->redirect('login');

		// Creating management tabs
		$tabs = $this->add('Tabs');

		$tab = $tabs->addTab('Signups');

		$user_crud=$tab->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
				
		$model = $this->add('Model_User')->addCondition('is_approved',false);
		
		$user_crud->setModel($model, array('name','email','company','phone','is_verified'));
		
		if($user_crud->grid){
			$user_crud->grid->addPaginator();
			
			// Approve user
			$user_crud->grid->addColumn('button','approve','Approve');
			if($_GET['approve']){
				// Getting user
				$um = $model->addCondition('id',$_GET['approve'])->tryLoadAny();
				// Set approve
				$um->set('is_approved',true);
				
				// Sending message for verification email
				$mail = $this->add('TMail');
				$mail->loadTemplate('approve');
				$mail->setTag('name',$um->get('name'));
				$mail->send($um->get('email'));

				$um->saveAndUnload();
				
				$this->js()->univ()->redirect('index')->execute();
			}
			
			// Reject user
			$user_crud->grid->addColumn('button','reject','Reject');
			if($_GET['reject']){
				// Getting user
				$um = $model->addCondition('id',$_GET['reject'])->tryLoadAny();
				
				// Sending message for verification email
				$mail = $this->add('TMail');
				$mail->loadTemplate('reject');
				$mail->setTag('name',$um->get('name'));
				$mail->send($um->get('email'));

				$um->delete();
				
				$this->js()->univ()->redirect('index')->execute();
			}
			
			$user_crud->grid->addClass('zebra bordered');
		}
    }

    function defaultTemplate(){
    	return array('page/index');
    }
}
