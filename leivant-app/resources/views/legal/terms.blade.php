@extends('layouts.seo')

@section('title', 'Terms of Service | Leivant Construction Solutions')
@section('meta_description', 'Terms for using Leivant construction services, provider discovery, equipment sourcing, product checkout, and construction support.')
@section('canonical', route('terms'))

@section('content')
    <section class="page-hero">
        <div class="container">
            <p class="eyebrow">Terms of Service</p>
            <h1>Terms for using Leivant services.</h1>
            <p class="lead">These terms apply when clients use Leivant for construction services, provider discovery, equipment sourcing, product checkout, technical support, and site logistics.</p>
        </div>
    </section>

    <section class="section section-paper">
        <div class="container" style="max-width:900px;">
            <div class="grid">
                @foreach ([
                    ['Requests and Quotations', 'Submitted requests are reviewed by Leivant before pricing, availability, rental scheduling, payment instructions, or provider connection is finalized.'],
                    ['Products and Rentals', 'Equipment availability, condition, rental dates, payment instructions, site requirements, and final approval details must be confirmed before fulfillment.'],
                    ['Provider Connections', 'Leivant helps connect clients with relevant providers, but project owners remain responsible for approving final scope, cost, contract terms, and site access.'],
                    ['Contact', 'For terms questions, contact '.config('app.company.email').' or '.config('app.company.phone').'.'],
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

