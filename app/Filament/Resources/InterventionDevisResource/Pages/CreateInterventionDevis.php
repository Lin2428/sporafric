<?php

namespace App\Filament\Resources\InterventionDevisResource\Pages;

use App\Filament\Resources\InterventionDevisResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInterventionDevis extends CreateRecord
{
    protected static string $resource = InterventionDevisResource::class;

     protected static ?string $title = 'Ajouter une intervention';
}
