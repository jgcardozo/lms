<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ResourceBankCrudRequest as StoreRequest;
use App\Http\Requests\Admin\ResourceBankCrudRequest as UpdateRequest;
use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;

class ResourcesBankCrudController extends CrudController
{

    use BackpackCrudTrait;

    public function setup()
    {
        $this->crud->setModel('App\Models\ResourcesBank');
        $this->crud->setRoute('admin/resourcesbank');
        $this->crud->setEntityNameStrings('resource', 'Resources Pages');

        /**
         * Define CRUD list columns
         */
        $this->crud->setColumns([
            [
                'name' => 'id',
                'label' => 'ID',
            ],
            [
                'label' => 'Title',
                'type' => 'model_function',
                'function_name' => 'admin_editable_title',
            ],
            [
                'name' => 'description',
                'label' => 'Description',
            ],
            [
                'name' => 'slug',
                'label' => 'Slug',
            ],
        ]);

        /**
         * Add CRUD fields
         */
        $this->crud->addField([
            'label' => 'Resource dashboard image',
            'name' => 'featured_image',
            'type' => 'upload',
            'upload' => true,
            'disk' => 's3',
        ]);

        $this->crud->addField([
            'label' => 'Resource header image',
            'name' => 'header_image',
            'type' => 'upload',
            'upload' => true,
            'disk' => 's3',
        ]);

        $this->crud->addField([
            'name' => 'title',
            'label' => 'Title',
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Slug',
        ]);

        $this->crud->addField([
            'name' => 'description',
            'label' => 'Description',
            'type' => 'wysiwyg',
        ]);

        $this->crud->addField([
            'name' => 'content',
            'label' => 'Content',
            'type' => 'wysiwyg',
        ]);


        $this->crud->addField([
            'label' => 'Lock tags:',
            'type' => 'select2_multipleIsTags',
            'name' => 'lock_tags',
            'entity' => 'tags',
            'attribute' => 'title',
            'model' => 'App\Models\ISTag',
            'pivot' => true,
        ]);

        $this->crud->addField([
            'label' => 'Resources children:',
            'type' => 'select2_multipleResourceChild',
            'name' => 'resources_children',
            'entity' => 'resources_children',
            'attribute' => 'title',
            'model' => 'App\Models\ResourcesChild',
            'pivot' => true,
        ]);

        $this->crud->addField([
            'name' => 'published',
            'label' => 'Published',
            'type' => 'checkbox',
        ]);

        /**
         * Enable CRUD reorder
         */
        $this->crud->enableReorder('title', 1);
        $this->crud->allowAccess('reorder');
        $this->crud->orderBy('lft');
    } //setup

    public function store(StoreRequest $request)
    {
        return parent::storeCrud();
    }

    public function update(UpdateRequest $request)
    {
        return parent::updateCrud();
    }

} //class
