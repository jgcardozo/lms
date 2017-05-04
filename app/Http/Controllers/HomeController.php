<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use DB;
use Auth;
use Carbon\Carbon;
// use Facebook\Facebook;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Session;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\CoachingCall;
use App\Models\User;

use App\Models\Gamification\Badge;
use App\Models\Gamification\Milestone;

use App\Streaks\Streak;
use App\Streaks\Types\LoginStreak;
use App\Notifications\UnlockedByTag;

use App\Events\WatchedSession;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Session as z;

use InfusionsoftFlow;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use App\Gamification\Contracts\Gamification;
use MongoDB\Driver\Exception\ExecutionTimeoutException;
// use Facebook\FacebookRequest;


class HomeController extends Controller
{
	public function __construct()
	{
		// $this->fb = new Facebook(Config::get('facebook'));
		// $this->helper = $this->fb->getRedirectLoginHelper();
	}

	public function index() {
		if(Auth::user())
		{
			// Courses data set in View composers [HeaderComposer.php]
			// $courses = Course::all();
			return view('lms.courses.list'); //->with(['courses' => $courses]);
		}

		return view('auth.login');
	}

	public function test()
	{
		// $s = Session::find(17);
		// dd($s->resources);
		die();
		// dd(Auth::user());
		// Mail::to(Auth::user())->send(new \App\Mail\UserRegistered('test-pass'));
		/*Mail::raw('Hello there', function($message) {
			$message->to('argirco.popov@gmail.com');
		});*/

		die();

		$creditCard = (object) [
			'cc_type' => '',
			'cc_number' => '4242424242424242',
			'cc_expiry_month' => '03',
			'cc_expiry_year' => '2019',
			'cc_cvv' => ''
		];

		// Add new credit card for this invoice Id
		$newCC = InfusionsoftFlow::createCreditCard(Auth::user(), $creditCard);
		dd($newCC);


		die();

		/*
		$contactId = 294378;
		$datetime = new \DateTime('now', new \DateTimeZone('America/New_York'));
		$product_id = 130;
		$amount = 25;
		$ccId = 28848;

		$invoice_id = InfusionsoftFlow::is()->invoices()->createBlankOrder($contactId, '', $datetime, 0, 0);
		InfusionsoftFlow::is()->invoices()->addOrderItem($invoice_id, $product_id, 4, (double)$amount, 1, '', '');
		$a = InfusionsoftFlow::is()->invoices()->addPaymentPlan($invoice_id, true, $ccId, 10, 1, 3, (double)0, $datetime, $datetime, 7, 1);
		$result = InfusionsoftFlow::is()->invoices()->chargeInvoice($invoice_id, 'asdasda', $ccId, 10, false);
		dd($result);
		*/

		$payplan = InfusionsoftFlow::is()->data()->query('PayPlan', 1000, 0, ['InvoiceId' => 58144], ['Id'], '', false);
		$payplan_items = InfusionsoftFlow::is()->data()->query('PayPlanItem', 1000, 0, ['PayPlanId' => $payplan[0]['Id']], ['AmtDue', 'AmtPaid', 'DateDue', 'Id', 'PayPlanId', 'Status'], '', false);
		dd($payplan_items);

		$payments = InfusionsoftFlow::is()->invoices()->getPayments(58148);
		var_dump(empty($payments));
		die();

		$courses = Course::get();
		$course_invoice = [];
		foreach($courses as $course)
		{
			$course_products = $course->is_course_products->pluck('product_id')->toArray();
			if(!$course_products)
				continue;

			// Get user invoices
			$invoices = InfusionsoftFlow::is()->data()->query('Invoice', 1000, 0, ['ContactId' => Auth::user()->contact_id], ['Id'], '', false);
			foreach($invoices as $invoice)
			{
				$invoiceItems = InfusionsoftFlow::is()->data()->query('OrderItem', 1000, 0, ['OrderId' => $invoice['Id']], ['ProductId'], '', false);
				$invoiceItems = array_pluck($invoiceItems, 'ProductId');
				if(!count(array_intersect($course_products, $invoiceItems)))
					continue;

				$payments = InfusionsoftFlow::is()->invoices()->getPayments($invoice['Id']);
				$charges = InfusionsoftFlow::is()->data()->query('CCharge', 1000, 0, ['Id' => $payments[0]['ChargeId']], ['CCId', 'PaymentId', 'Amt'], '', false);
				$course_invoice[$course->id] = [
					'invoice' => $invoice['Id'],
					'cc' => $charges[0]['CCId']
				];
				break;
			}
		}

		dd($course_invoice);

		die();

		$kur = InfusionsoftFlow::is()->data()->query('PayPlan', 1000, 0, ['InvoiceId' => 58112], ['Id'], '', false);
		$plans = InfusionsoftFlow::is()->data()->query('PayPlanItem', 1000, 0, ['PayPlanId' => $kur[0]['Id']], ['AmtDue', 'AmtPaid', 'DateDue', 'Id', 'PayPlanId', 'Status'], '', false);

		dump($invoiceItems);

		dump($kur);
		dump($plans);
		die();

		$contactId = 294378;
		$datetime = new \DateTime('now', new \DateTimeZone('America/New_York'));
		$product_id = 130;
		$amount = 25;
		$ccId = 28822;

		/*$invoice_id = InfusionsoftFlow::is()->invoices()->createBlankOrder($contactId, '', $datetime, 0, 0);
		InfusionsoftFlow::is()->invoices()->addOrderItem($invoice_id, $product_id, 4, (double)$amount, 1, '', '');
		$a = InfusionsoftFlow::is()->invoices()->addPaymentPlan($invoice_id, true, $ccId, 6, 1, 3, (double)0, $datetime, $datetime, 7, 1);
		dd($a);
		*/
		//$result = $this->infusionsoft->invoices()->chargeInvoice(57742, 'asdasda', 28610, 6, false);
		// var_dump($result);

// $payPlan = Infusionsoft_DataService::query(new Infusionsoft_PayPlan(), array('InvoiceId' => $invoiceId));
// $kur = $this->infusionsoft->data()->query('PayPlan', 1000, 0, ['InvoiceId' => 57734], ['Id'], '', false);
// $plans = $this->infusionsoft->data()->query('PayPlanItem', 1000, 0, ['PayPlanId' => $kur[0]['Id']], ['AmtDue', 'AmtPaid', 'DateDue', 'Id', 'PayPlanId', 'Status'], '', false);
		var_dump($a);
		dump($invoices);
		dump($invoiceItems);
		die();
		// Log::info('Test log | Data here');
		/*Mail::raw('Hello there', function($message) {
			$message->from('test@codeart.mk', 'Codeart');
			$message->to('argirco.popov@gmail.com');
		});
		*/
		/*$key = 'session_4_1';
		session([$key => 79]);
		session()->save();*/
		// session(['session_1' => '330']);
		// session()->save();
		$val = session()->all();
		dd($val);
		die();
		// $item = Course::find(1);
		// activity()->causedBy(Auth::user())->performedOn($item)->log('edited');
		// dd($item->getNextSession());
		$item = Resource::find(1);
		dd($item->file_size_mb);

		$user = User::find(1);
		dd($user->fb_posted);

		// dump($a);
		// $a = InfusionsoftFlow;
		// var_dump($a);
		// die();

		// $userTags = CA_Infusionsoft::data()->query('ContactGroupAssign', 1000, 0, ['ContactId' => $this->user->contact_id], ['GroupId', 'ContactGroup'], '', false);
		// $a = CA_Infusionsoft::get()::data()->query('ContactGroupAssign', 1000, 0, ['ContactId' => 294378], ['GroupId', 'ContactGroup'], '', false);
		// dd($a);

		die();
		// Streak::log(new LoginStreak());
		// $a = new LoginStreak();
		// dd($a->log());
		// dump($a->started());
		// dd($a->last_date());

		//dd(Gamification::getScore());
		// event(new \App\Events\WatchedSession($session));
		return;

		/***********************/

		var_dump(session_status()); // session_start() in bootstrap/app.php
		var_dump(PHP_SESSION_NONE);
		exit();
		$permissions = ['email', 'publish_actions', 'user_managed_groups', 'public_profile', 'user_friends'];
		$loginUrl = $this->helper->getLoginUrl(url('test/fb/callback'), $permissions);

		return redirect($loginUrl);
	}

	public function callback() {


		try {
			$accessToken = $this->helper->getAccessToken();
		}catch(Exception $e){
			var_dump($e);
		}

		echo '<h3>Access Token</h3>';
		var_dump($accessToken->getValue());

		$oAuth2Client = $this->fb->getOAuth2Client();

		// Get the access token metadata from /debug_token
		$tokenMetadata = $oAuth2Client->debugToken($accessToken);
		echo '<h3>Metadata</h3>';
		var_dump($tokenMetadata);

		$tokenMetadata->validateAppId(Config::get('facebook.app_id'));
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
		$tokenMetadata->validateExpiration();

		if (! $accessToken->isLongLived()) {
			// Exchanges a short-lived access token for a long-lived one
			try {
				$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
			} catch (Exception $e) {
				echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
				exit;
			}

			echo '<h3>Long-lived</h3>';
			var_dump($accessToken->getValue());
		}

		$_SESSION['fb_access_token'] = (string) $accessToken;

		return redirect(url('/test/fb/post'));
	}

	function posttofb() {

		$at = $_SESSION['fb_access_token'];
		dump($at);
		// $this->fb->setDefaultAccessToken($at);
		// $requestUserName = $this->fb->request('GET', '/me');
		// dump($requestUserName);

		$message = 'Hello there,' . "\n\n";
		$message .= 'This is a test message via asklms.dev';
		$statusUpdate = ['message' => $message];

		// $request = new FacebookRequest($this->fb, $at, 'POST', '/301899363546150/feed', $statusUpdate);

		$requestPostToFeed = $this->fb->request('POST', '/301899363546150/feed', $statusUpdate, $at);
		dump($requestPostToFeed);
	}
}
