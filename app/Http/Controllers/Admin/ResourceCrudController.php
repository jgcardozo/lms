<?php

namespace App\Http\Controllers\Admin;


use App\Models\Module;
use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\ResourceCrudRequest as StoreRequest;
use App\Http\Requests\Admin\ResourceCrudRequest as UpdateRequest;

class ResourceCrudController extends CrudController
{
    use BackpackCrudTrait;

    public function setup() {
        $this->crud->setModel('App\Models\Resource');
        $this->crud->setRoute('admin/resource');
        $this->crud->setEntityNameStrings('resource', 'resources');


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
            'name' => 'file_url',
            'label' => 'File',
        ]);

		$this->crud->addField([
			'label' => 'Resource file',
			'name' => 'file_url',
			'type' => 'upload',
			'upload' => true,
			'disk' => 's3'
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
