<?php

namespace App\Models;
use App\Enum\GeneratorStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Devis extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'customer_id',
        'generator_id',
        'number',
        'site',
        'code_site',
        'start_date',
        'end_date',
        'is_active',
        'is_retired',
        'forfait',
        'user_id',
        'adress',
        'contact_name',
        'contact_phone',
        'contact_email',
        'lat',
        'lng',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

     protected static function booted()
    {
        static::created(function ($model) {
           if($model->generator_id != null){
            // Update the generator status to EN_LOCATION
            Generator::where('id', $model->generator_id)
            ->update(['status' =>  GeneratorStatus::EN_LOCATION->value]); 
           }

            DevisGenerator::create([
                'devis_id' => $model->id,
                'generator_id' => $model->generator_id,
                'status' => $model->is_active,
                'user_id' => auth()->user()->id,
                'created_at' => $model->start_date,
            ]);
        });

        static::updating(function ($model) {
            $statusOld = $model->getOriginal('is_active');
            $statusNew = $model->is_active;

            if ($model->isDirty('generator_id') ||($statusOld != $statusNew)) {
                $oldGenerator = $model->getOriginal('generator_id');
          
                    DevisGenerator::where('devis_id', $model->id)
                        ->where('generator_id', $oldGenerator)
                        ->update(['status' => $statusNew]);

                    Generator::where('id', $oldGenerator)
                    ->update(['status' =>  GeneratorStatus::DISPONIBLE->value]); 
                    
                    Generator::where('id', $model->generator_id)
                    ->update(['status' =>  GeneratorStatus::EN_LOCATION->value]);
            }
            if(($statusOld != $statusNew) && $statusNew == 0){
                Generator::where('id', $model->generator_id)
                    ->update(['status' =>  GeneratorStatus::EN_REVU->value]); 
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function generator()
    {
        return $this->belongsTo(Generator::class);
    }
    public function devisGenerator()
    {
        return $this->hasOne(DevisGenerator::class)->where('status', true);
    }

    public function interventions()
    {
        return $this->hasMany(Intervention::class);
    }
}
