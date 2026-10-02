@if (session('success') || session('error') || $errors->any())
    <div class="vant-container mt-6">
        @if (session('success'))
            <div class="rounded-lg border border-vant-green/40 bg-vant-green/15 p-4 text-emerald-100">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-vant-orange/40 bg-vant-orange/15 p-4 text-orange-100">
                {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-vant-orange/40 bg-vant-orange/15 p-4 text-orange-100">
                <p class="font-bold">Please check the highlighted details.</p>
                <ul class="mt-2 list-inside list-disc text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
