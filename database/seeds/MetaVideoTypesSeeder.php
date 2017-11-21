<?php

use Illuminate\Database\Seeder;

class MetaVideoTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('video_types')
          ->truncate();

        $this->insert('1', 'wistia');
        $this->insert('2', 'youtube');
        $this->insert('3', 'vimeo');
    }

    /**
     * Insert seed data to database.
     *
     * @param $id
     * @param $title
     */
    private function insert($id, $title)
    {
        DB::table('video_types')
          ->insert(
              compact(
                  'id',
                  'title'
              )
          );
    }
}
