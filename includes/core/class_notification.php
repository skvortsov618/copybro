<?php

class Notification
{

    // GENERAL

    public static function notifications_get($data)
    {
        // vars
        isset($data['user_id']) && is_numeric($data['user_id']) ? $user_id = $data['user_id'] : die("user_id invalid");
        $only_unviewed = isset($data["only_unviewed"]);
        //where
        $only_unviewed ? $where = "viewed='0' AND user_id='" . $user_id . "'" : $where = "user_id='" . $user_id . "'";
        // query
        $q = DB::query("SELECT title, description, created, viewed FROM user_notifications WHERE " . $where);
        $notifications = [];
        while ($row = DB::fetch_row($q)) {
            $time = date("Y-m-d\TH:i:s\Z", $row['created']);
            $notifications[] = ['title' => $row['title'], 'description' => $row['description'], 'date' => $time, 'viewed' => $row['viewed']];
        }
        // output
        return $notifications;
    }

    public static function notification_create($data)
    {
        // vars
        $user_id = isset($data['user_id']) && is_numeric($data['user_id']) ? $data['user_id'] : 0;
        $title = isset($data['title']) ? $data['title'] : 0;
        $description = isset($data['description']) ? $data['description'] : "";
        // create
        if ($user_id && $title) {
            DB::query("INSERT INTO user_notifications (user_id, created, description, title) VALUES ('" . $user_id . "', '" . Session::$ts . "', '" . $description . "', '" . $title . "')") or die (DB::error());
            $notification_id = DB::insert_id();
            // output
            return $notification_id;
        } else return 'error';
    }

    public static function notifications_read()
    {
        // query
        DB::query("UPDATE user_notifications SET viewed=1 WHERE user_id='" . Session::$user_id . "' AND viewed='0'");
        // output
        return self::notifications_get(["user_id" => Session::$user_id]);
    }

}
