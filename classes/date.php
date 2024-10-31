<?php
class dateSln {
	static public function _($time = NULL) {
		if(is_null($time)) {
			$time = time();
		}
		return date(SLN_DATE_FORMAT_HIS, $time);
	}
}