<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->with(['product', 'provider', 'user'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('comment', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($query) => $query->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('product', fn ($query) => $query->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('provider', fn ($query) => $query->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('is_approved', $request->string('status')->toString() === 'approved'))
            ->when($request->filled('rating'), fn ($query) => $query->where('rating', $request->integer('rating')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
        ]);
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'is_approved' => ['required', 'boolean'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $review->update($validated);
        ActivityLog::record('review.updated', 'Updated review #'.$review->id, $review);

        return back()->with('success', 'Review updated.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['approve', 'hold'])],
            'reviews' => ['required', 'array'],
            'reviews.*' => ['integer', 'exists:reviews,id'],
        ]);

        $query = Review::query()->whereIn('id', $validated['reviews']);

        $approved = $validated['action'] === 'approve';
        $count = $query->update(['is_approved' => $approved]);
        ActivityLog::record('review.bulk_updated', ($approved ? 'Approved ' : 'Held ').$count.' review(s)');

        return back()->with('success', $count.' review(s) updated.');
    }

}
