<?php

namespace App\Filament\Resources\Orders;

use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Models\Order;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string | BackedEnum | null $navigationIcon = 'tabler-shopping-cart';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string
    {
        return __('Shop');
    }

    public static function getNavigationLabel(): string
    {
        return __('Orders');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make([
                'default' => 1,
                'xl' => 4,
                '2xl' => 4,
            ])->schema([

                Section::make('Order')
                    ->schema([
                        TextInput::make('order_number')
                            ->label(__('Order Number'))
                            ->disabled()
                            ->dehydrated(false),

                        Select::make('status')
                            ->label(__('Status'))
                            ->options(self::statuses())
                            ->required()
                            ->native(false),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label(__('Name')),

                                TextInput::make('secondname')
                                    ->label(__('Second Name')),

                                TextInput::make('phone')
                                    ->label(__('Phone')),

                                TextInput::make('email')
                                    ->label(__('Email')),

                                TextInput::make('country')
                                    ->label(__('Country')),

                                TextInput::make('city')
                                    ->label(__('City')),

                                TextInput::make('zip')
                                    ->label(__('Postal Code')),
                            ]),

                        Textarea::make('address')
                            ->label(__('Address'))
                            ->rows(3),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'xl' => 2,
                    ]),

                Section::make('Items')
                    ->schema([
                        Placeholder::make('items')
                            ->label(__('Order Items'))
                            ->content(fn (?Order $record) => new HtmlString(self::itemsHtml($record))),
                    ])
                    ->columnSpan([
                        'default' => 1,
                        'xl' => 2,
                    ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label(__('Order Number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium'),

                TextColumn::make('full_name')
                    ->label(__('Customer'))
                    ->state(fn (Model $record) => trim("{$record->name} {$record->secondname}"))
                    ->url(fn (Model $record) => self::getUrl('edit', ['record' => $record]))
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query
                        ->where(fn (Builder $query) => $query
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('secondname', 'like', "%{$search}%")
                        ))
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query
                        ->orderBy('name', $direction)
                        ->orderBy('secondname', $direction)),

                TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('total_amount')
                    ->label(__('Amount'))
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2, ',', ' ') . ' €')
                    ->alignEnd(),

                TextColumn::make('status')
                    ->label(__('Status'))
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'processing' => 'purple',
                        'shipped' => 'info',
                        'delivered' => 'success',
                        'return' => 'gray',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => self::statuses()[$state] ?? __('Unknown')),

                TextColumn::make('created_at')
                    ->label(__('Created'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->color('gray'),

                TextColumn::make('show')
                    ->label(__('Show'))
                    ->state(fn (Order $record) => $record->order_number)
                    ->url(fn (Order $record) => route('order.status', $record->order_number))
                    ->openUrlInNewTab()
                    ->icon('tabler-external-link'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('Status'))
                    ->options(self::statuses()),
            ])
            ->actions([
                ViewAction::make(),
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

    protected static function statuses(): array
    {
        return [
            'pending' => 'Payment pending',
            'paid' => 'Paid',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'return' => 'Return',
            'cancelled' => 'Cancelled',
        ];
    }

    protected static function itemsHtml(?Order $record): string
    {
        if (! $record || empty($record->items)) {
            return '<p class="text-sm text-gray-500">No items</p>';
        }

        $html = '<div class="space-y-4">';

        foreach ($record->items as $item) {
            $title = data_get($item, 'product.title', data_get($item, 'title', 'Product'));
            $image = data_get($item, 'product.image', data_get($item, 'image'));
            $price = (float) data_get($item, 'product.price', data_get($item, 'price', 0));
            $quantity = (int) data_get($item, 'quantity', 1);

            $sizeTitle = data_get($item, 'size_title');

            if (! $sizeTitle && data_get($item, 'size_id')) {
                $size = collect(data_get($item, 'product.sizes', []))
                    ->firstWhere('id', data_get($item, 'size_id'));

                $sizeTitle = data_get($size, 'title');
            }

            $option = $sizeTitle ? ' (' . e($sizeTitle) . ')' : '';

            $html .= '<div class="flex items-start gap-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-800">';

            if ($image) {
                $html .= '<img src="' . e($image) . '" alt="' . e($title) . '" class="h-16 w-16 rounded object-cover">';
            }

            $html .= '<div class="flex-1">';
            $html .= '<h4 class="font-medium text-gray-900 dark:text-gray-100">' . e($title) . $option . '</h4>';
            $html .= '<p class="text-sm text-gray-600 dark:text-gray-400">Quantity: ' . $quantity . '</p>';
            $html .= '<p class="text-sm font-medium text-gray-900 dark:text-gray-100">' . number_format($price, 0, ',', ' ') . ' €</p>';
            $html .= '</div>';
            $html .= '</div>';
        }

        $html .= '<div class="mt-4 space-y-2 border-t border-gray-200 pt-4 dark:border-gray-700">';
        $html .= '<div class="flex justify-between text-sm"><span class="text-gray-600 dark:text-gray-400">Subtotal:</span><span class="font-medium">' . number_format((float) $record->subtotal, 2, '.', ' ') . ' €</span></div>';
        $html .= '<div class="flex justify-between text-sm"><span class="text-gray-600 dark:text-gray-400">Delivery (' . e($record->country) . '):</span><span class="font-medium">' . number_format((float) $record->delivery_cost, 2, '.', ' ') . ' €</span></div>';
        $html .= '<div class="flex justify-between border-t border-gray-300 pt-2 text-base font-semibold dark:border-gray-600"><span>Total:</span><span>' . number_format((float) $record->total_amount, 2, '.', ' ') . ' €</span></div>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
            'view' => ViewOrder::route('/{record}'),
        ];
    }
}
