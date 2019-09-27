<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixCabAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv = array_map('str_getcsv', file(storage_path('cab-access.csv')));
        array_walk($csv, function(&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });
        array_shift($csv); # remove column header
        $csv = collect($csv)->pluck('Date Applied', 'Contact Id');


        foreach ($csv as $contactId => $dateApplied) {
            $userId = User::where('contact_id', $contactId)->first()->id ?? null;
            $dateAppliedParsed = \Carbon\Carbon::createFromFormat('m/d/y', $dateApplied);

            if($userId) {
                DB::table('tag_user')->where('user_id', $userId)->where('tag_id', 20601)->update([
                    'created_at' => $dateAppliedParsed,
                ]);
            }
        }
    }
}
