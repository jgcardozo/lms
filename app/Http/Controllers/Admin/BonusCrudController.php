<?php

namespace App\Http\Controllers\Admin;

use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\BonusCrudRequest as StoreRequest;
use App\Http\Requests\Admin\BonusCrudRequest as UpdateRequest;

class BonusCrudController extends CrudController
{
	use BackpackCrudTrait;

	public function setup()
	{
		$this->crud->setModel('App\Models\Bonus');
		$this->crud->setRoute('admin/bonus');
		$this->crud->setEntityNameStrings('bonus', 'bonuses');

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
            'label' => 'Video Type:',
            'type' => 'select',
            'name' => 'video_type_id',
            'attribute' => 'title',
            'model' => 'App\Models\VideoType',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-2'
            ]
        ]);

        $this->crud->addField([
            'name' => 'video_url',
            'label' => 'Video ID',
            'wrapperAttributes' => [
                'class' => 'form-group col-md-10'
            ]
        ]);

        $this->crud->addField([
            'label' => 'Bonus featured image',
            'name' => 'featured_image',
            'type' => 'upload',
            'upload' => true,
            'disk' => 's3'
        ]);

        $this->crud->addField([
            'label' => 'Bonus header image',
            'name' => 'header_image',
            'type' => 'upload',
            'upload' => true,
            'disk' => 's3'
        ]);

        $this->crud->addField([
            'name' => 'content',
            'label' => 'Content',
            'type' => 'wysiwyg'
        ]);

		$this->crud->addField([
			'label' => 'Lock tags:',
			'type' => 'select2_multipleIsTags',
			'name' => 'lock_tags',
			'entity' => 'tags',
			'attribute' => 'title',
			'model' => 'App\Models\ISTag',
			'pivot' => true
		]);

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
