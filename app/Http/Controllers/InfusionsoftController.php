<?php

namespace App\Http\Controllers;

use Session;
use Infusionsoft;
use App\Models\User;
use InfusionsoftFlow;
use App\Models\ISTag;
use App\Models\Course;
use Illuminate\Support\Facades\Request;

class InfusionsoftController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
		$this->user = $user;
    }

	/**
	 * Sync user tags from Infusionsoft
	 * 
	 * @return array New IDs attached
	 */
    public function sync()
    {
		$userTags = InfusionsoftFlow::getUserTags($this->user->contact_id);
		if($userTags === false)
		{
			return [];
		}

        $userTags = array_map(function($tag) {
            return array(
                'title' => $tag['ContactGroup'],
                'id' => $tag['GroupId']
            );
        }, $userTags);

        foreach($userTags as $tag)
        {
            if(ISTag::find($tag['id']))
            {
                continue;
            }

            $newTag = new ISTag();
            $newTag->title = $tag['title'];
            $newTag->id = $tag['id'];
            $newTag->save();
        }

        $syncUserTags = array_column($userTags, 'id');
        $result = $this->user->is_tags()->sync($syncUserTags, true);

		return $result['attached'];
    }

	/**
	 * Send notification to the user for unlocked
	 * courses by tag
	 *
	 * @param $tags
	 */
	public function checkUnlockedCourses($tags)
	{
		$tags = ISTag::query()->whereIn('id', $tags)->get();

		$a = Course::whereHas('lock_tags', function($query) use ($tags) {
			return $query->whereIn('tag_id', $tags->pluck('id'));
		})->get();

		return $a;
	}
}
