<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationLogController extends Controller
{
    public function delete($id)
    {
        $notificationLog = NotificationLog::find($id);
        $uuid = $notificationLog->uuid;
        $notifications = DB::table('notifications')
            ->where('data->uuid',$uuid)
            ->delete();

        $notificationLog->delete();

        return redirect()->to(url()->previous()."#logTable");
    }
}
