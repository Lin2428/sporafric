<?php

namespace App\Utils;

class FunctionUtils
{
    public static function download($file_name, $file_rename = null, $folderl = null)
    {

        $filePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $file_name);

        if ($folderl) {
            $filePath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $folderl . DIRECTORY_SEPARATOR . $file_name);
        }

        if (!file_exists($filePath)) {
            abort(404, 'Fichier non trouvé.');
        }

        if ($file_rename) {
            // Ajoute l'extension d'origine si absente
            if (pathinfo($file_rename, PATHINFO_EXTENSION) === '') {
                $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                if ($extension) {
                    $file_rename .= '.' . $extension;
                }
            }
            return response()->download($filePath, $file_rename);
        }

        return response()->download($filePath);
    }
}
