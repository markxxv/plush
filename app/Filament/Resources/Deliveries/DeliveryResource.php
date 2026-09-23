<?php

namespace App\Filament\Resources\Deliveries;

use App\Filament\Resources\Deliveries\Pages\CreateDelivery;
use App\Filament\Resources\Deliveries\Pages\EditDelivery;
use App\Filament\Resources\Deliveries\Pages\ListDeliveries;
use App\Models\Delivery;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DeliveryResource extends Resource
{
    protected static ?string $model = Delivery::class;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-truck-delivery';

    protected static ?int $navigationSort = 30;

    public static function getNavigationGroup(): string
    {
        return __('Shop');
    }

    public static function getNavigationLabel(): string
    {
        return __('Deliveries');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    TextInput::make('iso_code')
                        ->label(__('ISO Code'))
                        ->prefixIcon('tabler-flag')
                        ->required()
                        ->maxLength(3)
                        ->placeholder('AL')
                        ->unique(ignoreRecord: true),

                    TextInput::make('country_region')
                        ->label(__('Country / Region'))
                        ->prefixIcon('tabler-world')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('price')
                        ->label(__('Price'))
                        ->prefixIcon('tabler-currency-euro')
                        ->numeric()
                        ->required()
                        ->step('0.01')
                        ->minValue(0),

                    TextInput::make('sort_order')
                        ->label(__('Sort order'))
                        ->prefixIcon('tabler-arrows-sort')
                        ->numeric()
                        ->default(0),

                    Toggle::make('is_active')
                        ->label(__('Published'))
                        ->onColor('success')
                        ->offColor('danger')
                        ->onIcon('tabler-bolt')
                        ->offIcon('tabler-power')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('iso_code')
                    ->label(__('ISO Code'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->weight('medium'),

                TextColumn::make('country_region')
                    ->label(__('Country / Region'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price')
                    ->label(__('Price'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, ',', ' ') . ' €')
                    ->alignEnd(),

                IconColumn::make('is_active')
                    ->label(__('Published'))
                    ->sortable()
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label(__('Sort'))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d-M-Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label(__('Published'))
                    ->query(fn (Builder $query): Builder => $query->where('is_active', true)),

                Filter::make('not_active')
                    ->label(__('Draft'))
                    ->query(fn (Builder $query): Builder => $query->where('is_active', false)),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDeliveries::route('/'),
            'create' => CreateDelivery::route('/create'),
            'edit' => EditDelivery::route('/{record}/edit'),
        ];
    }
}
