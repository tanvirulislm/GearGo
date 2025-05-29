<?php

namespace App\Filament\Resources;

use App\Models\Car;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\ToggleButtons;
use App\Filament\Resources\CarResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CarResource\RelationManagers;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('brand')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Enter the brand of the car, e.g., Toyota, Ford, etc.'),
                TextInput::make('model')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Enter the model of the car, e.g., Corolla, Focus, etc.'),
                TextInput::make('year')
                    ->label('Manufacture Year')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(date('Y'))
                    ->helperText('Enter the year the car was manufactured.'),
                Select::make('car_type')
                    ->label('Car Type')
                    ->options([
                        'sedan' => 'Sedan',
                        'suv' => 'SUV',
                        'hatchback' => 'Hatchback',
                        'coupe' => 'Coupe',
                        'convertible' => 'Convertible',
                        'wagon' => 'Station Wagon',
                        'minivan' => 'Minivan',
                        'pickup' => 'Pickup Truck',
                        'sports_car' => 'Sports Car',
                        'electric' => 'Electric Vehicle (EV)',
                        'hybrid' => 'Hybrid',
                        'luxury' => 'Luxury Car',
                        'off_road' => 'Off-Road Vehicle',
                    ])
                    ->searchable()
                    ->required()
                    ->helperText('Select from common car types.'),
                Select::make('fuel_type')
                    ->label('Fuel Type')
                    ->required()
                    ->options([
                        'petrol' => 'Petrol (Gasoline)',
                        'diesel' => 'Diesel',
                        'electric' => 'Electric (EV)',
                        'hybrid' => 'Hybrid',
                        'cng' => 'CNG (Compressed Natural Gas)',
                        'lpg' => 'LPG (Liquefied Petroleum Gas)',
                        'hydrogen' => 'Hydrogen Fuel Cell',
                        'ethanol' => 'Ethanol (E85/Biofuel)',
                    ])
                    ->searchable()
                    ->helperText('Select the fuel type from the list.'),
                Select::make('transmission')
                    ->label('Transmission')
                    ->required()
                    ->options([
                        'manual' => 'Manual',
                        'automatic' => 'Automatic',
                        'semi_automatic' => 'Semi-Automatic',
                        'cvt' => 'CVT (Continuously Variable)',
                        'dual_clutch' => 'Dual-Clutch (DCT)',
                    ])
                    ->searchable()
                    ->helperText('Select the transmission type.'),
                TextInput::make('mileage')
                    ->numeric()
                    ->required()
                    ->helperText('Enter the mileage of the car.'),
                Select::make('seats')
                    ->label('Number of Seats')
                    ->required()
                    ->options([
                        2 => '2 Seats',
                        4 => '4 Seats',
                        5 => '5 Seats',
                        6 => '6 Seats',
                        7 => '7 Seats',
                        8 => '8 Seats',
                        9 => '9 Seats',
                        10 => '10 Seats',
                    ])
                    ->searchable()
                    ->helperText('Select the number of seats.'),
                ColorPicker::make('color')
                    ->required()
                    ->helperText('Select the color of the car. You can also enter a hex color code.'),
                TextInput::make('registration_number')
                    ->required()
                    ->maxLength(255)
                    ->unique(Car::class, 'registration_number', ignoreRecord: true)
                    ->helperText('Enter the car\'s registration number. This should be unique.'),
                TextInput::make('daily_rent_price')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Enter the daily rent price of the car.'),
                ToggleButtons::make('status')
                    ->default('available')
                    ->required()
                    ->options([
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'maintenance' => 'Maintenance',
                    ])
                    ->colors([
                        'available' => 'success',
                        'rented' => 'warning',
                        'maintenance' => 'danger',
                    ])
                    ->inline()
                    ->icons([
                        'available' => 'heroicon-o-check-circle',
                        'rented' => 'heroicon-o-clock',
                        'maintenance' => 'heroicon-o-wrench',
                    ]),
                FileUpload::make('image')
                    ->required()
                    ->image()
                    ->maxSize(1024)
                    ->disk('public')
                    ->directory('car_images')
                    ->preserveFilenames(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('brand')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('year')
                    ->sortable(),
                TextColumn::make('car_type')
                    ->sortable(),
                TextColumn::make('fuel_type')
                    ->sortable(),
                TextColumn::make('transmission')
                    ->sortable(),
                TextColumn::make('mileage')
                    ->sortable(),
                TextColumn::make('seats')
                    ->sortable(),
                TextColumn::make('color')
                    ->sortable(),
                TextColumn::make('registration_number')
                    ->sortable(),
                TextColumn::make('daily_rent_price')
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'danger' => 'maintenance',
                        'warning' => 'rented',
                        'success' => 'available',
                    ])
                    ->icon(fn($state) => match ($state) {
                        'maintenance' => 'heroicon-o-wrench',
                        'rented' => 'heroicon-o-clock',
                        'available' => 'heroicon-o-check-badge',
                        default => null,
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'maintenance' => 'Maintenance',
                        'rented' => 'Rented',
                        'available' => 'Available',
                        default => $state
                    }),
                ImageColumn::make('image')
                    ->sortable(),
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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
        ];
    }
}
