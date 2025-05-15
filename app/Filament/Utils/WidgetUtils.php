<?php

namespace App\Filament\Utils;

use App\Models\Contract;
use App\Models\Customer;
use App\Models\CustomerAdress;
use App\Models\Generator;
use App\Models\Piece;
use Closure;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class WidgetUtils
{
    public static function generatorSelectWidget(?Closure $onUpdate = null, ?Closure $callback = null, bool $isDispo = true): Select
    {
        $select = Select::make('generator_id')
            ->relationship('generator', 'name')
            ->allowHtml()
            ->getSearchResultsUsing(function (string $search) use ($isDispo) {
                $query = Generator::query()
                    ->where(function (Builder $query) use ($search) {
                        $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('modele', 'like', "%{$search}%")
                            ->when(intval($search), fn(Builder $query) => $query->orWhere('id', intval($search)));
                    });

                if ($isDispo) {
                    $query->where("status", "=", '0');
                }

                $result = $query->get();

                return $result
                    ->mapWithKeys(fn($generator) => [
                        $generator->id => WidgetUtils::getGeneratorSelect($generator),
                    ])->toArray();
            })
            ->getOptionLabelUsing(function ($value) {
                $generator = Generator::where('id', $value)
                    ->firstOrFail();

                return WidgetUtils::getGeneratorSelect($generator);
            })
            ->searchable()
            ->label('Groupe électrogène');

        if ($onUpdate !== null) {
            $select = $select->afterStateUpdated($onUpdate)->reactive();
        }

        return $select;
    }

    public static function customerSelectWidget(): Select
    {
        $select = Select::make('customer_id')
            ->relationship('customer', 'name')
            ->searchable()
            ->required()
            ->allowHtml()
            ->label('Client')
            ->getSearchResultsUsing(function (string $search) {
                $users = Customer::where('name', 'like', "%$search%")
                    ->orWhere('contact_c_phone', 'like', "$search%")
                    ->orWhere('contact_c_email', 'like', "$search%")
                    ->limit(50)
                    ->get();

                return $users
                    ->mapWithKeys(function ($user) {
                        return [$user->id => static::getCustomerSelect($user)];
                    })
                    ->toArray();
            })
            ->getOptionLabelUsing(function ($value) {
                $customer = Customer::where('id', $value)
                    ->firstOrFail();

                return WidgetUtils::getCustomerSelect($customer);
            })
            ->createOptionModalHeading('Nouveau client')
            ->createOptionUsing(function ($data) {
                $customer = Customer::make($data);

                if (mb_strlen($customer->email ?? '') === 0) {
                    $customer->email = null;
                }

                $customer->save();
            });
        //->createOptionForm([Grid::make(2)->schema(CustomerUtils::form())]);

        return $select;
    }

    public static function contractSelectWidget(string|null $name): Select
    {
        $select = Select::make($name ??'contract_id')
            ->relationship('contract', 'name')
            ->searchable()
            ->required()
            ->allowHtml()
            ->label('Contrat')
            ->getSearchResultsUsing(function (string $search) {
                $users = Contract::where('site', 'like', "%$search%")
                    ->orWhere('contact_phone', 'like', "$search%")
                    ->orWhere('contact_email', 'like', "$search%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'like', "$search%");
                        $query->orWhere('contact_c_phone', 'like', "$search%");
                        $query->orWhere('contact_c_email', 'like', "$search%");
                    })
                    ->limit(50)
                    ->get();

                return $users
                    ->mapWithKeys(function ($user) {
                        return [$user->id => static::getContractSelect($user)];
                    })
                    ->toArray();
            })
            ->getOptionLabelUsing(function ($value) {
                $customer = Customer::where('id', $value)
                    ->firstOrFail();

                return WidgetUtils::getCustomerSelect($customer);
            })
            ->createOptionModalHeading('Nouveau client')
            ->createOptionUsing(function ($data) {
                $customer = Customer::make($data);

                if (mb_strlen($customer->email ?? '') === 0) {
                    $customer->email = null;
                }

                $customer->save();
            });
        //->createOptionForm([Grid::make(2)->schema(CustomerUtils::form())]);

        return $select;
    }

    public static function adresseSelectWidget(): Select
    {
        $select = Select::make('customer_adresse_id')
            ->options(function (callable $get) {
                $customerId = $get('customer_id');
                $adresse = CustomerAdress::where('customer_id', $customerId)->get();
                return $adresse
                    ->mapWithKeys(function ($user) {
                        return [$user->id => static::getAdresseSelect($user)];
                    })
                    ->toArray();
            })
            ->searchable()
            ->required()
            ->preload()
            ->allowHtml()
            ->label('Adresse du client')
            ->getSearchResultsUsing(function (string $search, callable $get) {
                $customerId = $get('customer_id');
                $adresse = CustomerAdress::where('customer_id', '=', $customerId)
                    ->whereHas('city', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('district', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('quartier', function ($query) use ($search) {
                        $query->where('name', 'like', "%$search%");
                    })
                    ->limit(50)
                    ->get();

                return $adresse
                    ->mapWithKeys(function ($user) {
                        return [$user->id => static::getAdresseSelect($user)];
                    })
                    ->toArray();
            })
            ->getOptionLabelUsing(function ($value) {
                $adresse = CustomerAdress::where('id', $value)
                    ->firstOrFail();

                return WidgetUtils::getAdresseSelect($adresse);
            })
            ->createOptionModalHeading('Nouveau client')
            ->createOptionUsing(function ($data) {
                $customer = Customer::make($data);

                if (mb_strlen($customer->email ?? '') === 0) {
                    $customer->email = null;
                }

                $customer->save();
            });
        //->createOptionForm([Grid::make(2)->schema(CustomerUtils::form())]);

        return $select;
    }

    public static function pieceSelectWidget(?Closure $onUpdate = null, ?Closure $callback = null, bool $isDispo = true): Select
    {
        $select = Select::make('piece_id')
            ->allowHtml()
            ->getSearchResultsUsing(function (string $search) use ($isDispo) {
                $query = Piece::query()
                    ->where(function (Builder $query) use ($search) {
                        $query
                            ->where('reference', 'like', "%{$search}%")
                            ->orWhere('designation', 'like', "%{$search}%")
                        ;
                    });

                /*if ($isDispo) {
                    $query->where("status", "=", '0');
                }*/

                $result = $query->get();

                return $result
                    ->mapWithKeys(fn($pieces) => [
                        $pieces->id => WidgetUtils::getPieceSelect($pieces),
                    ])->toArray();
            })
            ->getOptionLabelUsing(function ($value) {
                $piece = Piece::where('id', $value)
                    ->firstOrFail();

                return WidgetUtils::getPieceSelect($piece);
            })
            ->searchable()
            ->label('Pièces');

        if ($onUpdate !== null) {
            $select = $select->afterStateUpdated($onUpdate)->reactive();
        }
        return $select;
    }

    public static function getGeneratorSelect(Generator $model): string
    {
        return view('filament.forms.components.select-generator-result')
            ->with('generator', $model)
            ->render();
    }

    public static function getCustomerSelect(Customer $model): string
    {
        return view('filament.forms.components.select-customer-result')
            ->with('customer', $model)
            ->render();
    }

    public static function getContractSelect(Contract $model): string
    {
        return view('filament.forms.components.select-contract-result')
            ->with('contract', $model)
            ->render();
    }

    public static function getAdresseSelect(CustomerAdress $model): string
    {
        return view('filament.forms.components.select-adresse-result')
            ->with('adresse', $model)
            ->render();
    }

    public static function getPieceSelect(Piece $model): string
    {
        return view('filament.forms.components.select-piece-result')
            ->with('piece', $model)
            ->render();
    }
}
