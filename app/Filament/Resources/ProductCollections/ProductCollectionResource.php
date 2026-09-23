<?php

namespace App\Filament\Resources\ProductCollections;

use App\Filament\Resources\ProductCollections\Pages\CreateProductCollection;
use App\Filament\Resources\ProductCollections\Pages\EditProductCollection;
use App\Filament\Resources\ProductCollections\Pages\ListProductCollections;
use App\Models\ProductCollection;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\FileUpload;

class ProductCollectionResource extends Resource
{
    protected static ?string $model = ProductCollection::class;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-folders';

    protected static ?string $navigationLabel = 'Collections';

    protected static ?string $modelLabel = 'Collection';

    protected static string|\UnitEnum|null $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 2;

    protected static ?string $pluralModelLabel = 'Collections';

    public static function getRecordTitle($record): ?string
    {
        return $record->name_en
            ?? $record->name_fr
            ?? null;
    }

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

                                        TextInput::make('name_en')
                                            ->label('Name EN')
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
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->prefixIcon('tabler-link'),


                                            ])
                                            ->collapsed()
                                            ->compact(),
                                    ]),

                                Tab::make('FR')
                                    ->icon('tabler-world')
                                    ->schema([

                                        TextInput::make('name_fr')
                                            ->label('Name FR')
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

                    ])
                    ->columnSpan([
                        'default' => 1,
                        'xl' => 2,
                    ]),

                Section::make('Settings')
                    ->schema([

                        Toggle::make('active')
                            ->label('Active')
                            ->default(true)
                            ->inline(false),

                        SpatieMediaLibraryFileUpload::make('cover')
                            ->label('Cover')
                            ->collection('cover')
                            ->image()
                            ->disk('public')
                            ->imageEditor()
                            ->maxSize(10240),

                        FileUpload::make('video')
                            ->label('Video')
                            ->disk('public')
                            ->directory('videos')
                            ->maxSize(262144),

                        TextInput::make('position')
                            ->label('Position')
                            ->numeric()
                            ->default(0)
                            ->prefixIcon('tabler-arrows-sort'),

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

                TextColumn::make('name_en')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug_en')
                    ->label('Slug')
                    ->searchable()
                    ->color('gray'),

                IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('position')
                    ->label('Position')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->color('gray'),

            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label('Active'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('position')
            ->defaultSort('position')
            ->paginated([25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProductCollections::route('/'),
            'create' => CreateProductCollection::route('/create'),
            'edit' => EditProductCollection::route('/{record}/edit'),
        ];
    }
}
