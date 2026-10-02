@extends('layouts.admin')

@section('title', 'Reviews Admin | Leivant')

@section('admin')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="admin-eyebrow">Reviews</p>
            <h1 class="mt-2">Customer Review Queue</h1>
            <p class="mt-2 max-w-2xl text-sm">Moderate public product and provider feedback before it appears on the client side.</p>
        </div>
        <span class="admin-pill">{{ $reviews->total() }} Reviews</span>
    </div>

    <form method="GET" class="admin-filter-bar mt-6 md:grid-cols-[1fr_auto_auto_auto]">
        <input class="vant-input min-h-12" name="search" value="{{ request('search') }}" placeholder="Search customer, product, provider, or comment">
        <select class="vant-input min-h-12" name="status">
            <option value="">All status</option>
            <option value="approved" @selected(request('status') === 'approved')>Approved</option>
            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
        </select>
        <select class="vant-input min-h-12" name="rating">
            <option value="">All ratings</option>
            @for ($rating = 5; $rating >= 1; $rating--)
                <option value="{{ $rating }}" @selected(request('rating') == $rating)>{{ $rating }} star</option>
            @endfor
        </select>
        <button class="vant-button" type="submit">Filter</button>
    </form>

    <form method="POST" action="{{ route('admin.reviews.bulk') }}" class="admin-table-wrap mt-5">
        @csrf
        <div class="flex flex-col gap-3 border-b border-zinc-100 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap items-center gap-2">
                <select class="vant-input min-h-10" name="action" required>
                    <option value="">Bulk action</option>
                    <option value="approve">Approve selected</option>
                    <option value="hold">Hold selected</option>
                </select>
                <button class="vant-button-outline py-2" type="submit">Apply</button>
            </div>
            <a href="{{ route('admin.reviews.index') }}" class="text-xs font-extrabold uppercase tracking-wide text-vant-gold">Reset filters</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-left text-sm">
                <thead>
                    <tr>
                        <th class="px-4 py-3"><input type="checkbox" onclick="document.querySelectorAll('[data-review-check]').forEach((box) => box.checked = this.checked)"></th>
                        <th class="px-4 py-3 uppercase">Review</th>
                        <th class="px-4 py-3 uppercase">Target</th>
                        <th class="px-4 py-3 uppercase">Rating</th>
                        <th class="px-4 py-3 uppercase">Status</th>
                        <th class="px-4 py-3 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($reviews as $review)
                        <tr>
                            <td class="px-4 py-4"><input data-review-check type="checkbox" name="reviews[]" value="{{ $review->id }}"></td>
                            <td class="px-4 py-4">
                                <p class="font-bold text-zinc-950">{{ $review->user?->name ?? 'Customer' }}</p>
                                <p class="mt-1 max-w-xl text-sm text-zinc-600">{{ $review->comment ?: 'No written comment.' }}</p>
                            </td>
                            <td class="px-4 py-4 text-zinc-700">{{ $review->product?->name ?? $review->provider?->name ?? 'General' }}</td>
                            <td class="px-4 py-4 font-bold text-vant-gold">{{ $review->rating }}/5</td>
                            <td class="px-4 py-4">
                                <span class="admin-badge {{ $review->is_approved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $review->is_approved ? 'Approved' : 'Pending' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="comment" value="{{ $review->comment }}">
                                        <input type="hidden" name="is_approved" value="{{ $review->is_approved ? 0 : 1 }}">
                                        <button class="font-bold text-vant-gold" type="submit">{{ $review->is_approved ? 'Hold' : 'Approve' }}</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-10 text-center text-zinc-500">No reviews found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-zinc-100 p-4">{{ $reviews->links() }}</div>
    </form>
@endsection
