<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class TextWidget extends Widget
{
    public string $title;

    protected static string $view = 'filament.widgets.text-widget';

    public function __construct(string $title = 'Titre')
    {
        $this->title = $title;
    }
}