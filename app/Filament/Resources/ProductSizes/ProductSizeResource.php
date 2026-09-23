<?php

namespace App\Filament\Resources\ProductSizes;

use App\Filament\Resources\ProductSizes\Pages\CreateProductSize;
use App\Filament\Resources\ProductSizes\Pages\EditProductSize;
use App\Filament\Resources\ProductSizes\Pages\ListProductSizes;
use App\Models\ProductSize;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables;

class ProductSizeResource extends Resource
{
    protected static ?string $model = ProductSize::class;

    protected static string|\BackedEnum|null $navigationIcon = 'tabler-ruler-measure';

    protected static string|\UnitEnum|null $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Sizes';

    protected static ?string $modelLabel = 'Size';

    protected static ?string $pluralModelLabel = 'Sizes';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])
                    ->schema([
                        Section::make('Size')
                            ->schema([
                                TextInput::make('value')
                                    ->label('Value')
                                    ->placeholder('S, M, L, XL, 42, S-M')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                TextInput::make('position')
                                    ->label('Position')
                                    ->numeric()
                                    ->default(0)
                                    ->required(),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 2,
                            ]),

                        Section::make('Settings')
                            ->schema([
                                Toggle::make('active')
                                    ->label('Active')
                                    ->default(true),
                            ])
                            ->columnSpan([
                                'default' => 1,
                                'lg' => 1,
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->reorderable('position')
            ->columns([
                TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('position')
                    ->label('Position')
                    ->sortable(),

                TextColumn::make('products_count')
                    ->label('Products')
                    ->counts('products')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('active')
                    ->label('Active'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductSizes::route('/'),
            'create' => CreateProductSize::route('/create'),
            'edit' => EditProductSize::route('/{record}/edit'),
        ];
    }
}
