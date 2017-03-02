<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\CoachingCallRequest as StoreRequest;
use App\Http\Requests\Admin\CoachingCallRequest as UpdateRequest;


class CoachingCallsCrudController extends CrudController
{
	use BackpackCrudTrait;

	public function setup() {
		$this->crud->setModel('App\Models\CoachingCall');
		$this->crud->setRoute('admin/coachingcall');
		$this->crud->setEntityNameStrings('coaching call', 'coaching calls');

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
				'name' => 'short_description',
				'label' => 'Short description'
			],
			[
				'name' => 'description',
				'label' => 'Description'
			],
			[
				'name' => 'video_url',
				'label' => 'Video URL'
			],
			[
				'name' => 'module_group_title',
				'label' => 'Module group title'
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
			'name' => 'short_description',
			'label' => 'Short description'
		]);

		$this->crud->addField([
			'name' => 'description',
			'label' => 'Description',
			'type' => 'wysiwyg'
		]);

		$this->crud->addField([
			'name' => 'video_url',
			'label' => 'Video URL'
		]);

		$this->crud->addField([
			'name' => 'module_group_title',
			'label' => 'Module group title'
		]);

		$this->crud->addField([
			'label' => 'Course featured image',
			'name' => 'featured_image',
			'type' => 'upload',
			'upload' => true,
			'disk' => 's3'
		]);

		$this->crud->addField([
			'name' => 'course_id',
			'label' => 'Assign this coaching call to course:',
			'type' => 'select2',
			'attribute' => 'title',
			'model' => 'App\Models\Course'
		]);
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
