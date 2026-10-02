<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $inquiries = Inquiry::query()
            ->with(['service', 'assignedUser'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('priority'), fn ($query) => $query->where('priority', $request->string('priority')))
            ->when($request->filled('assigned_to'), fn ($query) => $query->where('assigned_to', $request->integer('assigned_to')))
            ->when($request->boolean('due'), fn ($query) => $query->whereNotNull('follow_up_at')->where('follow_up_at', '<=', now())->whereNotIn('status', ['closed']))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.inquiries.index', [
            'inquiries' => $inquiries,
            'admins' => User::query()->where('is_admin', true)->orderBy('name')->get(),
        ]);
    }

    public function show(Inquiry $inquiry): View
    {
        return view('admin.inquiries.show', [
            'inquiry' => $inquiry->load('service', 'user', 'assignedUser', 'projectRequest.materials'),
            'admins' => User::query()->where('is_admin', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:new,in_progress,responded,closed'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'follow_up_at' => ['nullable', 'date'],
            'response' => ['nullable', 'string', 'max:5000'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
            'send_email' => ['nullable', 'boolean'],
        ]);

        $validated['responded_at'] = filled($validated['response'] ?? null) ? now() : $inquiry->responded_at;
        $sendEmail = (bool) ($validated['send_email'] ?? false);
        unset($validated['send_email']);

        $inquiry->update($validated);
        ActivityLog::record('inquiry.updated', 'Updated inquiry '.$inquiry->subject, $inquiry);

        if ($sendEmail) {
            if (blank($inquiry->email) || blank($inquiry->response)) {
                return back()->with('error', 'Inquiry saved, but email was not sent because the client email or response note is missing.');
            }

            try {
                Mail::raw($this->emailBody($inquiry), function ($message) use ($inquiry) {
                    $message->to($inquiry->email, $inquiry->name)
                        ->subject('Leivant Construction: '.$inquiry->subject);
                });

                ActivityLog::record('inquiry.email_sent', 'Sent email response for inquiry '.$inquiry->subject, $inquiry);
            } catch (Throwable $exception) {
                report($exception);

                return back()->with('error', 'Inquiry saved, but the email could not be sent. Check mail settings or mailbox status.');
            }
        }

        return back()->with('success', 'Inquiry updated.');
    }

    public function bulk(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['in_progress', 'responded', 'closed', 'high_priority', 'normal_priority'])],
            'inquiries' => ['required', 'array'],
            'inquiries.*' => ['integer', 'exists:inquiries,id'],
        ]);

        $updates = match ($validated['action']) {
            'in_progress' => ['status' => 'in_progress'],
            'responded' => ['status' => 'responded', 'responded_at' => now()],
            'closed' => ['status' => 'closed'],
            'high_priority' => ['priority' => 'high'],
            default => ['priority' => 'normal'],
        };

        $count = Inquiry::query()->whereIn('id', $validated['inquiries'])->update($updates);
        ActivityLog::record('inquiry.bulk_updated', 'Updated '.$count.' inquiry record(s)');

        return back()->with('success', $count.' inquiry record(s) updated.');
    }

    private function emailBody(Inquiry $inquiry): string
    {
        return trim(
            "Hello {$inquiry->name},\n\n"
            .$inquiry->response
            ."\n\nRegards,\n"
            .config('mail.from.name', 'Leivant Construction Solutions')
            ."\n"
            .config('app.company.phone')
        );
    }
}

