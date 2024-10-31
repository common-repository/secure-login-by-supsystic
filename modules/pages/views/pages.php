<?php
class pagesViewSln extends viewSln {
    public function displayDeactivatePage() {
        $this->assign('GET', reqSln::get('get'));
        $this->assign('POST', reqSln::get('post'));
        $this->assign('REQUEST_METHOD', strtoupper(reqSln::getVar('REQUEST_METHOD', 'server')));
        $this->assign('REQUEST_URI', basename(reqSln::getVar('REQUEST_URI', 'server')));
        parent::display('deactivatePage');
    }
}

