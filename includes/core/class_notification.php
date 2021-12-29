<?php

class Notification {

    // GENERAL

    public static function notifications_get($data) {
        // vars
        $notifications = [];
        $user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? $data['user_id'] : 0;
        $only_unviewed = $data['only_unviewed'] ?? false;
        // validation
        if (!$user_id) return 'UserID is invalid';
        //where
        $only_unviewed ? $where = "viewed='0' AND user_id='".$user_id."'" : $where = "user_id='".$user_id."'";
        // query
        $q = DB::query("SELECT title, description, created, viewed FROM user_notifications WHERE ".$where);
        while ($row = DB::fetch_row($q)) {
            $notifications[] = [
                'title' => $row['title'],
                'description' => $row['description'],
                'date' => date("Y-m-d\TH:i:s\Z", $row['created']),
                'viewed' => $row['viewed']
            ];
        }
        // output
        return $notifications;
    }

    public static function notification_create($data) {
        // vars
        $user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? $data['user_id'] : 0;
        $title = isset($data['title']) ? $data['title'] : '';
        $description = isset($data['description']) ? $data['description'] : '';
        // create
        if ($user_id && $title) {
            DB::query("INSERT INTO user_notifications (user_id, created, description, title) VALUES ('".$user_id."', '".Session::$ts."', '".$description."', '".$title."')") or die (DB::error());
            // output
            return DB::insert_id();
        } else return 'error';
    }

    public static function notifications_read() {
        DB::query("UPDATE user_notifications SET viewed='1' WHERE user_id='".Session::$user_id."' AND viewed='0'");
        return Notification::notifications_get(['user_id' => Session::$user_id]);
    }

}
