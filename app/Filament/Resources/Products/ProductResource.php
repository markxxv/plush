<?php

namespace App\Filament\Resources\Products;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-hanger';

    protected static ?string $recordTitleAttribute = 'Product';

    protected static ?int $navigationSort = 0;

    protected static string|\UnitEnum|null $navigationGroup = 'Shop';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make([
                'default' => 1,
                'xl' => 3,
                '2xl' => 3,
            ])->schema([

                Section::make()
                    ->schema([
                        Tabs::make('Translations')
                            ->tabs([

                                Tab::make('EN')
                                    ->icon('tabler-world')
                                    ->schema([

                                        TextInput::make('title_en')
                                            ->label('Title EN')
                                            ->required()
                                            ->maxLength(255),

                                        RichEditor::make('description_en')
                                            ->label('Description EN')
                                            ->toolbarButtons([
                                                ['h2', 'h3'],
                                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                                ['bulletList', 'orderedList'],
                                                ['undo', 'redo'],
                                            ])
                                            ->columnSpanFull(),

                                        Section::make('SEO')
                                            ->icon('tabler-brand-google')
                                            ->schema([
                                                TextInput::make('meta_title_en')
                                                    ->label('Meta Title EN')
                                                    ->prefixIcon('tabler-brand-google')
                                                    ->maxLength(255),

                                                Textarea::make('meta_description_en')
                                                    ->label('Meta Description EN')
                                                    ->rows(3)
                                                    ->maxLength(255),
                                                TextInput::make('slug_en')
                                                    ->label('Slug EN')
                                                    ->maxLength(255)
                                                    ->prefixIcon('tabler-link'),
                                            ])
                                            ->collapsed()
                                            ->compact(),
                                    ]),

                                Tab::make('FR')
                                    ->icon('tabler-world')
                                    ->schema([

                                        TextInput::make('title_fr')
                                            ->label('Title FR')
                                            ->maxLength(255),


                                        RichEditor::make('description_fr')
                                            ->label('Description FR')
                                            ->toolbarButtons([
                                                ['h2', 'h3'],
                                                ['bold', 'italic', 'underline', 'strike', 'link'],
                                                ['bulletList', 'orderedList'],
                                                ['undo', 'redo'],
                                            ])
                                            ->columnSpanFull(),

                                        Section::make('SEO')
                                            ->icon('tabler-brand-google')
                                            ->schema([
                                                TextInput::make('meta_title_fr')
                                                    ->label('Meta Title FR')
                                                    ->prefixIcon('tabler-brand-google')
                                                    ->maxLength(255),

                                                Textarea::make('meta_description_fr')
                                                    ->label('Meta Description FR')
                                                    ->rows(3)
                                                    ->maxLength(255),
                                                TextInput::make('slug_fr')
                                                    ->label('Slug FR')
                                                    ->maxLength(255)
                                                    ->prefixIcon('tabler-link'),
                                            ])
                                            ->collapsed()
                                            ->compact(),
                                    ]),
                            ]),
                    SpatieMediaLibraryFileUpload::make('gallery')
                        ->label('Gallery')
                        ->collection('gallery')
                        ->multiple()
                        ->reorderable()
                        ->image()
                        ->appendFiles()
                        // ->imageEditor()
                        // ->downloadable()
                        ->openable()
                        ->imagePreviewHeight('260')
                        ->panelLayout('grid')
                        ->removeUploadedFileButtonPosition('right'),

                    ])
                    ->columnSpan([
                        'default' => 1,
                        'xl' => 2,
                    ]),


                Section::make('Settings')
                    ->schema([

                        TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->prefix('€')
                            ->required()
                            ->default(0)
                            ->prefixIcon('tabler-currency-euro'),

                        Toggle::make('active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),

                        Toggle::make('availability')
                            ->label('Available')
                            ->default(true)
                            ->inline(false),

                        Select::make('sizes')
                            ->label('Sizes')
                            ->relationship('sizes', 'value')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->prefixIcon('tabler-ruler'),

                        Select::make('collections')
                            ->label('Collections')
                            ->relationship('collections', 'name_en')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->prefixIcon('tabler-folders'),

                        Toggle::make('preorder')
                            ->label('Pre-order')
                            ->default(false)
                            ->inline(true),

                    ])
                    ->columnSpan([
                        'xl' => 1,
                        '2xl' => 1,
                    ]),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('media')
                    ->label('Image')
                    ->collection('gallery')
                    ->conversion('thumb')
                    ->filterMediaUsing(fn ($media) => $media
                        ->sortBy('order_column')
                        ->take(1)
                    )
                    ->width(64)
                    ->height(64)
                    ->extraImgAttributes([
                        'class' => 'rounded-lg object-cover',
                ]),

                TextColumn::make('title_en')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(50),

                TextColumn::make('price')
                    ->label('Price')
                    ->money('EUR')
                    ->sortable(),

                TextColumn::make('collections.name_en')
                    ->label('Collections')
                    ->badge()
                    ->separator(',')
                    ->limitList(2),

                TextColumn::make('sizes.value')
                    ->label('Sizes')
                    ->badge()
                    ->separator(',')
                    ->limitList(4),

                IconColumn::make('availability')
                    ->label('Available')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                TernaryFilter::make('availability')
                    ->label('Available'),

                SelectFilter::make('collections')
                    ->relationship('collections', 'name_en')
                    ->multiple()
                    ->preload(),

                SelectFilter::make('sizes')
                    ->relationship('sizes', 'value')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            'reviews' => RelationManagers\ReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
