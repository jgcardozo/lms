<?php

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserLoginsController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:Administrator');
    }

    public function index()
    {
        $num = lms_get_setting('max_ip_logins', 10);
        $userLogins = DB::table('user_logins')
                        ->select(
                            [
                                'user_logins.user_id',
                                DB::raw('COUNT(user_logins.ip) AS count')
                            ])
                        ->groupBy('user_logins.user_id')
                        ->orderBy('count', 'desc')
                        ->get();

        $userLogins = $userLogins->where('count', '>=', (int)$num);

        return view('lms.admin.userlogins')->with('logins', $userLogins);
    }
}