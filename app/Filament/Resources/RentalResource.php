<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use App\Models\Car;
use Filament\Forms;
use Filament\Tables;
use App\Models\Rental;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\RentalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RentalResource\RelationManagers;

class RentalResource extends Resource
{
    protected static ?string $model = Rental::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('car_id')
                    ->label('Car')
                    ->searchable()
                    ->relationship('car', 'display_name')
                    ->options(
                        Car::all()->mapWithKeys(function ($car) {
                            return [$car->id => $car->display_name];
                        })->toArray()
                    )

                    ->required()
                    ->createOptionForm(CarResource::getCarFormSchema()),
                Select::make('customer_id')
                    ->label('Customer')
                    ->searchable()
                    ->options(function () {
                        return Customer::all()->mapWithKeys(fn($customer) => [
                            $customer->id => "{$customer->name} - {$customer->email} - {$customer->phone}"
                        ])->toArray();
                    })
                    ->required()
                    ->createOptionForm(CustomerResource::getCustomerFormSchema()),
                DatePicker::make('start_date')
                    ->label('Start Date')
                    ->minDate(now())
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        $endDate = $get('end_date');
                        $startDate = $get('start_date');

                        if ($endDate && Carbon::parse($endDate)->lt(Carbon::parse($startDate))) {
                            $set('end_date', $startDate);
                        }
                    })
                    ->rules([
                        'required',
                        'date',
                        'after_or_equal:today',
                    ]),
                DatePicker::make('end_date')
                    ->label('End Date')
                    ->minDate(fn(Get $get) => $get('start_date') ?: now())
                    ->required()
                    ->rules([
                        'required',
                        'date',
                        'after_or_equal:start_date',
                        function ($attribute, $value, $fail) {
                            $startDate = request()->input('start_date');
                            $carId = $this->record?->id ?? request()->route('record');

                            if (!$startDate || !$carId) return;

                            $isBooked = Rental::where('car_id', $carId)
                                ->where(function ($query) use ($startDate, $value) {
                                    $query->whereBetween('start_date', [$startDate, $value])
                                        ->orWhereBetween('end_date', [$startDate, $value])
                                        ->orWhere(function ($query) use ($startDate, $value) {
                                            $query->where('start_date', '<=', $startDate)
                                                ->where('end_date', '>=', $value);
                                        });
                                })
                                ->whereIn('status', ['approved', 'pending'])
                                ->exists();

                            if ($isBooked) {
                                $fail('This car is already booked for the selected dates.');
                            }
                        }
                    ]),
                TextInput::make('total_cost')
                    ->label('Total Cost')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->default(0),
                ToggleButtons::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ])
                    ->inline()
                    ->colors([
                        'pending' => 'gray',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'warning',
                        'completed' => 'success',
                    ])
                    ->icons([
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'rejected' => 'heroicon-o-x-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                        'completed' => 'heroicon-o-flag',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentals::route('/'),
            'create' => Pages\CreateRental::route('/create'),
            'edit' => Pages\EditRental::route('/{record}/edit'),
        ];
    }
}
