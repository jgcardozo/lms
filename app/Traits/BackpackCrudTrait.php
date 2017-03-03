<?php

namespace App\Traits;

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
		?>
		<a href="<?php echo route('crud.' . $modelname . '.edit', $this->id); ?>" title="Edit <?php echo $modelname; ?>'"><?php echo $this->title; ?></a>
		<?php
		$title = ob_get_clean();
		return $title;
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