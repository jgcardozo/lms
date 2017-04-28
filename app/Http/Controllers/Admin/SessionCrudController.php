<?php

namespace App\Http\Controllers\Admin;

use App\Models\Session;
use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\SessionCrudRequest as StoreRequest;
use App\Http\Requests\Admin\SessionCrudRequest as UpdateRequest;

class SessionCrudController extends CrudController
{
	use BackpackCrudTrait;

	public function setup() {
		$this->crud->setModel('App\Models\Session');
		$this->crud->setRoute("admin/session");
		$this->crud->setEntityNameStrings('session', 'sessions');


		/**
		 * Define CRUD list columns
		 */
		$this->crud->setColumns([
			[
				'name' => 'id',
				'label' => 'ID'
			],
			[
				'label' => 'Title',
				'type' => 'model_function',
				'function_name' => 'admin_editable_title'
			],
			[
				'name' => 'description',
				'label' => 'Description'
			],
			[
				'name' => 'video_url',
				'label' => 'Wistia Video ID'
			],
			[
				'name' => 'video_duration',
				'label' => 'Video duration'
			],
			[
				'name' => 'lock_date',
				'label' => 'Lock date'
			],
			[
				'label' => 'Lesson',
				'type' => 'model_function',
				'function_name' => 'admin_lesson_link'
			],
			[
				'label' => 'Intro video for course',
				'type' => 'model_function',
				'function_name' => 'admin_course_link'
			]
		]);


		/**
		 * Add CRUD fields
		 */
		$this->crud->addField([
			'name' => 'title',
			'label' => 'Title'
		]);

		$this->crud->addField([
			'name' => 'slug',
			'label' => 'Slug'
		]);

		$this->crud->addField([
			'name' => 'description',
			'label' => 'Description',
			'type' => 'wysiwyg'
		]);

		$this->crud->addField([
			'name' => 'video_url',
			'label' => 'Wistia Video ID',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'name' => 'video_duration',
			'label' => 'Video duration (in minutes)',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'label' => 'Session featured image',
			'name' => 'featured_image',
			'type' => 'upload',
			'upload' => true,
			'disk' => 's3'
		]);

		$this->crud->addField([
			'label' => 'Assign resource(s) to this session',
			'type' => 'select2_multiple',
			'name' => 'resources',
			'entity' => 'resources',
			'attribute' => 'title',
			'model' => 'App\Models\Resource',
			'pivot' => true,
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'name' => 'bucket_url',
			'label' => 'Bucket URL',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'name' => 'learn_more',
			'label' => 'Learn More',
			'type' => 'wysiwyg'
		]);

		$this->crud->addField([
			'label' => 'Assign this session to lesson:',
			'type' => 'select',
			'name' => 'lesson_id',
			'attribute' => 'backpack_crud_title',
			'model' => 'App\Models\Lesson'
		]);

		$this->crud->addField([
			'label' => 'Assign this session to course as intro video:',
			'type' => 'select',
			'name' => 'starter_course_id',
			'attribute' => 'title',
			'model' => 'App\Models\Course'
		]);

		$this->crud->addField([
			'label' => 'Lock the session until this date:',
			'name' => 'lock_date',
			'type' => 'datetime_picker',
			'date_picker_options' => [
				'format' => 'dd-mm-yyyy g:ia'
			]
		]);

		$this->crud->addField([
			'name' => 'type',
			'type' => 'hidden',
			'value' => \App\Models\Session::class
		]);

		
		/**
		 * Setup CRUD filters
		 */
		$this->crud->addFilter([
			'type' => 'select2',
			'name' => 'lesson',
			'label'=> 'Lesson'
		], function() {
			$lessons = \App\Models\Lesson::all()->pluck('title', 'id')->toArray();
			return $lessons;
		},  function($value) {
			$this->crud->addClause('where', 'lesson_id', $value);
		});

		$this->crud->addFilter([
			'type' => 'select2',
			'name' => 'course',
			'label'=> 'Course intro video'
		], function() {
			$courses = \App\Models\Course::all()->pluck('title', 'id')->toArray();
			return $courses;
		},  function($value) {
			$this->crud->addClause('where', 'starter_course_id', $value);
		});

		/**
		 * Add CRUD action button
		 */
		$this->crud->addButton('line', 'view_in_frontend', 'model_function', 'view_in_frontend_button', 'end');

		/**
		 * Enable CRUD reorder
		 */
		$this->crud->enableReorder('title', 1);
		$this->crud->allowAccess('reorder');
		$this->crud->orderBy('lft');

		/**
		 * Modify default sessions query If there
		 * is no filter for course starter videos
		 */
		if(!$this->request->get('course')) {
			$this->crud->addClause('whereNull', 'starter_course_id');
		}
	}

	public function store(StoreRequest $request)
	{
		$this->crud->hasAccessOrFail('create');

		// fallback to global request instance
		if (is_null($request)) {
			$request = \Request::instance();
		}

		$args = [];
		if($request->has('starter_course_id')) {
			$args['lesson_id'] = null;
		}

		// replace empty values with NULL, so that it will work with MySQL strict mode on
		foreach ($request->input() as $key => $value) {
			if (empty($value) && $value !== '0') {
				$request->request->set($key, null);
			}
		}

		// insert item in the db
		$item = $this->crud->create($request->except(['save_action', '_token', '_method']));
		$item->update($args);
		$this->data['entry'] = $this->crud->entry = $item;

		// show a success message
		\Alert::success(trans('backpack::crud.insert_success'))->flash();

		// save the redirect choice for next time
		$this->setSaveAction();

		return $this->performSaveAction($item->getKey());
	}

	public function update(UpdateRequest $request)
	{
		$response = parent::updateCrud();


		/**
		 * Prevent setting this session to
		 * be attached on Course and Lesson in same time
		 */
		$session_id = $request->get('id');
		$session = Session::findOrFail($session_id);

		if($request->has('starter_course_id')) {
			$session->update(['lesson_id' => null]);
		}

		return $response;
	}
}
