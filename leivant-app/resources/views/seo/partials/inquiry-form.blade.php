<form method="POST" action="{{ route('contact.store') }}" class="form-card">
    @csrf
    <h2>{{ $formTitle ?? 'Send Project Request' }}</h2>
    <p class="form-help">Fill the essentials. Leivant reviews and responds with the right next step - not an automatic price.</p>
    <input type="hidden" name="subject" value="{{ old('subject', $defaultSubject ?? 'Construction project inquiry') }}">
    <div class="form-grid">
        <label>
            <span>Full name *</span>
            <input name="name" value="{{ old('name') }}" required autocomplete="name">
            @error('name') <small>{{ $message }}</small> @enderror
        </label>
        <label>
            <span>Phone / WhatsApp *</span>
            <input name="phone" value="{{ old('phone') }}" required autocomplete="tel">
            @error('phone') <small>{{ $message }}</small> @enderror
        </label>
        <label>
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email">
            @error('email') <small>{{ $message }}</small> @enderror
        </label>
        <label>
            <span>Project type *</span>
            <select name="project_type" required>
                @foreach (['Construction service', 'House planning and design', 'Construction tools or materials', 'Heavy equipment rental', 'BOQ preparation', 'Renovation or finishing', 'Construction supervision', 'Provider connection'] as $option)
                    <option value="{{ $option }}" @selected(old('project_type') === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Project / site location</span>
            <input name="site_location" value="{{ old('site_location') }}" placeholder="Example: Mbezi, Dar es Salaam">
        </label>
        <label>
            <span>Timeline</span>
            <select name="timeline">
                @foreach (['Planning stage', 'Urgent / this week', 'Within 2 weeks', 'Within 1 month', 'Within 1 to 3 months', 'Not sure yet'] as $option)
                    <option value="{{ $option }}" @selected(old('timeline') === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Budget range</span>
            <select name="budget_range">
                @foreach (['Need quotation first', 'Under TZS 5M', 'TZS 5M - 25M', 'TZS 25M - 100M', 'TZS 100M+', 'Not sure yet'] as $option)
                    <option value="{{ $option }}" @selected(old('budget_range') === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </label>
        <label>
            <span>Preferred contact</span>
            <select name="preferred_contact">
                @foreach (['whatsapp' => 'WhatsApp', 'phone' => 'Phone', 'email' => 'Email'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('preferred_contact') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="full">
            <span>Project details *</span>
            <textarea name="project_details" required placeholder="Tell us what you want to build, finish, renovate, source, or price.">{{ old('project_details') }}</textarea>
            @error('project_details') <small>{{ $message }}</small> @enderror
        </label>
    </div>
    <button class="btn btn-primary" style="margin-top: 18px;width:100%;" type="submit">Send to Leivant</button>
    <div class="notice" style="margin-top:16px;border-radius:8px;padding:14px 18px;font-size:13px;color:var(--color-mid);line-height:1.65;">
        {{ $nextNote ?? 'After you submit, Leivant reviews your scope, location, budget, timeline, and site needs - then replies by your preferred contact method with a clear next step. Not an automatic price. A real professional response.' }}
    </div>
</form>
