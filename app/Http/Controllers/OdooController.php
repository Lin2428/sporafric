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
}