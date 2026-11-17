<form method="POST" action="{{ route('contact.store') }}" class="hv-form">
    @csrf
    <h2 class="hv-title" style="font-size:clamp(32px,3vw,48px);">{{ $formTitle ?? 'Send Project Request' }}</h2>
    <p class="hv-lead">Fill the essentials. Leivant reviews and responds with the right next step — not an automatic price.</p>
    <input type="hidden" name="subject" value="{{ old('subject', $defaultSubject ?? 'Construction project inquiry') }}">
    <div class="hv-form__row">
        <label class="hv-field">
            <span>Full name *</span>
            <input name="name" value="{{ old('name') }}" required autocomplete="name">
            @error('name') <small>{{ $message }}</small> @enderror
        </label>
        <label class="hv-field">
            <span>Phone / WhatsApp *</span>
            <input name="phone" value="{{ old('phone') }}" required autocomplete="tel">
            @error('phone') <small>{{ $message }}</small> @enderror
        </label>
        <label class="hv-field">
            <span>Email</span>
            <input type="email" name="email" value="{{ old('email') }}" autocomplete="email">
            @error('email') <small>{{ $message }}</small> @enderror
        </label>
        <label class="hv-field">
            <span>Project type *</span>
            <select name="project_type" required>
                @foreach (['Construction service', 'House planning and design', 'Construction tools or materials', 'Heavy equipment rental', 'BOQ preparation', 'Renovation or finishing', 'Construction supervision', 'Provider connection'] as $option)
                    <option value="{{ $option }}" @selected(old('project_type') === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </label>
        <label class="hv-field">
            <span>Project / site location</span>
            <input name="site_location" value="{{ old('site_location') }}" placeholder="Example: Mbezi, Dar es Salaam">
        </label>
        <label class="hv-field">
            <span>Timeline</span>
            <select name="timeline">
                @foreach (['Planning stage', 'Urgent / this week', 'Within 2 weeks', 'Within 1 month', 'Within 1 to 3 months', 'Not sure yet'] as $option)
                    <option value="{{ $option }}" @selected(old('timeline') === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </label>
        <label class="hv-field">
            <span>Budget range</span>
            <select name="budget_range">
                @foreach (['Need quotation first', 'Under TZS 5M', 'TZS 5M - 25M', 'TZS 25M - 100M', 'TZS 100M+', 'Not sure yet'] as $option)
                    <option value="{{ $option }}" @selected(old('budget_range') === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </label>
        <label class="hv-field">
            <span>Preferred contact</span>
            <select name="preferred_contact">
                @foreach (['whatsapp' => 'WhatsApp', 'phone' => 'Phone', 'email' => 'Email'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('preferred_contact') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <label class="hv-field" style="grid-column:1/-1;">
            <span>Project details *</span>
            <textarea name="project_details" required placeholder="Tell us what you want to build, finish, renovate, source, or price.">{{ old('project_details') }}</textarea>
            @error('project_details') <small>{{ $message }}</small> @enderror
        </label>
    </div>
    <button class="hv-btn" style="width:100%;margin-top:8px;" type="submit">Send to Leivant</button>
    <p class="hv-pill" style="white-space:normal;line-height:1.7;text-transform:none;letter-spacing:0;display:block;">
        {{ $nextNote ?? 'After you submit, Leivant reviews your scope, location, budget, timeline, and site needs - then replies by your preferred contact method with a clear next step. Not an automatic price. A real professional response.' }}
    </p>
</form>
