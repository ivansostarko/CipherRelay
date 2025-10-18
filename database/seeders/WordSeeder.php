<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        // You can expand this list or load from a file; exposed via config('app.pass_words')
        $words = [
            'alpha','bravo','charlie','delta','echo','foxtrot','golf','hotel','india','juliet','kilo','lima','mike','november','oscar','papa','quebec','romeo','sierra','tango','uniform','victor','whiskey','xray','yankee','zulu',
            'apple','banana','cherry','dragon','eagle','forest','galaxy','harbor','island','jasmine','kitten','lemon','mountain','nebula','ocean','pearl','quasar','raven','sunset','thunder','uranus','vector','winter','xenon','yarrow','zephyr'
        ];
        // Save to a simple PHP config cache file (config/app.php override at runtime)
        // In a real app, consider a dedicated config file or DB table.
        file_put_contents(config_path('pass_words.php'), '<?php return '.var_export($words, true).';');
    }
}
