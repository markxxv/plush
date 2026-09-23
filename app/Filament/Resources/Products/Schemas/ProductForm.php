<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_en')
                    ->required(),
                TextInput::make('title_fr')
                    ->default(null),
                Toggle::make('active')
                    ->required(),
                TextInput::make('slug_en')
                    ->required(),
                TextInput::make('slug_fr')
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
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                Toggle::make('availability')
                    ->required(),
            ]);
    }
}
