<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';

    protected static ?string $title = 'Reviews';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make([
                'default' => 1,
                'xl' => 3,
            ])
                ->schema([
                    Section::make(__('Review'))
                        ->schema([
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

                            Textarea::make('review')
                                ->label(__('Review'))
                                ->required()
                                ->rows(8)
                                ->columnSpanFull(),
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label(__('Customer'))
                    ->description(fn ($record): ?string => $record->email)
                    ->searchable(['name', 'email'])
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('review')
                    ->label(__('Review'))
                    ->searchable()
                    ->limit(80)
                    ->wrap()
                    ->tooltip(fn ($record): string => $record->review),

                TextColumn::make('rating')
                    ->label(__('Rating'))
                    ->formatStateUsing(fn ($state): string => "{$state}/5")
                    ->icon('tabler-star-filled')
                    ->iconColor('warning')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('language')
                    ->label(__('Language'))
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'fr' => 'FR',
                        default => 'EN',
                    })
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                ToggleColumn::make('active')
                    ->label(__('Approved'))
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
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Add review'))
                    ->icon('tabler-plus'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }
}