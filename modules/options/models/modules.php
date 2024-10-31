<?php
class modulesModelSln extends modelSln {
    public function get($d = array()) {
        if($d['id'] && is_numeric($d['id'])) {
            $fields = frameSln::_()->getTable('modules')->fillFromDB($d['id'])->getFields();
            $fields['types'] = array();
            $types = frameSln::_()->getTable('modules_type')->fillFromDB();
            foreach($types as $t) {
                $fields['types'][$t['id']->value] = $t['label']->value;
            }
            return $fields;
        } elseif(!empty($d)) {
            $data = frameSln::_()->getTable('modules')->get('*', $d);
            return $data;
        } else {
            return frameSln::_()->getTable('modules')
                ->innerJoin(frameSln::_()->getTable('modules_type'), 'type_id')
                ->getAll(frameSln::_()->getTable('modules')->alias().'.*, '. frameSln::_()->getTable('modules_type')->alias(). '.label as type');
        }
    }
    public function put($d = array()) {
        $res = new responseSln();
        $id = $this->_getIDFromReq($d);
        $d = prepareParamsSln($d);
        if(is_numeric($id) && $id) {
            if(isset($d['active']))
                $d['active'] = ((is_string($d['active']) && $d['active'] == 'true') || $d['active'] == 1) ? 1 : 0;           //mmm.... govnokod?....)))
           /* else
                 $d['active'] = 0;*/
            
            if(frameSln::_()->getTable('modules')->update($d, array('id' => $id))) {
                $res->messages[] = __('Module Updated', SLN_LANG_CODE);
                $mod = frameSln::_()->getTable('modules')->getById($id);
                $newType = frameSln::_()->getTable('modules_type')->getById($mod['type_id'], 'label');
                $newType = $newType['label'];
                $res->data = array(
                    'id' => $id, 
                    'label' => $mod['label'], 
                    'code' => $mod['code'], 
                    'type' => $newType,
                    'params' => utilsSln::jsonEncode($mod['params']),
                    'description' => $mod['description'],
                    'active' => $mod['active'], 
                );
            } else {
                if($tableErrors = frameSln::_()->getTable('modules')->getErrors()) {
                    $res->errors = array_merge($res->errors, $tableErrors);
                } else
                    $res->errors[] = __('Module Update Failed', SLN_LANG_CODE);
            }
        } else {
            $res->errors[] = __('Error module ID', SLN_LANG_CODE);
        }
        return $res;
    }
    protected function _getIDFromReq($d = array()) {
        $id = 0;
        if(isset($d['id']))
            $id = $d['id'];
        elseif(isset($d['code'])) {
            $fromDB = $this->get(array('code' => $d['code']));
            if($fromDB[0]['id'])
                $id = $fromDB[0]['id'];
        }
        return $id;
    }
}
