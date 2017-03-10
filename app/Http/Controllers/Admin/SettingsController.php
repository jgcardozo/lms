<?php

namespace App\Http\Controllers\Admin;

use Session;
use Infusionsoft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

Class SettingsController extends BaseController
{
	protected $infusionsoft;

	public function __construct(Infusionsoft $infusionsoft)
	{
		$this->infusionsoft = $infusionsoft;
	}

	public function index()
	{
		Infusionsoft::setToken(unserialize(Session::get('token')));

		// 68
		/*$t = Infusionsoft::data()->query('ContactGroup', 1000, 0, ['GroupCategoryId' => '68'], ['Id', 'GroupName'], '', false);
		dump($t);
		die();*/

		$isTagCategories = Infusionsoft::data()->query('ContactGroupCategory', 1000, 0, ['Id' => '%'], ['Id', 'CategoryName'], '', false);

		$settings_data = [
			'is_tag_categories' => $isTagCategories
		];

		return view('lms.admin.settings', $settings_data);
	}

	public function save(Request $request)
	{
		$is_tag_cats = $request->get('is_tag_categories');
		if(!empty($is_tag_cats))
		{
			$this->syncTags($is_tag_cats);
		}

		return redirect()->back();
	}

	public function syncTags($categories)
	{
		Infusionsoft::setToken(unserialize(Session::get('token')));

		$sql = "INSERT INTO is_tags (id, title) VALUES ";
		$sqlVal = "";
		foreach($categories as $category)
		{
			$catTags = Infusionsoft::data()->query('ContactGroup', 1000, 0, ['GroupCategoryId' => $category], ['Id', 'GroupName'], '', false);
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
}