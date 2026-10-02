@extends('layouts.admin')

@section('title', 'Orders Admin | Leivant')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="admin-eyebrow">Orders</p>
            <h1 class="mt-2">Customer Orders</h1>
            <p class="mt-2 max-w-2xl text-sm">Read-only checkout records for owner visibility and client follow-up.</p>
        </div>
        <span class="admin-pill">{{ $orders->total() }} orders</span>
    </div>

    <section class="admin-table-wrap mt-7">
        <div class="flex flex-col gap-2 border-b border-zinc-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="admin-eyebrow">Order Queue</p>
                <h2>Newest orders first.</h2>
            </div>
            <span class="admin-badge bg-amber-100 text-amber-700">Read Only</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3">Order</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Region</th>
                        <th class="px-4 py-3">Total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($orders as $order)
                        <tr>
                            <td class="px-4 py-4 font-bold text-zinc-950">{{ $order->order_number }}</td>
                            <td class="px-4 py-4 text-zinc-700">
                                {{ $order->customer_name }}
                                <br>
                                <span class="text-xs text-zinc-500">{{ $order->customer_phone }}</span>
                            </td>
                            <td class="px-4 py-4 text-zinc-700">{{ $order->delivery_region }}</td>
                            <td class="px-4 py-4 font-bold text-vant-gold">TZS {{ number_format($order->total) }}</td>
                            <td class="px-4 py-4">
                                <span class="admin-badge {{ in_array($order->status, ['completed', 'paid'], true) ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ str($order->status)->headline() }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-bold text-vant-gold hover:text-vant-orange">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-zinc-500">No equipment orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-100 p-4">{{ $orders->links() }}</div>
    </section>
@endsection
