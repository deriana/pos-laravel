<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackupController extends Controller
{
    public function backupDatabase()
    {
        $dbName = env('DB_DATABASE');
        $user = env('DB_USERNAME');
        $pass = env('DB_PASSWORD');
        $host = env('DB_HOST');
        $fileName = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $fileName);

        // Pastikan folder backups ada
        if (!file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        $command = "mysqldump --user={$user} --password={$pass} --host={$host} {$dbName} > {$path}";

        $result = null;
        $output = null;
        exec($command, $output, $result);

        if ($result === 0) {
            return response()->download($path)->deleteFileAfterSend(true);
        } else {
            return back()->with('error', 'Backup database gagal.');
        }
    }
}
