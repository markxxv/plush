<?php

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;

new class extends Component
{
    private const STATUSES = [
        'all' => 'All Orders',
        'pending' => 'Payment pending',
        'paid' => 'Paid',
        'processing' => 'Processing',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered',
        'return' => 'Return',
        'cancelled' => 'Cancelled',
    ];

    #[Url(as: 'status', keep: true)]
    public string $statusFilter = 'all';

    public function filterByStatus(string $status): void
    {
        if (! array_key_exists($status, self::STATUSES)) {
            return;
        }

        $this->statusFilter = $status;
    }

    #[Computed]
    public function statuses(): array
    {
        return self::STATUSES;
    }

    #[Computed]
    public function orders(): Collection
    {
        return Order::query()
            ->when(
                $this->statusFilter !== 'all'
                && array_key_exists($this->statusFilter, self::STATUSES),
                fn ($query) => $query->where('status', $this->statusFilter),
            )
            ->latest('created_at')
            ->limit(60)
            ->get();
    }

    #[Computed]
    public function statusCounts(): array
    {
        $counts = Order::query()
            ->select('status')
            ->selectRaw('COUNT(*) AS aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $result = [
            'all' => (int) $counts->sum(),
        ];

        foreach (self::STATUSES as $status => $label) {
            if ($status === 'all') {
                continue;
            }

            $result[$status] = (int) ($counts[$status] ?? 0);
        }

        return $result;
    }

    public function statusBadgeClass(?string $status): string
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
            'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
            'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
            'shipped' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
            'delivered' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
            'return' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
            'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400',
        };
    }

    public function formatPhone(?string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone ?? '') ?? '';

        if (mb_strlen($phone) <= 10) {
            return $phone;
        }

        return mb_substr($phone, 0, 6) . '...' . mb_substr($phone, -3);
    }

    public function formatAddress(?string $address): string
    {
        return Str::limit($address ?? '', 30);
    }
};
?>

<div class="flex h-full flex-col">
    {{-- Status filters --}}
    <div class="flex flex-wrap gap-2 border-b border-gray-200 p-3 dark:border-gray-700">
        @foreach($this->statuses as $key => $label)
            <button
                type="button"
                wire:key="order-status-{{ $key }}"
                wire:click="filterByStatus(@js($key))"
                class="rounded-full px-3 py-1.5 text-xs font-medium transition-all
                    {{ $statusFilter === $key
                        ? 'active_pill'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300'
                    }}"
            >
                {{ $label }} ({{ $this->statusCounts[$key] ?? 0 }})
            </button>
        @endforeach
    </div>

    {{-- Orders table --}}
    <div class="flex-1 overflow-auto">
        <table class="w-full text-sm">
            <thead class="sticky top-0 bg-gray-50 dark:bg-gray-800">
            <tr class="border-b border-gray-200 dark:border-gray-700">
                <th class="px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">
                    Order
                </th>

                <th class="hidden px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300 sm:table-cell">
                    Customer
                </th>

                <th class="hidden px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300 md:table-cell">
                    Phone
                </th>

                <th class="hidden px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300 lg:table-cell">
                    Address
                </th>

                <th class="hidden px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300 xl:table-cell">
                    Date
                </th>

                <th class="px-3 py-2 text-left font-medium text-gray-700 dark:text-gray-300">
                    Status
                </th>

                <th class="px-3 py-2 text-right font-medium text-gray-700 dark:text-gray-300">
                    Amount
                </th>
            </tr>
            </thead>

            <tbody>
            @forelse($this->orders as $order)
                <tr
                    wire:key="order-{{ $order->getKey() }}"
                    class="border-b border-gray-100 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/50"
                >
                    <td class="px-3 py-3">
                        <div class="font-medium text-gray-900 dark:text-gray-100">
                            <a href="/admin/orders/{{ $order->id }}/edit">
                                #{{ $order->order_number }}
                            </a>
                        </div>

                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 sm:hidden">
                            {{ $order->name }} {{ $order->secondname }}

                            <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                                {{ $order->city }}, {{ $order->country }}
                            </div>
                        </div>

                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 md:hidden">
                            {{ $this->formatPhone($order->phone) }}
                        </div>
                    </td>

                    <td class="hidden px-3 py-3 sm:table-cell">
                        <div class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $order->name }} {{ $order->secondname }}
                        </div>

                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400 md:hidden">
                            {{ $this->formatPhone($order->phone) }}
                        </div>
                    </td>

                    <td class="hidden px-3 py-3 text-gray-700 dark:text-gray-300 md:table-cell">
                        {{ $order->phone }}
                    </td>

                    <td class="hidden px-3 py-3 lg:table-cell">
                        <div class="text-xs text-gray-700 dark:text-gray-300">
                            {{ $this->formatAddress($order->address) }}
                        </div>

                        <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ $order->city }}, {{ $order->country }}
                        </div>
                    </td>

                    <td class="hidden px-3 py-3 text-xs text-gray-600 dark:text-gray-400 xl:table-cell">
                        {{ $order->created_at->format('d M Y') }}
                        <br>

                        <span class="text-gray-400 dark:text-gray-500">
                                {{ $order->created_at->format('H:i') }}
                            </span>
                    </td>

                    <td class="px-3 py-3">
                            <span
                                class="st_{{ $order->status }} inline-flex rounded-full px-2 py-1 text-xs font-medium
                                    {{ $this->statusBadgeClass($order->status) }}"
                            >
                                {{ $order->status }}
                            </span>
                    </td>

                    <td class="px-3 py-3 text-right font-bold text-gray-900 dark:text-gray-100">
                        €{{ number_format((float) $order->total_amount, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-3 py-12 text-center">
                        <div class="text-gray-400 dark:text-gray-500">
                            <svg
                                class="mx-auto mb-3 h-12 w-12 text-gray-300 dark:text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>

                            <p class="text-sm font-medium">
                                No orders found
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
