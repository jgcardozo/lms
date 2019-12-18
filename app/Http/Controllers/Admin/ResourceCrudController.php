<?php

namespace App\Http\Controllers\Admin;


use App\Models\Module;
use App\Models\ResourceTag;
use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\ResourceCrudRequest as StoreRequest;
use App\Http\Requests\Admin\ResourceCrudRequest as UpdateRequest;

class ResourceCrudController extends CrudController
{
    use BackpackCrudTrait;

    public function setup()
	{
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
                'function_name' => 'admin_editable_title',
                'searchLogic' => function($query, $column, $searchTerm) {
                    $query->orWhere('title','like',"%{$searchTerm}%");
                }
            ],
			[
				'label' => 'Size',
				'name' => 'file_size'
			],
            [
                'label' => "Tags",
                'type' => "select_multiple",
                'name' => 'resources',
                'entity' => 'resourceTags',
                'attribute' => "title",
                'model' => "App\Models\ResourceTag",
                'pivot' => true
            ],
            [
                'name' => 'created_at',
                'label' => 'Created'
            ],
        ]);


        /**
         * Add CRUD fields
         */
        $this->crud->addField([
            'name' => 'title',
            'label' => 'Title',
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

        $this->crud->addField([
            'label' => 'Tags:',
            'type' => 'select2_multipleAllowNew',
            'name' => 'resourceTags',
            'entity' => 'resources',
            'attribute' => 'title',
            'model' => 'App\Models\ResourceTag',
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
        unset($this->crud->create_fields['resourceTags']);

        // Exec store
        $parent = parent::storeCrud();

        $this->fixTags($request->get('resourceTags', []));

        return $parent;
    }

    public function update(UpdateRequest $request)
    {
        unset($this->crud->update_fields['resourceTags']);

        // Exec update
        $parent = parent::updateCrud();

        $this->fixTags($request->get('resourceTags', []));

        return $parent;
    }

    private function fixTags($tags)
    {
        $toAdd = [];
        $exists = [];

        foreach($tags as $tag)
        {
            try {
                $resourceTag = ResourceTag::find($tag);

                if(! $resourceTag)
                {
                    $toAdd[] = $tag;
                    continue;
                }

                $exists[] = $tag;
            }catch (\Exception $e)
            {
                $toAdd[] = $tag;
            }
        }

        foreach($toAdd as $item)
        {
            $resourceTag = new ResourceTag();
            $resourceTag->title = $item;
            $resourceTag->save();

            $exists[] = $resourceTag->id;
        }

        $action = $this->crud->entry->resourceTags()->sync($exists);

        if(!empty($action['detached']))
        {
            $this->removeIfUnused($action['detached']);
        }
    }

    private function removeIfUnused($ids)
    {
        // Remove resource tag if its not used in any resource
        $resourceTags = ResourceTag::find($ids);

        foreach($resourceTags as $resourceTag)
        {
            if($resourceTag->resources->count() == 0)
            {
                $resourceTag->delete();
            }
        }
    }
}
