<?php

namespace App\Http\Controllers;

use App\Models\ISTag;
use App\Models\LessonQuestion;
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
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

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
        die('Schhhhh....');
    }
}
