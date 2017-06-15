<?php

namespace App\Http\Controllers\Admin;

use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\LessonQuestionCrudRequest as StoreRequest;
use App\Http\Requests\Admin\LessonQuestionCrudRequest as UpdateRequest;

class LessonQuestionCrudController extends CrudController
{
    use BackpackCrudTrait;

	public function setup()
	{
		$this->crud->setModel('App\Models\LessonQuestion');
		$this->crud->setRoute('admin/lessonquestion');
		$this->crud->setEntityNameStrings('question', 'questions');

		$this->crud->enableReorder('question', 1);
		$this->crud->allowAccess('reorder');
		$this->crud->orderBy('lft');

		/**
		 * Define CRUD list columns
		 */
		$this->crud->setColumns([
			[
				'name' => 'id',
				'label' => 'ID'
			],
			[
				'name' => 'question',
				'type' => 'model_function',
				'function_name' => 'admin_editable_title'
			],
			[
				'label' => 'Lesson',
				'type' => 'model_function',
				'function_name' => 'admin_lesson_link'
			],
		]);

		/**
		 * Add CRUD fields
		 */
		$this->crud->addField([
			'name' => 'question',
			'label' => 'Question'
		]);

		$this->crud->addField([
			'name' => 'video_url',
			'label' => 'Wistia Video ID',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'name' => 'video_title',
			'label' => 'Video title',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'name' => 'description',
			'label' => 'Description',
			'type' => 'wysiwyg'
		]);

		$this->crud->addField([
			'label' => 'Video image',
			'name' => 'featured_image',
			'type' => 'upload',
			'upload' => true,
			'disk' => 's3'
		]);

		$this->crud->addField([
			'name' => 'outer_url',
			'label' => 'Button URL'
		]);

		$this->crud->addField([
			'name' => 'assessment_embed_id',
			'label' => 'Assessment ID',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'name' => 'assessment_id',
			'label' => 'Assessment ID',
			'wrapperAttributes' => [
				'class' => 'form-group col-md-6'
			]
		]);

		$this->crud->addField([
			'label' => 'Assign this question to lesson:',
			'type' => 'select',
			'name' => 'lesson_id',
			'attribute' => 'backpack_crud_title',
			'model' => 'App\Models\Lesson'
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