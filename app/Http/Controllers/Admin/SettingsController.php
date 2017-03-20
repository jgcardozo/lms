<?php

namespace App\Http\Controllers\Admin;

use DB;
use Session;
use Infusionsoft;
use InfusionsoftFlow;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

Class SettingsController extends BaseController
{
	public function index()
	{
		// $a = InfusionsoftFlow::is()->invoices()->locateExistingCard(294378, '0002');
		$a = InfusionsoftFlow::is()->data()->query('CreditCard', 1000, 0, ['ContactId' => 294378], ['Id', 'Last4', 'NameOnCard', 'CardType', 'FirstName', 'LastName', 'ExpirationMonth', 'ExpirationYear'], '', false);
		dd($a);

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
			$row = DB::table('settings')->where('key', 'auto_login_key')->first();
			if(empty($row))
			{
				DB::insert("insert into settings (`key`, `value`) values ('auto_login_key', ?)", [$auto_login_key]);
			}else{
				DB::update("update settings set `value` = ? where `key`='auto_login_key'", [$auto_login_key]);
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
					$tagTitle = $tag['GroupName'];

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
		if($request->has('code') and !InfusionsoftFlow::is()->getToken())
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