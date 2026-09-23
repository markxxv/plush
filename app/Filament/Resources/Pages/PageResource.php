<?php

namespace App\Filament\Resources\Pages;

use App\Filament\Resources\Pages\Pages\CreatePage;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Models\Page;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-file-text';

    protected static ?int $navigationSort = 20;

    public static function getRecordTitle($record): ?string
    {
        return $record->title_en
            ?? $record->title_fr
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

                                        TextInput::make('title_en')
                                            ->label('Title EN')
                                            ->required()
                                            ->maxLength(255),

                                        RichEditor::make('body_en')
                                            ->label('Body EN')
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

                                                TextInput::make('url_en')
                                                    ->label('URL EN')
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

                                        TextInput::make('title_fr')
                                            ->label('Title FR')
                                            ->maxLength(255),

                                        RichEditor::make('body_fr')
                                            ->label('Body FR')
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

                                                TextInput::make('url_fr')
                                                    ->label('URL FR')
                                                    ->required()
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

                        FileUpload::make('cover')
                            ->label('Cover')
                            ->disk('public')
                            ->directory('pages')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->imagePreviewHeight('260')
                            ->removeUploadedFileButtonPosition('right'),

//                        Select::make('language')
//                            ->label('Default Language')
//                            ->options([
//                                'en' => 'English',
//                                'fr' => 'Français',
//                            ])
//                            ->default('en')
//                            ->native(false)
//                            ->prefixIcon('tabler-language'),

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

                ImageColumn::make('cover')
                    ->label('Cover')
                    ->disk('public')
                    ->width(64)
                    ->height(64)
                    ->extraImgAttributes([
                        'class' => 'rounded-lg object-cover',
                    ]),

                TextColumn::make('title_en')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('url_en')
                    ->label('URL')
                    ->searchable()
                    ->color('gray'),

                TextColumn::make('language')
                    ->label('Lang')
                    ->badge(),

                IconColumn::make('active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->sortable()
                    ->color('gray'),

            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label('Active'),

//                SelectFilter::make('language')
//                    ->options([
//                        'en' => 'English',
//                        'fr' => 'Français',
//                    ]),
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

    public static function getPages(): array
    {
        return [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record}/edit'),
        ];
    }
}
