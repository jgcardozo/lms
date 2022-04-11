<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\ResourceChildCrudRequest as StoreRequest;
use App\Http\Requests\Admin\ResourceChildCrudRequest as UpdateRequest;
use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;

class ResourcesChildCrudController extends CrudController
{

    use BackpackCrudTrait;

    public function setup()
    {

        $this->crud->setModel('App\Models\ResourcesChild');
        $this->crud->setRoute('admin/resourceschild');
        $this->crud->setEntityNameStrings('resource', 'Resources Items');

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
                'name' => 'slug',
                'label' => 'Slug',
            ],
            
            /*[
                'label' => "Belongs to",
                'type' => "select_multiple",
                'name' => 'resourcesBank',
                'entity' => 'resourcesChildren',
                'attribute' => "title",
                'model' => "App\Models\ResourcesBank",
                'pivot' => true
            ] */

        ]);

        /**
         * Add CRUD fields
         */

        $this->crud->addField([
            'name' => 'title',
            'label' => 'Title',
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'label' => 'Slug',
        ]);

        $this->crud->addField([
            'name' => 'content',
            'label' => 'Content',
            'type' => 'wysiwyg',
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
