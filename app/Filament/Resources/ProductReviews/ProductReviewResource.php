<?php

namespace App\Filament\Resources\ProductReviews;

use App\Filament\Resources\ProductReviews\Pages\CreateProductReview;
use App\Filament\Resources\ProductReviews\Pages\EditProductReview;
use App\Filament\Resources\ProductReviews\Pages\ListProductReviews;
use App\Models\Product;
use App\Models\ProductReview;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductReviewResource extends Resource
{
    protected static ?string $model = ProductReview::class;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-message-star';

    protected static ?int $navigationSort = 10;

    public static function getNavigationGroup(): string
    {
        return __('Shop');
    }

    public static function getNavigationLabel(): string
    {
        return __('Reviews');
    }

    public static function getModelLabel(): string
    {
        return __('Review');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Reviews');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make([
                'default' => 1,
                'xl' => 3,
                '2xl' => 3,
            ])
                ->schema([
                    Section::make(__('Review'))
                        ->schema([
                            Select::make('product_id')
                                ->label(__('Product'))
                                ->relationship(
                                    name: 'product',
                                    titleAttribute: 'title_en',
                                )
                                ->getOptionLabelFromRecordUsing(
                                    fn (Product $record): string => $record->title
                                )
                                ->searchable([
                                    'title_en',
                                    'title_fr',
                                ])
                                ->preload()
                                ->required()
                                ->prefixIcon('tabler-hanger'),

                            Textarea::make('review')
                                ->label(__('Review'))
                                ->required()
                                ->rows(10)
                                ->columnSpanFull(),

                            Grid::make(2)
                                ->schema([
                                    TextInput::make('name')
                                        ->label(__('Name'))
                                        ->required()
                                        ->maxLength(255)
                                        ->prefixIcon('tabler-user'),

                                    TextInput::make('email')
                                        ->label(__('Email'))
                                        ->email()
                                        ->maxLength(255)
                                        ->prefixIcon('tabler-mail'),
                                ]),
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'xl' => 2,
                        ]),

                    Section::make(__('Settings'))
                        ->schema([
                            Select::make('rating')
                                ->label(__('Rating'))
                                ->options([
                                    5 => '★★★★★ — 5',
                                    4 => '★★★★☆ — 4',
                                    3 => '★★★☆☆ — 3',
                                    2 => '★★☆☆☆ — 2',
                                    1 => '★☆☆☆☆ — 1',
                                ])
                                ->default(5)
                                ->required()
                                ->native(false)
                                ->prefixIcon('tabler-star-filled'),

                            Select::make('language')
                                ->label(__('Language'))
                                ->options([
                                    'en' => 'English',
                                    'fr' => 'Français',
                                ])
                                ->default('en')
                                ->required()
                                ->native(false)
                                ->prefixIcon('tabler-language'),

                            Toggle::make('active')
                                ->label(__('Approved'))
                                ->default(true)
                                ->inline(false)
                                ->onColor('success')
                                ->offColor('danger')
                                ->onIcon('tabler-check')
                                ->offIcon('tabler-x'),
                        ])
                        ->columnSpan([
                            'default' => 1,
                            'xl' => 1,
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product_title')
                    ->label(__('Product'))
                    ->state(
                        fn (ProductReview $record): string =>
                            $record->product?->title ?? '—'
                    )
                    ->searchable(
                        query: fn (
                            Builder $query,
                            string $search
                        ): Builder => $query->whereHas(
                            'product',
                            fn (Builder $productQuery): Builder =>
                            $productQuery
                                ->where('title_en', 'like', "%{$search}%")
                                ->orWhere('title_fr', 'like', "%{$search}%")
                        )
                    )
                    ->weight('medium')
                    ->limit(40),

                TextColumn::make('name')
                    ->label(__('Customer'))
                    ->description(
                        fn (ProductReview $record): ?string => $record->email
                    )
                    ->searchable([
                        'name',
                        'email',
                    ])
                    ->sortable(),

                TextColumn::make('review')
                    ->label(__('Review'))
                    ->searchable()
                    ->limit(70)
                    ->wrap()
                    ->tooltip(
                        fn (ProductReview $record): string => $record->review
                    ),

                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->icon('tabler-star-filled')
                    ->iconColor('warning')
                    ->formatStateUsing(
                        fn (int|string $state): string => "{$state}/5"
                    )
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('language')
                    ->label(__('Language'))
                    ->formatStateUsing(
                        fn (string $state): string => match ($state) {
                            'fr' => 'FR',
                            default => 'EN',
                        }
                    )
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                IconColumn::make('active')
                    ->label(__('Approved'))
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label(__('Approved')),

                SelectFilter::make('language')
                    ->label(__('Language'))
                    ->options([
                        'en' => 'English',
                        'fr' => 'Français',
                    ]),

                SelectFilter::make('rating')
                    ->label(__('Rating'))
                    ->options([
                        5 => '5 stars',
                        4 => '4 stars',
                        3 => '3 stars',
                        2 => '2 stars',
                        1 => '1 star',
                    ]),

                SelectFilter::make('product_id')
                    ->label(__('Product'))
                    ->relationship('product', 'title_en')
                    ->searchable()
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

    public static function getPages(): array
    {
        return [
            'index' => ListProductReviews::route('/'),
            'create' => CreateProductReview::route('/create'),
            'edit' => EditProductReview::route('/{record}/edit'),
        ];
    }
}
