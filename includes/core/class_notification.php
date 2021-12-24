<?php

class Notification {

    // TEST
	
	public static function user_notification($data, $not_viewed_only = false, $is_read = false) {
        // vars
        $user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? $data['user_id'] : 0;
        // where
        if ($user_id) $where = "user_id='".$user_id."'";
        else return $is_read ? '' : [];
		if($not_viewed_only) $where .= " and viewed=0";
        // info
        $q = DB::query("SELECT `title`,`description`,`created`,`viewed` FROM `user_notifications` WHERE ".$where.";") or die (DB::error());
        if ($rows = DB::fetch_all($q)) {
            return $is_read ? 'Уведомления успешно прочитаны.' : $rows;
        }
		return $is_read ? : [];
    }

	//return array

    public static function owner_get($data = []) {
        if(!empty(Session::$user_id)){
			$not_viewed_only = !empty($data['not_viewed_only']) ? true : false;
			return self::user_notification(['user_id' => Session::$user_id], $not_viewed_only);
		}else{
			return error_response(3001, 'Get owner notification: invalid owner user.');
		}
    }
	
	//only read
	
	public static function owner_read() {
        if(!empty(Session::$user_id)){
			return self::user_notification(['user_id' => Session::$user_id], false, true);
		}else{
			return error_response(3001, 'Get owner notification: invalid owner user.');
		}
    }

}
