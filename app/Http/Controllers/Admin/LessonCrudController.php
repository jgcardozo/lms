<?php

namespace App\Http\Controllers\Admin;

use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\LessonCrudRequest as StoreRequest;
use App\Http\Requests\Admin\LessonCrudRequest as UpdateRequest;

class LessonCrudController extends CrudController
{
	use BackpackCrudTrait;

	public function setup() {
		$this->crud->setModel('App\Models\Lesson');
		$this->crud->setRoute('admin/lesson');
		$this->crud->setEntityNameStrings('lesson', 'lessons');


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
				'label' => 'Video URL'
			],
			[
				'label' => 'Module',
				'type' => 'model_function',
				'function_name' => 'admin_module_link'
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
			'name' => 'description',
			'label' => 'Description',
			'type' => 'wysiwyg'
		]);

		$this->crud->addField([
			'name' => 'video_url',
			'label' => 'Video URL',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'name' => 'bonus_video_url',
			'label' => 'Bonus video URL',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'label' => 'Lesson featured image',
			'name' => 'featured_image',
			'type' => 'upload',
			'upload' => true,
			'disk' => 's3'
		]);

		$this->crud->addField([
			'label' => 'Assign this lesson to module:',
			'type' => 'select2',
			'name' => 'module_id',
			'attribute' => 'title',
			'model' => 'App\Models\Module'
		]);

		$this->crud->addField([
			'label' => 'Lock the lesson until this date:',
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
			'name' => 'module',
			'label'=> 'Module'
		], function() {
			$courses = \App\Models\Module::all()->pluck('title', 'id')->toArray();
			return $courses;
		},  function($value) {
			$this->crud->addClause('where', 'module_id', $value);
		});

		/**
		 * Add CRUD action button
		 */
		$this->crud->addButton('line', 'view_modules', 'model_function', 'view_sessions_button', 'end');

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
