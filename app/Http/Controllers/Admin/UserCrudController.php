<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Models\Module;
use App\Models\User;
use App\Models\Profile;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Models\Progress;
use App\Http\Requests\Admin\UserStoreCrudRequest as StoreRequest;
// VALIDATION
use App\Http\Requests\Admin\UserUpdateCrudRequest as UpdateRequest;
use Illuminate\Http\Request;

class UserCrudController extends CrudController
{
    public function __construct()
    {
		$this->middleware('role:Administrator');
        parent::__construct();
    }

    public function setup()
    {
        $this->crud->setModel('App\Models\User');
        $this->crud->setRoute('admin/user');
        $this->crud->setEntityNameStrings('user', 'users');
        $this->crud->enableAjaxTable();

        $this->crud->setColumns([
            [
                'name' => 'id',
                'type' => 'text',
                'label' => 'ID'
            ],
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'label' => 'Auto-login url (click to select the url)',
                'type' => 'model_function',
                'function_name' => 'admin_user_login_url',
				'name' => 'id'
            ]
        ]);

        $this->crud->addColumn([ // n-n relationship (with pivot table)
           'label'     => trans('backpack::permissionmanager.roles'), // Table column heading
           'type'      => 'select_multiple',
           'name'      => 'roles', // the method that defines the relationship in your Model
           'entity'    => 'roles', // the method that defines the relationship in your Model
           'attribute' => 'name', // foreign key attribute that is shown to user
           'model'     => "Backpack\PermissionManager\app\Models\Roles", // foreign key model
        ]);

        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'contact_id',
                'label' => 'Infusionsoft ContactID',
                'type'  => 'text'
            ],
            [
                'name' => 'first_name', // the db column for the foreign key
                'label' => 'First name',
                'type' => 'related_text',
                'entity' => 'profile', // the method that defines the relationship in your Model
                'attribute' => 'first_name', // foreign key attribute that is shown to user
                'model' => 'App\Models\Profile' // foreign key model
            ],
            [
                'name' => 'last_name', // the db column for the foreign key
                'label' => 'Last name',
                'type' => 'related_text',
                'entity' => 'profile', // the method that defines the relationship in your Model
                'attribute' => 'last_name', // foreign key attribute that is shown to user
                'model' => 'App\Models\Profile' // foreign key model
            ],
			[
				'name' => 'phone1', // the db column for the foreign key
				'label' => 'Phone',
				'type' => 'related_text',
				'entity' => 'profile', // the method that defines the relationship in your Model
				'attribute' => 'phone1', // foreign key attribute that is shown to user
				'model' => 'App\Models\Profile' // foreign key model
			],
			[
				'name' => 'company', // the db column for the foreign key
				'label' => 'Company',
				'type' => 'related_text',
				'entity' => 'profile', // the method that defines the relationship in your Model
				'attribute' => 'company', // foreign key attribute that is shown to user
				'model' => 'App\Models\Profile' // foreign key model
			],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
				'attributes' => [
					'autocomplete' => 'off'
				]
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
				'attributes' => [
					'autocomplete' => 'off'
				]
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
				'attributes' => [
					'autocomplete' => 'off'
				]
            ],
			[
				'label' => 'Sessions watched:',
				'type' => 'select2_multipleSessionsWatched',
				'name' => 'sessionsWatched',
				'entity' => 'sessions',
				'attribute' => 'title',
				'model' => 'App\Models\Session',
				'pivot' => true
            ],
            [
                'label'     => 'Cohorts',
                'type'      => 'checklist',
                'name'      => 'cohorts',
                'entity'    => 'cohorts',
                'attribute' => 'name',
                'model'     => "App\Models\Cohort",
                'pivot'     => true,
            ],
            /*[
                 'label' => 'Cohort:',
                 'type' => 'select2',
                 'name' => 'cohort_id',
                 'attribute' => 'name',
                 'model' => 'App\Models\Cohort'
            ],*/
            [
               'label' => 'Sessions watched:',
               'type' => 'select2_multipleSessionsWatched',
               'name' => 'sessionsWatched',
               'entity' => 'sessions',
               'attribute' => 'title',
               'model' => 'App\Models\Session',
               'pivot' => true
            ],
            [
                // two interconnected entities
                'label'             => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type'              => 'checklist_dependency',
                'name'              => 'roles_and_permissions', // the methods that defines the relationship in your Model
                'subfields'         => [
                        'primary' => [
                            'label'            => trans('backpack::permissionmanager.roles'),
                            'name'             => 'roles', // the method that defines the relationship in your Model
                            'entity'           => 'roles', // the method that defines the relationship in your Model
                            'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                            'attribute'        => 'name', // foreign key attribute that is shown to user
                            'model'            => "Backpack\PermissionManager\app\Models\Role", // foreign key model
                            'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                            'number_columns'   => 3, //can be 1,2,3,4,6
                        ],
                        'secondary' => [
                            'label'          => ucfirst(trans('backpack::permissionmanager.permission_singular')),
                            'name'           => 'permissions', // the method that defines the relationship in your Model
                            'entity'         => 'permissions', // the method that defines the relationship in your Model
                            'entity_primary' => 'roles', // the method that defines the relationship in your Model
                            'attribute'      => 'name', // foreign key attribute that is shown to user
                            'model'          => "Backpack\PermissionManager\app\Models\Permission", // foreign key model
                            'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                            'number_columns' => 3, //can be 1,2,3,4,6
                        ],
                    ],
            ],
        ]);

        $this->crud->addButton('line', 'view_activity', 'model_function', 'view_user_activity', 'end');
    }

    /**
     * Store a newly created resource in the database.
     *
     * @param StoreRequest $request - type injection used for validation using Requests
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreRequest $request)
    {
        $this->crud->hasAccessOrFail('create');

        // insert item in the db
        if ($request->input('password')) {
            $item = $this->crud->create(\Request::except(['redirect_after_save']));

            // now bcrypt the password
            $item->password = bcrypt($request->input('password'));
            $item->save();
        } else {
            $item = $this->crud->create(\Request::except(['redirect_after_save', 'password']));
        }

		$this->updateProfile($item->id);
        $this->updateProgressSessions($request->input('sessionsWatched'),$item->id);

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->setSaveAction();

        return $this->performSaveAction($item->getKey());
    }

    public function update(UpdateRequest $request)
    {
        //encrypt password and set it to request
        $this->crud->hasAccessOrFail('update');

        $dataToUpdate = \Request::except(['redirect_after_save', 'password']);

        //encrypt password
        if ($request->input('password')) {
            $dataToUpdate['password'] = bcrypt($request->input('password'));
        }

        $this->updateProgressSessions($dataToUpdate['sessionsWatched'],$dataToUpdate['id']);

		$this->updateProfile($dataToUpdate['id']);

        if(count(User::where('email',$request->input('email'))->where('id','!=',$dataToUpdate['id'])->get()))
        {
            \Alert::error('A user with that email already exists!')->flash();
            return redirect()->back();
        }
        // update the row in the db
        $this->crud->update(\Request::get($this->crud->model->getKeyName()), $dataToUpdate);

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->setSaveAction();

        return $this->performSaveAction();
    }

    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->cohorts()->detach();

        return parent::destroy($id); // TODO: Change the autogenerated stub
    }

    private function updateProfile($user_id)
	{
		if(empty($user_id))
		{
			return;
		}

		if($user = User::find($user_id))
		{
			$profile = $user->profile ?: new Profile();
			$profile->first_name = request()->has('first_name') ? request()->get('first_name') : '';
			$profile->last_name = request()->has('last_name') ? request()->get('last_name') : '';
			$profile->phone1 = request()->has('phone1') ? request()->get('phone1') : '';
			$profile->company = request()->has('company') ? request()->get('company') : '';
			$user->profile()->save($profile);
		}
	}

    private function updateProgressSessions($sessionsWatched,$id) {
        $existing = Progress::where('user_id',$id)->pluck('id')->toArray();
        $created = Progress::where('user_id',$id)->where('progress_type','LIKE','%Session')->whereRaw("progress_id IN (".implode(', ',$sessionsWatched).")")->pluck('created_at','progress_id')->toArray();
        if(count($sessionsWatched) > 0) {
            $data = [];

            foreach ( $sessionsWatched as $session) {
                if(array_key_exists($session,$created)){
                    $created_at = $created[$session];

                } else {
                    $created_at = now();
                }
                $data[] = [
                    'user_id' => $id,
                    'progress_type' => 'App\Models\Session',
                    'progress_id' => $session,
                    'created_at' => $created_at,
                    'updated_at' => now()
                ];
            }
        }

        $progressData = $this->updateProgressModuleAndLesson($sessionsWatched,$id);

        Progress::destroy($existing);
        Progress::insert($data);
        Progress::insert($progressData['lessonData']);
        Progress::insert($progressData['moduleData']);
    }

    private function updateProgressModuleAndLesson ($sessionsWatched,$id)
    {
        $moduleData = [];
        $lessonData = [];
        $modulesComplete = [];
        foreach (Module::with('lmsLessons.sessions')->get() as $module) {
            $lessonsComplete = [];

            if($module->lmsLessons->count() == 0) {
                continue;
            }

            foreach ($module->lmsLessons as $lesson) {
                if($lesson->sessions->count() == 0) {
                    continue;
                }

                if(count(array_intersect($lesson->sessions->pluck('id')->toArray(),$sessionsWatched)) == $lesson->sessions->count()) {
                    $lessonsComplete[] = $lesson->id;
                    if(Progress::where('user_id',$id)->where('progress_type','LIKE','%Session')->where('progress_id',$lesson->sessions->last()->id)->exists()) {
                        $created_at = Progress::where('user_id',$id)->where('progress_type','LIKE','%Session')->where('progress_id',$lesson->sessions->last()->id)->first()->created_at;
                    } else {
                        $created_at = now();
                    }

                    $lessonData[] = [
                        'user_id' => $id,
                        'progress_type' => 'App\Models\Lesson',
                        'progress_id' => $lesson->id,
                        'updated_at' => now(),
                        'created_at' => $created_at
                    ];
                }
            }

            if(count(array_intersect($module->lmsLessons->pluck('id')->toArray(),$lessonsComplete)) == $module->lmsLessons->count()) {
                $modulesComplete[] = $module->id;
                if(Progress::where('user_id',$id)->where('progress_type','LIKE','%Session')->where('progress_id',$module->lmsLessons->last()->sessions->last()->id)->exists()) {
                    $created_at = Progress::where('user_id',$id)->where('progress_type','LIKE','%Session')->where('progress_id',$module->lmsLessons->last()->sessions->last()->id)->first()->created_at;
                } else {
                    $created_at = now();
                }
                $moduleData[] = [
                    'user_id' => $id,
                    'progress_type' => 'App\Models\Module',
                    'progress_id' => $module->id,
                    'updated_at' => now(),
                    'created_at' => $created_at
                ];
            }

        }


        return [
            'moduleData' => $moduleData,
            'lessonData' => $lessonData
        ];
    }
}
