<?php
class tableEmail_auth_codesSln extends tableSln {
    public function __construct() {
        $this->_table = '@__email_auth_codes';
        $this->_id = 'id';
        $this->_alias = 'sln_email_auth_codes';
        $this->_addField('id', 'text', 'int')
				->_addField('code', 'text', 'varchar')
				->_addField('uid', 'text', 'int')
				->_addField('sent_time', 'text', 'int');
    }
}
