<?php

namespace App\Http\Controllers\Admin;

use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\EventCrudRequest as StoreRequest;
use App\Http\Requests\Admin\EventCrudRequest as UpdateRequest;

class EventCrudController extends CrudController
{
	use BackpackCrudTrait;

	public function setup()
	{
		$this->crud->setModel('App\Models\Event');
		$this->crud->setRoute('admin/event');
		$this->crud->setEntityNameStrings('event', 'events');

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
				'name' => 'start_date',
				'label' => 'Event start date'
			],
			[
				'name' => 'end_date',
				'label' => 'Event end date'
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
			'name' => 'short_description',
			'label' => 'Short description'
		]);

		$this->crud->addField([
			'name' => 'description',
			'label' => 'Description',
			'type' => 'wysiwyg'
		]);

		$this->crud->addField([
			'label' => 'Event image',
			'name' => 'event_image',
			'type' => 'upload',
			'upload' => true,
			'disk' => 's3'
		]);

		$this->crud->addField([
			'name' => 'course_id',
			'label' => 'Assign this event to course:',
			'type' => 'select2',
			'attribute' => 'title',
			'model' => 'App\Models\Course',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'name' => 'url',
			'label' => 'Apply URL',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'label' => 'Event start:',
			'name' => 'start_date',
			'type' => 'datetime_picker',
			'date_picker_options' => [
				'format' => 'dd-mm-yyyy g:ia'
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'label' => 'Event end:',
			'name' => 'end_date',
			'type' => 'datetime_picker',
			'date_picker_options' => [
				'format' => 'dd-mm-yyyy g:ia'
			],
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
            'label'     => 'Cohorts',
            'type'      => 'checklist',
            'name'      => 'cohorts',
            'entity'    => 'cohorts',
            'attribute' => 'name',
            'model'     => "App\Models\Cohort",
            'pivot'     => true,
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
