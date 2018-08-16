<?php

namespace App\Http\Controllers\Admin;

use DB;
use Session;
use Autologin;
use Infusionsoft;
use InfusionsoftFlow;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

Class SettingsController extends BaseController
{
	public function __construct()
	{
		$this->middleware('role:Administrator');
	}

	public function index()
	{
		$isTagCategories = InfusionsoftFlow::getTagCategories();

		$settingsDB = DB::table('settings')->get()->toArray();

		$settings_data = [
			'is_tag_categories' => $isTagCategories !== false ? $isTagCategories : []
		];

		foreach($settingsDB as $key => $value)
		{
			$settings_data[$value->key] = $value->value;
		}

		return view('lms.admin.settings', $settings_data);
	}

	public function save(Request $request)
	{
		$is_tag_cats = $request->get('is_tag_categories');
		if(!empty($is_tag_cats))
		{
			$this->syncTags($is_tag_cats);
		}
		
		$auto_login_key = $request->get('auto_login_key');
		if(!empty($auto_login_key))
		{
			Autologin::refreshKey($auto_login_key);
		}

		$max_ip_logins = $request->get('max_ip_logins');
		if(!empty($max_ip_logins))
		{
			$row = DB::table('settings')->where('key', 'max_ip_logins')->first();
			if(empty($row))
			{
				DB::insert("insert into settings (`key`, `value`) values ('max_ip_logins', ?)", [$max_ip_logins]);
			}else{
				DB::update("update settings set `value` = ? where `key`='max_ip_logins'", [$max_ip_logins]);
			}
		}

		return redirect()->back();
	}

	public function syncTags($categories)
	{
		$sql = "INSERT INTO is_tags (id, title) VALUES ";
		$sqlVal = "";
		foreach($categories as $category)
		{
			$catTags = InfusionsoftFlow::getCategoryTags($category);
			if(!empty($catTags))
			{
				foreach($catTags as $tag)
				{
					$tagId = $tag['Id'];
					$tagTitle = addslashes($tag['GroupName']);

					$sqlVal .= !empty($sqlVal) ? ',' : '';
					$sqlVal .= "($tagId,'$tagTitle')";
				}
			}
		}

		$sql = $sql . $sqlVal . " ON DUPLICATE KEY UPDATE title=VALUES(title)";

		DB::insert($sql);
		\Alert::success('Tags were successfully synchronized.')->flash();
	}

	public function InfusionsoftCallback(Request $request)
	{
		if($request->has('code'))
		{
			InfusionsoftFlow::is()->requestAccessToken($request->get('code'));
		}

		if(InfusionsoftFlow::is()->getToken())
		{
			InfusionsoftFlow::saveTokenToDB();
		}

		return redirect()->back();
	}
}