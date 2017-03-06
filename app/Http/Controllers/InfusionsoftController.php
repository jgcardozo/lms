<?php

namespace App\Http\Controllers;

use Session;
use Infusionsoft;
use App\Models\User;
use App\Models\ISTag;
use Illuminate\Support\Facades\Request;

class InfusionsoftController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Sync user stuff
     */
    public function sync()
    {
        Infusionsoft::setToken(unserialize(Session::get('token')));
        
        $userTags = Infusionsoft::data()->query('ContactGroupAssign', 1000, 0, ['ContactId' => $this->user->contact_id], ['GroupId', 'ContactGroup'], '', false);
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
        $this->user->is_tags()->sync($syncUserTags, false);
    }

    public function signin()
    {
        echo '<a href="' . Infusionsoft::getAuthorizationUrl() . '">Click here to connect to Infusionsoft</a>';
    }

    public function callback()
    {
        if (Session::has('token')) {
            Infusionsoft::setToken(unserialize(Session::get('token')));
        }

        if (Request::has('code') and !Infusionsoft::getToken()) {
            Infusionsoft::requestAccessToken(Request::get('code'));
        }

        if (Infusionsoft::getToken()) {
            Session::put('token', serialize(Infusionsoft::getToken()));
            return redirect()->to('/');
        }

        return redirect()->to('/');
    }
}
