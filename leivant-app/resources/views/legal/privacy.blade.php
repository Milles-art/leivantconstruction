@extends('layouts.seo')

@section('title', 'Privacy Policy | Leivant Construction Solutions')
@section('meta_description', 'Leivant privacy policy for project inquiries, marketplace orders, provider discovery, construction planning, and client communication.')
@section('canonical', route('privacy'))

@section('content')
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow">Privacy Policy</p>
            <h1>How Leivant handles project information.</h1>
            <p class="lead">We collect only the details needed to review construction requests, coordinate providers, process marketplace orders, and respond to clients professionally.</p>
        </div>
    </section>

    <section class="section section-paper">
        <div class="container" style="max-width:900px;">
            <div class="grid">
                @foreach ([
                    ['Information We Collect', 'Name, phone, email, project location, site details, service needs, equipment orders, preferred contact method, and provider registration details where applicable.'],
                    ['How We Use It', 'We use submitted information to contact clients, prepare quotations, coordinate providers, review checkout requests, arrange payment instructions, and improve Leivant service operations.'],
                    ['Sharing', 'Project details may be shared with relevant contractors, suppliers, equipment providers, technical teams, or logistics partners only where needed to support the request.'],
                    ['Contact', 'For privacy questions, contact '.config('app.company.email').' or '.config('app.company.phone').'.'],
                ] as [$heading, $body])
                    <article class="card card-pad">
                        <h2>{{ $heading }}</h2>
                        <p>{{ $body }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection

