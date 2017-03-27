<?php

namespace App\Http\Controllers\Admin;

use App\Models\Module;
use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\CourseCrudRequest as StoreRequest;
use App\Http\Requests\Admin\CourseCrudRequest as UpdateRequest;

class CourseCrudController extends CrudController
{
	use BackpackCrudTrait;

	public function setup()
	{
		$this->crud->setModel('App\Models\Course');
		$this->crud->setRoute('admin/course');
		$this->crud->setEntityNameStrings('course', 'courses');

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
				'label' => 'Wistia Video ID'
			],
			[
				'name' => 'lock_date',
				'label' => 'Lock date'
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
			'name' => 'slug',
			'label' => 'Slug'
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
			'label' => 'Wistia Video ID'
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
			'label' => 'Lock the course until this date:',
			'name' => 'lock_date',
			'type' => 'datetime_picker',
			'date_picker_options' => [
				'format' => 'dd-mm-yyyy'
			]
		]);

		$this->crud->addField([
			'label' => 'Lock tags:',
			'type' => 'select2_multiple',
			'name' => 'lock_tags',
			'entity' => 'tags',
			'attribute' => 'title',
			'model' => 'App\Models\ISTag',
			'pivot' => true
		]);

		/**
		 * Add CRUD action button
		 */
		$this->crud->addButton('line', 'view_modules', 'model_function', 'view_modules_button', 'end');
		$this->crud->addButton('line', 'view_intros', 'model_function', 'view_intros_button', 'end');

		/**
		 * Enable CRUD reorder
		 */
		$this->crud->enableReorder('title', 1);
		$this->crud->allowAccess('reorder');
		$this->crud->orderBy('lft');
	}

	public function store(StoreRequest $request)
	{
		// $response = parent::storeCrud();

		$this->crud->hasAccessOrFail('create');

		// fallback to global request instance
		if (is_null($request)) {
			$request = \Request::instance();
		}

		// replace empty values with NULL, so that it will work with MySQL strict mode on
		foreach ($request->input() as $key => $value) {
			if (empty($value) && $value !== '0') {
				$request->request->set($key, null);
			}
		}

		// insert item in the db
		$item = $this->crud->create($request->except(['save_action', '_token', '_method']));
		$this->data['entry'] = $this->crud->entry = $item;

		// Create default module and lesson for this course flow
		$module = new Module();
		$module->title = $item->title . ' module';
		$module->description = '';
		$module->video_url = '';
		$module->course_id = $item->id;
		$module->save();

		// show a success message
		\Alert::success(trans('backpack::crud.insert_success'))->flash();

		// save the redirect choice for next time
		$this->setSaveAction();

		return $this->performSaveAction($item->getKey());
	}

	public function update(UpdateRequest $request)
	{
		return parent::updateCrud();
	}
}
