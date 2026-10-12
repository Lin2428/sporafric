<?php

namespace App\Filament\Resources\HourRoundResource\Pages;

use App\Filament\Resources\HourRoundResource;
use App\Services\HourReadingService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateHourRound extends CreateRecord
{
    protected static string $resource = HourRoundResource::class;

    protected static ?string $title = 'Nouvelle tournée de relevés';

    protected function handleRecordCreation(array $data): Model
    {
        $generatorIds = $data['generator_ids'] ?? [];
        unset($data['customers'], $data['generator_ids']);

        return DB::transaction(function () use ($data, $generatorIds) {
            $round = static::getModel()::create([...$data, 'user_id' => auth()->id()]);
            HourReadingService::addGenerators($round, $generatorIds);

            return $round;
        });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
