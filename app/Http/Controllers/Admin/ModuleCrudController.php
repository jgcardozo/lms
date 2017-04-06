<?php

namespace App\Http\Controllers\Admin;

use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\ModuleCrudRequest as StoreRequest;
use App\Http\Requests\Admin\ModuleCrudRequest as UpdateRequest;

class ModuleCrudController extends CrudController
{
	use BackpackCrudTrait;

	public function setup() {
		$this->crud->setModel('App\Models\Module');
		$this->crud->setRoute('admin/module');
		$this->crud->setEntityNameStrings('module', 'modules');


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
				'name' => 'lock_date',
				'label' => 'Lock date'
			],
			[
				'label' => 'Course',
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
			'label' => 'Wistia Video ID'
		]);

		$this->crud->addField([
			'label' => 'Module featured image',
			'name' => 'featured_image',
			'type' => 'upload',
			'upload' => true,
			'disk' => 's3'
		]);

		// TODO: Fix this mess
		$courses = \App\Models\Course::all()->keyBy('id')->toArray();
		$coachingCalls = \App\Models\CoachingCall::all()->keyBy('id')->toArray();
		$_coursesWithCoachingCalls = $courses + $coachingCalls;
		$coursesWithCoachingCalls = [];
		foreach($_coursesWithCoachingCalls as $key => $item) {
			$coursesWithCoachingCalls[$key] = $item['title'];
		}

		$this->crud->addField([
			'name' => 'course_id',
			'label' => 'Assign this module to course:',
			'type' => 'select_from_array',
			'options' => $coursesWithCoachingCalls,
			'allows_null' => false,
		]);

		$this->crud->addField([
			'label' => 'Lock the module until this date:',
			'name' => 'lock_date',
			'type' => 'datetime_picker',
			'date_picker_options' => [
				'format' => 'dd-mm-yyyy'
			]
		]);


		/**
		 * Setup CRUD filters
		 */
		$this->crud->addFilter([
			'type' => 'select2',
			'name' => 'course',
			'label'=> 'Course'
		], function() {
			$courses = \App\Models\Course::all()->pluck('title', 'id')->toArray();
			return $courses;
		},  function($value) {
			$this->crud->addClause('where', 'course_id', $value);
		});


		/**
		 * Add CRUD action button
		 */
		$this->crud->addButton('line', 'view_modules', 'model_function', 'view_lessons_button', 'end');
		$this->crud->addButton('line', 'view_in_frontend', 'model_function', 'view_in_frontend_button', 'end');

		/**
		 * Enable CRUD reorder
		 */
		$this->crud->enableReorder('title', 1);
		$this->crud->allowAccess('reorder');
		$this->crud->orderBy('lft');
	}

	public function store(StoreRequest $request)
	{
		return parent::storeCrud();
	}

	public function update(UpdateRequest $request)
	{
		return parent::updateCrud();
	}
}
