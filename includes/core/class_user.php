<?php

class User
{

    // GENERAL

    public static function user_info($data)
    {
        // vars
        $user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? $data['user_id'] : 0;
        $phone = isset($data['phone']) ? preg_replace('~[^\d]+~', '', $data['phone']) : 0;
        // where
        if ($user_id) $where = "user_id='" . $user_id . "'";
        else if ($phone) $where = "phone='" . $phone . "'";
        else return [];
        // info
        $q = DB::query("SELECT phone, user_id, first_name, last_name, middle_name, email, gender_id, count_notifications FROM users WHERE " . $where . " LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => (int)$row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'middle_name' => $row['middle_name'],
                'gender_id' => (int)$row['gender_id'],
                'email' => $row['email'],
                'phone' => (int)$row['phone'],
                'phone_str' => phone_formatting($row['phone']),
                'count_notifications' => (int)$row['count_notifications']
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

    public static function user_get_or_create($phone)
    {
        // validate
        $user = User::user_info(['phone' => $phone]);
        $user_id = $user['id'];
        // create
        if (!$user_id) {
            DB::query("INSERT INTO users (status_access, phone, created) VALUES ('3', '" . $phone . "', '" . Session::$ts . "');") or die (DB::error());
            $user_id = DB::insert_id();
        }
        // output
        return $user_id;
    }

    public static function user_update($data)
    {
        // vars
        $user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? $data['user_id'] : 0;
        $first_name = isset($data['first_name']) ? $data['first_name'] : 0;
        $middle_name = isset($data['middle_name']) ? $data['middle_name'] : 0;
        $last_name = isset($data['last_name']) ? $data['last_name'] : 0;
        $phone = isset($data['phone']) ? $data['phone'] : 0;
        $email = isset($data['email']) ? strtolower($data['email']) : 0;
        // validate
        $errors = [];
        if (!$user_id) $errors[] = 'UserID invalid';
        if (!$first_name) $errors[] = 'First name is not valid';
        if (!$last_name) $errors[] = 'Last name is not set';
        $phone = preg_replace('/\D/', '', $phone);
        if (!preg_match('/^7\d{10}$/', $phone)) $errors[] = 'Phone is invalid';
        if (count($errors) > 0) return implode(' | ', $errors);
        // set
        $set = [];
        if ($first_name) $set[] = "first_name='" . $first_name . "'";
        if ($last_name) $set[] = "last_name='" . $last_name . "'";
        if ($middle_name) $set[] = "middle_name='" . $middle_name . "'";
        if ($phone) $set[] = "phone='" . $phone . "'";
        if ($email) $set[] = "email='" . $email . "'";
        if (count($set) == 0) return 'Set is empty';
        $set = implode(",", $set);
        // query
        DB::query("UPDATE users SET " . $set . " WHERE user_id='" . $user_id . "'") or die (DB::error());
        Notification::notification_create(['user_id' => $user_id, 'title' => 'User data updated', 'description' => 'User data updated']);
        //info
        return self::user_info(['user_id' => $user_id]);
    }

    // TEST

    public static function owner_info()
    {
        return self::user_info(["user_id" => Session::$user_id]);
    }

    public static function owner_update($data = [])
    {
        // vars
        $data['user_id'] = Session::$user_id;
        // query, output
        return self::user_update($data);
    }
}
