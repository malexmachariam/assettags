<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MigrateController extends Controller
{
    public function migrate(Request $request)
    {
        $exitCode = Artisan::call('migrate', [
            '--force' => true,
        ]);
        $output = Artisan::output();
        return response("Migration finished with exit code $exitCode. Output: <pre>$output</pre>");
    }
}
