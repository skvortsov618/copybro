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
        //vars, validate
        $errors=[];
        !empty($data['user_id']) && is_numeric($data['user_id']) ? $user_id = $data['user_id'] : $errors[] = "UserID invalid";
        !empty($data['first_name']) ? $first_name = $data['first_name'] : $errors[] = "First name is not set";
        !empty($data['last_name']) ? $last_name = $data['last_name'] : $errors[] = "Last name is not set";
        $phone = isset($data['phone']) ? $data['phone'] : "";
        $phone = preg_replace("/\D/", "", $phone);
        if (!preg_match("/^7\d{10}$/", $phone)) $errors[] = "Phone is invalid";
        isset($data['middle_name']) ? $middle_name = $data['middle_name'] : null;
        isset($data['email']) ? $email = strtolower($data['email']) : null;

        if (count($errors)>0) die(implode(" | ", $errors));
        //set
        $set=[];
        if (isset($first_name)) $set[] = "first_name='".$first_name."'";
        if (isset($last_name))  $set[] = "last_name='".$last_name."'";
        if (isset($middle_name))  $set[] = "middle_name='".$middle_name."'";
        if (isset($phone))  $set[] = "phone='".$phone."'";
        if (isset($email))  $set[] = "email='".$email."'";

        if (count($set) == 0) die ("Set is empty");
        $set = implode(",", $set);
        //query
        DB::query("UPDATE users SET ".$set." WHERE user_id='".$user_id."'") or die (DB::error());
        DB::query("INSERT INTO user_notifications (user_id, created, description, title) VALUES ('".$user_id."', '".Session::$ts."', 'updated user info', 'userinfo update')") or die (DB::error());
        //info
        $user['user_id']=$user_id; $user['phone'] = $phone;
        return self::user_info($user);
    }

    public static function notifications_get($data) {
        //vars
        isset($data['user_id']) && is_numeric($data['user_id']) ? $user_id = $data['user_id'] : die("user_id invalid");
        $only_unviewed = isset($data["only_unviewed"]);
        //where
        $only_unviewed ? $where = "viewed='0' AND user_id='".$user_id."'" : $where = "user_id='".$user_id."'";
        //query
        $q = DB::query("SELECT title, description, created, viewed FROM user_notifications WHERE ".$where);
        $notifications = [];
        while ($n = DB::fetch_row($q)) {
            $time = date("Y-m-d\TH:i:s\Z", $n['created']);
            $notifications[] =  "TITLE: ".$n['title']." | DESCRIPTION: ".$n['description']." | DATE: ".$time." | VIEWED:".$n['viewed'];
        }
        //output
        return $notifications;
    }

    public static function notifications_read() {
        //vars
        $user_id = Session::$user_id;
        //query
        DB::query("UPDATE user_notifications SET viewed=1 WHERE user_id='".$user_id."' AND viewed='0'");
        //output
        return self::notifications_get(["user_id"=>$user_id]);
    }

    // TEST

    public static function owner_info() {
        return self::user_info(["user_id"=>Session::$user_id]);
    }

    public static function owner_update($data = []) {
        //vars
        $data['user_id'] = Session::$user_id;
        //query, output
        return self::user_update($data);
    }

}
