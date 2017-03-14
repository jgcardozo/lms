<?php

namespace app\InfusionsoftFlow;

use DB;
use Infusionsoft;

class InfusionsoftFlow
{
	public function __construct($is)
	{
		$this->is = $is;
		$this->setToken();
	}

	public function is()
	{
		return $this->is;
	}

	public function requestUrl()
	{
		return $this->is->getAuthorizationUrl();
	}

	public function setToken()
	{
		$row = DB::table('settings')->where('key', 'is_token')->first();

		$token = unserialize($row->value);
		$this->is->setToken($token);
	}

	public function saveTokenToDB()
	{
		if($this->is->getToken())
		{
			$token = serialize($this->is->getToken());
			$row = DB::select("select * from settings where `key`='is_token'");
			if(empty($row))
			{
				DB::insert("insert into settings (`key`, `value`) values ('is_token', ?)", [$token]);
			}else{
				DB::update("update settings set `value` = ? where `key`='is_token'", [$token]);
			}
		}
	}

	public function refreshToken()
	{
		$this->is->refreshAccessToken();
		$this->saveTokenToDB();
	}

	public  function getUserTags($contactID = 0)
	{
		try {
			$userTags = $this->is()->data()->query('ContactGroupAssign', 1000, 0, ['ContactId' => $contactID], ['GroupId', 'ContactGroup'], '', false);
		} catch (\Exception $e) {
			$userTags = [];
		}

		return $userTags;
	}
}