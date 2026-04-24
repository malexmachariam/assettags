<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MigrateController extends Controller
{
    public function migrate(Request $request)
    {
        // Run composer require phpoffice/phpword
        $composerOutput = shell_exec('composer require phpoffice/phpword 2>&1');

        $exitCode = Artisan::call('migrate', [
            '--force' => true,
        ]);
        $output = Artisan::output();
        return response("Composer output:<pre>$composerOutput</pre>\nMigration finished with exit code $exitCode. Output: <pre>$output</pre>");
    }
}
