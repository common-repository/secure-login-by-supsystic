<?php
class tableBlacklistSln extends tableSln {
    public function __construct() {
        $this->_table = '@__blacklist';
        $this->_id = 'id';
        $this->_alias = 'sln_blacklist';
        $this->_addField('id', 'text', 'int')
				->_addField('ip', 'text', 'varchar')
				->_addField('type', 'text', 'int')
				->_addField('date_created', 'text', 'varchar')
				->_addField('is_temp', 'text', 'int');
    }
}
