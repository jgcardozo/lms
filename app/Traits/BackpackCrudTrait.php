<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Cviebrock\EloquentSluggable\Services\SlugService;

trait BackpackCrudTrait {
	/*
	|--------------------------------------------------------------------------
	| Models
	|--------------------------------------------------------------------------
	*/
	public function admin_editable_title()
	{
		$modelname = strtolower(class_basename($this));

		ob_start();
		$title = $this->title;
		if($modelname == 'lesson')
		{
			$title = '[' . $this->module->title . '] - ' . $this->title;
		}

		if($modelname == 'lessonquestion')
		{
			$title = $this->question;
		}

		?>
		<a href="<?php echo route('crud.' . $modelname . '.edit', $this->id); ?>" title="Edit <?php echo $modelname; ?>"><?php echo $title; ?></a>
		<?php
		$title = ob_get_clean();
		return $title;
	}

	public function setSlugAttribute($value)
	{
		$request = request();

		if(empty($value))
		{
			$this->attributes['slug'] = '';
		}

		if($request->has('slug') && $value == $request->get('slug'))
		{
			$this->attributes['slug'] = $value;
			return;
		}

		$this->attributes['slug'] = SlugService::createSlug(self::class, 'slug', $value, ['unique' => true]);
	}

	/*
	|--------------------------------------------------------------------------
	| Controllers
	|--------------------------------------------------------------------------
	*/
	public function saveReorder()
	{
		$this->crud->hasAccessOrFail('reorder');

		$all_entries = \Request::input('tree');

		if (count($all_entries)) {
			$count = 0;

			foreach ($all_entries as $key => $entry) {
				if ($entry['item_id'] != '' && $entry['item_id'] != null) {
					$item = $this->crud->model->find($entry['item_id']);
					$item->lft = empty($entry['left']) ? null : $entry['left'];
					$item->save();

					$count++;
				}
			}
		}else{
			return false;
		}

		return 'success for '.$count.' items';
	}
}