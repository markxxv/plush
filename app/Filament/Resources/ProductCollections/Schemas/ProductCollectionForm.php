<?php

namespace App\Filament\Resources\ProductCollections\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductCollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug_en')
                    ->required(),
                TextInput::make('slug_fr')
                    ->default(null),
                Toggle::make('active')
                    ->required(),
                TextInput::make('name_en')
                    ->required(),
                TextInput::make('name_fr')
                    ->default(null),
                Textarea::make('description_en')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('description_fr')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('meta_title_en')
                    ->default(null),
                TextInput::make('meta_title_fr')
                    ->default(null),
                Textarea::make('meta_description_en')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('meta_description_fr')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('path_en')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('path_fr')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
