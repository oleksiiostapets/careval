<?php
class DealerAuth extends BasicAuth {
    function init(){
        parent::init();
        $this->setModel('User');
        $this->usePasswordEncryption('sha256/salt');
    }
}
