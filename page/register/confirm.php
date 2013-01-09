<?php
class page_register_confirm extends Page {
    function init(){
        parent::init();

        // Validate if we have needed parameters
        if ($_GET['uid'] && $_GET['confirm']) {
        	$um = $this->add('Model_User')->addCondition('id',$_GET['uid'])->addCondition('verification',$_GET['confirm'])->tryLoadAny();
        	if ($um->loaded()) {
        		$um->setVerify(true);
		        $this->template->setHTML('title','Success verification!');
		        $this->add('View_Info')->set('Your email has been verified. Please wait for administrator approval.');
        	}else{
        		$this->template->setHTML('title','Error!');
        		$this->add('View_Info')->set('Wrong parameters.');
        	}
        } else {
        	$this->template->setHTML('title','Error!');
        	$this->add('View_Info')->set('Wrong parameters.');
        }
    }
    function defaultTemplate(){
    	return array('page/register/confirm');
    }
}

