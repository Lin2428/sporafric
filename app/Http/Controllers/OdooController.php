<?php

namespace App\Http\Controllers;

use App\Services\OdooService;



class OdooController extends Controller
{
    public function index(OdooService $odoo)
    {
        $companies = $odoo->getCompany();
        
        return $companies;
    }

    public static function syncronizeClient()
    {
        $odoo = new OdooService();
        $companies = $odoo->searchRead('res.partner',
         [
            (['is_company', '=', true])
        ], 
        [
            'name',
            'phone',
            'email',
            'city',
        ]);
        
        return $companies;
    }
}