<?php
class page_index extends Page {
    function init(){
        parent::init();

   		$is_admin = $this->api->auth->model['is_admin'];

		if(!$is_admin)$this->api->redirect('login');

		// Creating management tabs
		$tabs=$this->add('Tabs');

// Tab signups *************************/
		$tab=$tabs->addTab('Signups');

		$user_crud=$tab->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
				
		$model=$this->add('Model_User');
		$model->addCondition('is_approved',null);
		$user_crud->setModel($model, array('name','email','company','phone','is_verified'));

		if($user_crud->grid){
			$user_crud->grid->addPaginator();
			
			// Approve user
			$user_crud->grid->addColumn('button','approve','Approve');
			
			if($_GET['approve'] && $_GET['AdminCarEval_index_tabs_view_htmlelement_crud_grid_approve']){
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
				
				$user_crud->grid->js()->reload()->execute();
			}
			
			// Reject user
			$user_crud->grid->addColumn('button','reject','Reject');
			if($_GET['reject'] && $_GET['AdminCarEval_index_tabs_view_htmlelement_crud_grid_reject']){
				// Getting user
				$um = $model->addCondition('id',$_GET['reject'])->tryLoadAny();
				
				// Sending message for verification email
				$mail = $this->add('TMail');
				$mail->loadTemplate('reject');
				$mail->setTag('name',$um->get('name'));
				$mail->send($um->get('email'));

				$um->delete();
				
				$user_crud->grid->js()->reload()->execute();
			}
			$user_crud->grid->addClass('zebra bordered');
		}
		
// Tab makes *************************/
		
		$tab = $tabs->addTab('Makes');
		$make_crud=$tab->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
		
		$model_make = $this->add('Model_Make')->addCondition('reviewed',null);
		
		$make_crud->setModel($model_make, array('name','models_count'));
		
		if($make_crud->grid){
			$make_crud->grid->addPaginator();
				
			// Approve make
			$make_crud->grid->addColumn('button','approve','Approve');
			if($_GET['approve'] && $_GET['AdminCarEval_index_tabs_view_htmlelement_2_crud_grid_approve']){
				// Getting make
				$make_row = $model_make->addCondition('id',$_GET['approve'])->tryLoadAny();
				// Set approve
				$make_row->set('reviewed',true);
		
				$make_row->saveAndUnload();
		
				$make_crud->grid->js()->reload()->execute();
			}

			// Reject make
			$make_crud->grid->addColumn('button','reject','Reject');
			if($_GET['reject'] && $_GET['AdminCarEval_index_tabs_view_htmlelement_2_crud_grid_reject']){
				// Getting make
				$make_row = $model_make->addCondition('id',$_GET['reject'])->tryLoadAny();
		
				$make_row->delete();
		
				$make_crud->grid->js()->reload()->execute();
			}

			$make_crud->grid->addClass('zebra bordered');
		}
		
    // Tab models *************************/
		
		$tab = $tabs->addTab('Models');
		$mod_crud=$tab->add('CRUD',array('allow_add'=>false,'allow_edit'=>false,'allow_del'=>false));
		
		$model_mod = $this->add('Model_Mod')->addCondition('reviewed',null);
		
		$mod_crud->setModel($model_mod, array('make_name','name','cars_count'));
		
		if($mod_crud->grid){
			$mod_crud->grid->addPaginator();
				
			// Approve mod
			$mod_crud->grid->addColumn('button','approve','Approve');
			if($_GET['approve'] && $_GET['AdminCarEval_index_tabs_view_htmlelement_3_crud_grid_approve']){
				// Getting mod
				$mod_row = $model_mod->addCondition('id',$_GET['approve'])->tryLoadAny();
				// Set approve
				$mod_row->set('reviewed',true);
		
				$mod_row->saveAndUnload();
		
				$mod_crud->grid->js()->reload()->execute();
			}

			// Reject mod
			$mod_crud->grid->addColumn('button','reject','Reject');
			if($_GET['reject'] && $_GET['AdminCarEval_index_tabs_view_htmlelement_3_crud_grid_reject']){
				// Getting mod
				$mod_row = $model_mod->addCondition('id',$_GET['reject'])->tryLoadAny();
		
				$mod_row->delete();
		
				$mod_crud->grid->js()->reload()->execute();
			}

			$mod_crud->grid->addClass('zebra bordered');
		}
		
    }

    function defaultTemplate(){
    	return array('page/index');
    }
}
