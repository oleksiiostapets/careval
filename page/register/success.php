<?php
class page_register_success extends Page {
    function init(){
        parent::init();

        $this->template->setHTML('title','Thank you');
        $this->add('View_Info')->set('Your account registered and email verification message has been sent. Please follow the instructions in the message.');
    }
    function defaultTemplate(){
    	return array('page/register/success');
    }
}

