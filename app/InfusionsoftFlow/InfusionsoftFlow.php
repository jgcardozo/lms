<?php

namespace app\InfusionsoftFlow;

use DB;
use Infusionsoft;
use Illuminate\Support\Facades\Mail;
use Mockery\CountValidator\Exception;

class InfusionsoftFlow
{
	protected static $cards = array(
		'visaelectron' => array(
			'type' => 'visaelectron',
			'pattern' => '/^4(026|17500|405|508|844|91[37])/',
			'length' => array(16),
			'cvcLength' => array(3),
			'luhn' => true,
		),
		'maestro' => array(
			'type' => 'maestro',
			'pattern' => '/^(5(018|0[23]|[68])|6(39|7))/',
			'length' => array(12, 13, 14, 15, 16, 17, 18, 19),
			'cvcLength' => array(3),
			'luhn' => true,
		),
		'forbrugsforeningen' => array(
			'type' => 'forbrugsforeningen',
			'pattern' => '/^600/',
			'length' => array(16),
			'cvcLength' => array(3),
			'luhn' => true,
		),
		'dankort' => array(
			'type' => 'dankort',
			'pattern' => '/^5019/',
			'length' => array(16),
			'cvcLength' => array(3),
			'luhn' => true,
		),
		// Credit cards
		'visa' => array(
			'type' => 'visa',
			'pattern' => '/^4/',
			'length' => array(13, 16),
			'cvcLength' => array(3),
			'luhn' => true,
		),
		'mastercard' => array(
			'type' => 'mastercard',
			'pattern' => '/^(5[0-5]|2[2-7])/',
			'length' => array(16),
			'cvcLength' => array(3),
			'luhn' => true,
		),
		'amex' => array(
			'type' => 'amex',
			'pattern' => '/^3[47]/',
			'format' => '/(\d{1,4})(\d{1,6})?(\d{1,5})?/',
			'length' => array(15),
			'cvcLength' => array(3, 4),
			'luhn' => true,
		),
		'dinersclub' => array(
			'type' => 'dinersclub',
			'pattern' => '/^3[0689]/',
			'length' => array(14),
			'cvcLength' => array(3),
			'luhn' => true,
		),
		'discover' => array(
			'type' => 'discover',
			'pattern' => '/^6([045]|22)/',
			'length' => array(16),
			'cvcLength' => array(3),
			'luhn' => true,
		),
		'unionpay' => array(
			'type' => 'unionpay',
			'pattern' => '/^(62|88)/',
			'length' => array(16, 17, 18, 19),
			'cvcLength' => array(3),
			'luhn' => false,
		),
		'jcb' => array(
			'type' => 'jcb',
			'pattern' => '/^35/',
			'length' => array(16),
			'cvcLength' => array(3),
			'luhn' => true,
		),
	);

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
				try {
					$this->is->setToken($token);
					$this->refreshToken();
				}catch (\Exception $e) {
					\Log::critical('Token error.');

					Mail::send([], [], function ($message) {
						$message->to('panovtomislav@hotmail.com')
							->subject('[ASK LMS] Token error')
							->setBody('Re-auth the infusionsoft token');
					});
				}
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
			return $this->is->data()->query('ContactGroupAssign', 1000, 0, ['ContactId' => $contactID], ['GroupId', 'ContactGroup'], '', false);
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

	public function validateCreditCard($contact_id, $creditCard)
	{
		$validateCreditCard = $this->is->invoices()->validateCreditCard($this->creditCardType($creditCard->cc_number), $contact_id, $creditCard->cc_number, $creditCard->cc_expiry_month, $creditCard->cc_expiry_year, $creditCard->cc_cvv);

		if($validateCreditCard['Valid'] == 'false')
		{
			return false;
		}

		return true;
	}

	public function createCreditCard($user, $creditCard)
	{
		if(!is_object($creditCard))
		{
			$creditCard = (object) $creditCard;
		}

		if(!$this->validateCreditCard($user->contact_id, $creditCard))
		{
			return (object) [
				'status' => false,
				'message' => 'This credit card can not be validated.'
			];
		}

		$creditCardValues = [
			'ContactId' => $user->contact_id,
			'CardNumber' => $creditCard->cc_number,
			'ExpirationMonth' => $creditCard->cc_expiry_month <= 9 ? '0' . $creditCard->cc_expiry_month : $creditCard->cc_expiry_month,
			'ExpirationYear' => $creditCard->cc_expiry_year,
			'CardType' => ucfirst($this->creditCardType($creditCard->cc_number)),
			'NameOnCard' => !empty($creditCard->cc_name) ? $creditCard->cc_name : $user->name,
			'BillAddress1' => !empty($creditCard->cc_address) ? $creditCard->cc_address : $user->profile->address,
			'BillCity' => $user->profile->city,
			'BillCountry' => $user->profile->country
		];

		try {
			$newCC = $this->is->data()->add('CreditCard', $creditCardValues);
		} catch (Exception $e) {
			return (object) [
				'status' => false,
				'message' => 'This credit card can not be created.'
			];
		}

		return (object) [
			'status' => true,
			'id' => $newCC
		];
	}

	protected function creditCardType($number)
	{
		foreach (self::$cards as $type => $card)
		{
			if(preg_match($card['pattern'], $number))
			{
				return $type;
			}
		}

		return false;
	}

	public function syncContactDetails($user)
	{
		$fields = [
			'FirstName' => $user->profile->first_name,
			'LastName' => $user->profile->last_name,
			'Email' => $user->email,
			'Phone1' => $user->profile->phone1,
			'Company' => $user->profile->company
		];

		$fields = array_filter($fields);

		$this->is->contacts()->update((int) $user->contact_id, $fields);
	}

	public function addTag($contactID = 0, $tags)
	{
		if(empty($contactID) || empty($tags))
		{
			return false;
		}

		if(!is_array($tags))
		{
			$tags = [(int) $tags];
		}

		foreach($tags as $tag)
		{
			$this->is->contacts()->addToGroup($contactID, (int) $tag);
		}

		return true;
	}
}