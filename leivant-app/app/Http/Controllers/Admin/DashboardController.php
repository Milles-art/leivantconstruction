<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        // Counts are cached for 60s: the dashboard runs ~30 aggregate
        // queries that do not need to be realtime.
        $counts = Cache::remember('admin.dashboard.counts', 60, function (): array {
            return [
                'stats' => [
                    'orders' => Order::query()->count(),
                'orders_pending' => Order::query()->where('status', 'pending')->count(),
                'orders_processing' => Order::query()->whereIn('status', ['paid', 'processing'])->count(),
                'orders_done' => Order::query()->where('status', 'completed')->count(),
                'revenue' => Order::query()->whereIn('status', ['paid', 'processing', 'completed'])->sum('total'),
                'products' => Product::query()->count(),
                'products_inactive' => Product::query()->where('is_active', false)->count(),
                'products_low_stock' => Product::query()->where('stock', '<=', 2)->count(),
                'providers' => Provider::query()->count(),
                'reviews_pending' => Review::query()->where('is_approved', false)->count(),
                'reviews_done' => Review::query()->where('is_approved', true)->count(),
                'pending_providers' => Provider::query()
                    ->where(fn ($query) => $query->where('is_active', false)->orWhere('is_verified', false))
                    ->count(),
                'inquiries' => Inquiry::query()->count(),
                'new_inquiries' => Inquiry::query()->where('status', 'new')->count(),
                'inquiries_in_progress' => Inquiry::query()->where('status', 'in_progress')->count(),
                'inquiries_done' => Inquiry::query()->whereIn('status', ['responded', 'closed'])->count(),
                'high_priority_inquiries' => Inquiry::query()->where('priority', 'high')->whereNotIn('status', ['closed'])->count(),
                'due_followups' => Inquiry::query()
                    ->whereNotIn('status', ['closed'])
                    ->whereNotNull('follow_up_at')
                    ->where('follow_up_at', '<=', now())
                    ->count(),
            ],
            'statusQueues' => [
                [
                    'label' => 'Orders',
                    'permission' => 'orders.view',
                    'route' => route('admin.orders.index'),
                    'items' => [
                        ['New / Pending', Order::query()->where('status', 'pending')->count(), 'Needs review'],
                        ['Processing', Order::query()->whereIn('status', ['paid', 'processing'])->count(), 'Being handled'],
                        ['Done', Order::query()->where('status', 'completed')->count(), 'Completed'],
                    ],
                ],
                [
                    'label' => 'Inquiries',
                    'permission' => 'pipeline.manage',
                    'route' => route('admin.inquiries.index'),
                    'items' => [
                        ['New', Inquiry::query()->where('status', 'new')->count(), 'Not touched'],
                        ['In Progress', Inquiry::query()->where('status', 'in_progress')->count(), 'Assigned/follow-up'],
                        ['Done', Inquiry::query()->whereIn('status', ['responded', 'closed'])->count(), 'Responded/closed'],
                    ],
                ],
                [
                    'label' => 'Reviews',
                    'permission' => 'reviews.manage',
                    'route' => route('admin.reviews.index'),
                    'items' => [
                        ['Pending Review', Review::query()->where('is_approved', false)->count(), 'Moderate'],
                        ['Approved', Review::query()->where('is_approved', true)->count(), 'Visible'],
                        ['Total', Review::query()->count(), 'All reviews'],
                    ],
                ],
                [
                    'label' => 'Providers',
                    'permission' => 'providers.manage',
                    'route' => route('admin.providers.index'),
                    'items' => [
                        ['Pending Review', Provider::query()->where(fn ($query) => $query->where('is_active', false)->orWhere('is_verified', false))->count(), 'Approve/verify'],
                        ['Live', Provider::query()->where('is_active', true)->where('is_verified', true)->count(), 'Published'],
                        ['Total', Provider::query()->count(), 'All providers'],
                    ],
                ],
            ],
        ];
        });

        return view('admin.dashboard', array_merge($counts, [
            'recentOrders' => Order::query()->latest()->take(6)->get(),
            'recentInquiries' => Inquiry::query()->with('assignedUser')->latest()->take(6)->get(),
            'followUps' => Inquiry::query()
                ->with('assignedUser')
                ->whereNotIn('status', ['closed'])
                ->whereNotNull('follow_up_at')
                ->orderBy('follow_up_at')
                ->take(6)
                ->get(),
            'pendingProviders' => Provider::query()
                ->with('region')
                ->where(fn ($query) => $query->where('is_active', false)->orWhere('is_verified', false))
                ->latest()
                ->take(6)
                ->get(),
        ]));
    }
}

