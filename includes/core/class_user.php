<?php

class User {

    // GENERAL

    public static function user_info($data) {
        // vars
        $user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? $data['user_id'] : 0;
        $phone = isset($data['phone']) ? preg_replace('~[^\d]+~', '', $data['phone']) : 0;
        // where
        if ($user_id) $where = "user_id='".$user_id."'";
        else if ($phone) $where = "phone='".$phone."'";
        else return [];
        // info
        $q = DB::query("SELECT user_id, first_name, last_name, middle_name, email, gender_id, count_notifications, phone FROM users WHERE ".$where." LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => (int) $row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'middle_name' => $row['middle_name'],
                'gender_id' => (int) $row['gender_id'],
                'email' => $row['email'],
                'phone' => (int) $row['phone'],
                'phone_str' => phone_formatting($row['phone']),
                'count_notifications' => (int) $row['count_notifications']
            ];
        } else {
            return [
                'id' => 0,
                'first_name' => '',
                'last_name' => '',
                'middle_name' => '',
                'gender_id' => 0,
                'email' => '',
                'phone' => '',
                'phone_str' => '',
                'count_notifications' => 0
            ];
        }
    }

    public static function user_get_or_create($phone) {
        // validate
        $user = User::user_info(['phone' => $phone]);
        $user_id = $user['id'];
        // create
        if (!$user_id) {
            DB::query("INSERT INTO users (status_access, phone, created) VALUES ('3', '".$phone."', '".Session::$ts."');") or die (DB::error());
            $user_id = DB::insert_id();
        }
        // output
        return $user_id;
    }

    // TEST

	//return info

    public static function owner_info() {
        if(!empty(Session::$user_id)){
			return self::user_info(['user_id' => Session::$user_id]);
		}else{
			return error_response(2001, 'Get owner user info: invalid owner user.');
		}
    }
	
	//update

    public static function owner_update($data = []) {
		//parse data
		$cnt_all = 0;
		$set = [];
		$ar_only_fields = array(
			'first_name' => array(
				'is_empty' => false,
			),
			'last_name' => array(
				'is_empty' => false,
			),
			'middle_name' => array(
				'is_empty' => true,
			),
			'email' => array(
				'is_empty' => true,
			),
			'phone' => array(
				'is_empty' => false,
			));
		foreach($ar_only_fields as $k=>$v){
			if(isset($data[$k])){
				$cnt_all++;
				if($k === 'email') $data[$k] = mb_strtolower($data[$k]); // && filter_var($data[$k], FILTER_VALIDATE_EMAIL)  --- if needed
				if($k === 'phone'){
					$data[$k] = preg_replace('/[^0-9]/', '', $data[$k]);
					if(!preg_match('/7\d{10}?/', $data[$k]))
						continue;
				}
				if(!$v['is_empty'] && (string)$data[$k] === ''){
					continue;
				}else{
					$set[] = '`'.$k.'`="'.$data[$k].'"';
				}
			}
		};
		//error if no fileds
		if($cnt_all === 0){
			return error_response(2002, 'Update owner user: no fields.');
		}
		//update if check
		if(!empty($user_id = Session::$user_id) && !empty($set)){
			$set = implode(',', $set);
			DB::query("UPDATE `users` SET ".$set." WHERE user_id='".$user_id."' LIMIT 1;") or die (DB::error());
			DB::query("INSERT INTO `user_notifications` (`user_id`, `title`, `viewed`, `created`) VALUES ('".$user_id."', 'update', 0, '".time()."');") or die (DB::error());
			return 'Success update user_id: '.$user_id;
		}else{
			return error_response(2001, 'Update owner user: invalid owner user.');
		}
    }

}
