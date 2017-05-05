<?php

namespace App\Http\Controllers\Admin;

use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\CoachingCallRequest as StoreRequest;
use App\Http\Requests\Admin\CoachingCallRequest as UpdateRequest;

class CoachingCallsCrudController extends CrudController
{
	use BackpackCrudTrait;

	public function setup()
	{
		$this->crud->setModel('App\Models\CoachingCall');
		$this->crud->setRoute("admin/coachingcall");
		$this->crud->setEntityNameStrings('Coaching Call', 'Coaching Calls');


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
				'label' => 'Course',
				'type' => 'model_function',
				'function_name' => 'admin_course_link'
			],
			[
				'label' => 'Used as main page',
				'type' => 'model_function',
				'function_name' => 'featured_marker'
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
			'label' => 'Assign this coaching call to course:',
			'type' => 'select',
			'name' => 'course_id',
			'attribute' => 'title',
			'model' => 'App\Models\Course'
		]);

		$this->crud->addField([
			'name' => 'featured_training_coachingcall',
			'label' => 'Use content from this coaching call for main coaching call page?',
			'type' => 'checkbox'
		]);

		$this->crud->addField([
			'name' => 'type',
			'type' => 'hidden',
			'value' => \App\Models\CoachingCall::class
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
