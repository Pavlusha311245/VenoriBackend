<?php

namespace App\Services\Logs;

class DeleteLogFiles
{
    public function removeLogs()
    {
        $dir_path = 'storage/logs/';
        $files = opendir($dir_path);

        while (($file = readdir($files)) !== false) {
            if(in_array($file, array('.', '..'))) continue;
            unlink($dir_path . $file);
        }

        closedir($files);
    }
}
