<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\Admin\CohortsCrudRequest as StoreRequest;
use App\Http\Requests\Admin\CohortsCrudRequest as UpdateRequest;

class CohortCrudController extends CrudController
{

	public function setup()
	{
		$this->crud->setModel('App\Models\Cohort');
		$this->crud->setRoute('admin/cohort');
		$this->crud->setEntityNameStrings('cohort', 'cohorts');

		/**
		 * Define CRUD list columns
		 */
		$this->crud->setColumns([
			[
				'name' => 'id',
				'label' => 'ID'
			],
            [
                'name' => 'name',
                'label' => 'Name'
            ]
		]);

		/**
		 * Add CRUD fields
		 */
		$this->crud->addField([
			'name' => 'name',
			'label' => 'Name'
		]);

		$this->crud->addField([
            'label'     => 'Courses',
            'type'      => 'checklist',
            'name'      => 'courses',
            'entity'    => 'courses',
            'attribute' => 'title',
            'model'     => "App\Models\Course",
            'pivot'     => true,
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
