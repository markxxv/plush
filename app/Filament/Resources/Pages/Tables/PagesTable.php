<?php

namespace App\Filament\Resources\Pages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('title_en')
                    ->searchable(),
                TextColumn::make('title_fr')
                    ->searchable(),
                IconColumn::make('active')
                    ->boolean(),
                TextColumn::make('language')
                    ->searchable(),
                TextColumn::make('cover')
                    ->searchable(),
                TextColumn::make('url_en')
                    ->searchable(),
                TextColumn::make('url_fr')
                    ->searchable(),
                TextColumn::make('meta_title_en')
                    ->searchable(),
                TextColumn::make('meta_description_en')
                    ->searchable(),
                TextColumn::make('meta_title_fr')
                    ->searchable(),
                TextColumn::make('meta_description_fr')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
