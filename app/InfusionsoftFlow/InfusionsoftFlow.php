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

		if(!empty($row))
		{
			$token = unserialize($row->value);

			if($token->endOfLife > time())
			{
				$this->is->setToken($token);
				$this->refreshToken();
			}
		}
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
		if(!$this->is->getToken())
		{
			return false;
		}

		try {
			return $this->is()->data()->query('ContactGroupAssign', 1000, 0, ['ContactId' => $contactID], ['GroupId', 'ContactGroup'], '', false);
		} catch (\Exception $e) {
			return false;
		}
	}

	public function getTagCategories()
	{
		if(!$this->is->getToken())
		{
			return false;
		}

		try {
			return $this->is->data()->query('ContactGroupCategory', 1000, 0, ['Id' => '%'], ['Id', 'CategoryName'], '', false);
		} catch (\Exception $e) {
			return false;
		}
	}

	public function getCategoryTags($category)
	{
		if(!$this->is->getToken())
		{
			return false;
		}

		try {
			return $tags = $this->is->data()->query('ContactGroup', 1000, 0, ['GroupCategoryId' => $category], ['Id', 'GroupName'], '', false);
		} catch (\Exception $e) {
			return false;
		}
	}

	public function getCreditCards($contactID = 0, $fields = [])
	{
		if(!$this->is->getToken())
		{
			return false;
		}

		if(!$fields)
			$fields = ['Id', 'CardType', 'Last4', 'ExpirationMonth', 'ExpirationYear', 'BillAddress1', 'BillName'];

		try {
			return $this->is->data()->query('CreditCard', 1000, 0, ['ContactId' => $contactID, 'Status' => 3], $fields, '', false);
		} catch (\Exception $e) {
			return false;
		}
	}
}