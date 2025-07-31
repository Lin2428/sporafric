<?php

namespace App\Filament\Pages;

use App\Enum\GeneratorStatus;
use App\Filament\Resources\GeneratorResource;
use App\Filament\Resources\RevisionResource;
use App\Models\Generator;
use Filament\Pages\Page;

class RevisionPage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Location';
    protected static ?string $title = 'Revisions';
    protected static ?int $navigationSort = 3;

       public static function getNavigationBadge(): ?string
    {
        $count = Generator::where('status',  GeneratorStatus::EN_REVU->value)->count();
        return $count;
    }

    protected static string $view = 'filament.pages.revision-page';
}
