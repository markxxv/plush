<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title_en')
                    ->required(),
                TextInput::make('title_fr')
                    ->default(null),
                Textarea::make('body_en')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('body_fr')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('active')
                    ->required(),
                TextInput::make('language')
                    ->required()
                    ->default('en'),
                TextInput::make('cover')
                    ->default(null),
                TextInput::make('url_en')
                    ->required(),
                TextInput::make('url_fr')
                    ->required(),
                TextInput::make('meta_title_en')
                    ->default(null),
                TextInput::make('meta_description_en')
                    ->default(null),
                TextInput::make('meta_title_fr')
                    ->default(null),
                TextInput::make('meta_description_fr')
                    ->default(null),
            ]);
    }
}
