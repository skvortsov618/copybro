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
        $q = DB::query("SELECT phone, user_id, first_name, last_name, middle_name, email, gender_id, count_notifications FROM users WHERE ".$where." LIMIT 1;") or die (DB::error());
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

    public static function user_update($data) {
        $columns=[]; $errors=[];
        isset($data["first_name"]) ? $columns[] = "first_name=:first_name" : $errors[] = "First name is not set";
        isset($data["last_name"]) ? $columns[] = 'last_name=:last_name' : $errors[] = "Last name is not set";
        isset($data["phone"]) ? $columns[] = 'phone=:phone' : $errors[] = "Phone is not set";
        $data["phone"] = preg_replace("/\D/", "", $data["phone"]);
        if ($data["phone"][0] != "7" || !preg_match("/^7\d{10}$/",$data["phone"])) $errors[] = "Phone format is incorrect";
        isset($data["middle_name"]) ? $columns[] = 'middle_name=:middle_name' : null;
        isset($data["email"]) ? $columns[] = 'email=:email' : null;
        if (count($columns) == 0 || count($errors)>0) return "ERROR";
        $columns = implode(",", $columns);
        $qdata = [
            "first_name"=>$data["first_name"],
            "middle_name"=>$data["middle_name"],
            "last_name"=>$data["last_name"],
            "phone"=>$data["phone"],
            "email"=>$data["email"],
//            "user_id"=>$data["user_id"]
            "user_id"=>2
        ];
        DB::update("UPDATE users SET ".$columns." WHERE user_id=:user_id",$qdata) or die (DB::error());
        DB::query("INSERT INTO user_notifications (user_id, created, description, title) VALUES (".$data["user_id"].", ".time().", 'updated user info', 'userinfo update')") or die (DB::error());
        return "Update successful";
    }

    public static function notifications_get($data) {
        $where="";
        if (isset($data["only_unviewed"])) $where="viewed=0 AND ";
        $where .= "user_id=".$data["user_id"];
        $q = DB::query("SELECT title, description, created, viewed FROM user_notifications WHERE ".$where);
        $notifications = DB::fetch_all($q);
        return $notifications;
    }

    public static function notifications_read() {
        DB::query("UPDATE user_notifications SET viewed=1 WHERE user_id=".Session::$user_id." AND viewed=0");
        return "all read";
    }

    // TEST

    public static function owner_info() {
        // your code here ...
    }

    public static function owner_update($data = []) {
        // your code here ...
    }

}
