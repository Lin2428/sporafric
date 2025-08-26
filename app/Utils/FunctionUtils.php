<?php

namespace App\Utils;

class FunctionUtils
{
    public static function download($file_name, $folderl = null)
    {
        
        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $file_name);

        if($folderl){
            $filePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderl . DIRECTORY_SEPARATOR . $file_name);
        }

        if (!file_exists($filePath)) {
            abort(404, 'Fichier non trouvé.');
        }

        return response()->download($filePath);
    }
}