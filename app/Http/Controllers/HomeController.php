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
		die();
		// dump(InfusionsoftFlow::is()->data()->query('Affiliate', 10, 0, ['Id' => '%'], ['Id', 'AffCode', 'AffName', 'ContactId'], '', false));
		// dump(InfusionsoftFlow::is()->data()->query('Referral', 10, 0, ['Id' => '%'], ['Id', 'AffiliateId', 'ContactId', 'DateExpires', 'DateSet', 'IPAddress', 'Info', 'Source', 'Type'], '', false));

		$datetime = new \DateTime('now', new \DateTimeZone('America/New_York'));

		$args = [
			"Type" => 0,
			"AffiliateId" => 94,
			"ContactId" => 322372,
			"DateSet" => $datetime,
			"IPAddress" => "81.17.233.90"
		];

		InfusionsoftFlow::is()->data()->add('Referral', $args);

		dump(InfusionsoftFlow::is()->data()->query('Affiliate', 10, 0, ['Id' => 94], ['Id', 'AffCode', 'AffName', 'ContactId'], '', false));
		dump(InfusionsoftFlow::is()->data()->query('Referral', 10, 0, ['AffiliateId' => 94], ['Id', 'AffiliateId', 'ContactId', 'DateExpires', 'DateSet', 'IPAddress', 'Info', 'Source', 'Type'], 'DateSet', false));
		die();
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
