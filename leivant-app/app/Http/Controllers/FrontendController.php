<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Region;
use App\Models\Service as LeivantService;
use App\Jobs\SendWhatsAppNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class FrontendController extends Controller
{
    private const MARKETPLACE_PUBLIC_LIMIT = 50;

    private const MARKETPLACE_PAGE_SIZE = 8;

    private ?array $publicProducts = null;

    private ?array $publicProviders = null;

    public function home(): View
    {
        return view('seo.home');
    }

    public function services(): View
    {
        return view('seo.services');
    }

    public function serviceShow(string $slug): View
    {
        $service = collect($this->serviceData())->firstWhere('slug', $slug);
        abort_unless($service, 404);

        return view('services.show', [
            'service' => $service,
            'regions' => $this->regionData(),
            'requestOptions' => $this->serviceRequestOptions(),
        ]);
    }

    public function serviceRequest(Request $request, string $slug): RedirectResponse
    {
        $service = collect($this->serviceData())->firstWhere('slug', $slug);
        abort_unless($service, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'company' => ['nullable', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:160'],
            'region' => ['required', 'string', 'max:120'],
            'site_location' => ['required', 'string', 'max:180'],
            'project_type' => ['required', 'string', 'max:120'],
            'project_stage' => ['nullable', 'string', 'max:120'],
            'budget_range' => ['nullable', 'string', 'max:120'],
            'timeline' => ['required', 'string', 'max:120'],
            'preferred_contact' => ['required', 'in:phone,whatsapp,email'],
            'message' => ['required', 'string', 'max:2500'],
        ]);

        try {
            if (Schema::hasTable('inquiries')) {
                $serviceModel = Schema::hasTable('services')
                    ? LeivantService::query()
                        ->where('slug', $slug)
                        ->when($slug === 'site-support', fn ($query) => $query->orWhere('slug', 'unskilled-labour'))
                        ->first()
                    : null;

                $inquiry = Inquiry::query()->create([
                    'service_id' => $serviceModel?->id,
                    'user_id' => auth()->id(),
                    'name' => $validated['name'],
                    'company' => $validated['company'] ?? null,
                    'phone' => $validated['phone'],
                    'email' => $validated['email'] ?? null,
                    'region' => $validated['region'],
                    'site_location' => $validated['site_location'],
                    'subject' => $service['name'].' service request - '.$validated['project_type'],
                    'project_type' => $validated['project_type'],
                    'project_stage' => $validated['project_stage'] ?? null,
                    'budget_range' => $validated['budget_range'] ?? null,
                    'timeline' => $validated['timeline'],
                    'preferred_contact' => $validated['preferred_contact'],
                    'message' => $validated['message'],
                    'status' => 'new',
                ]);

                SendWhatsAppNotification::dispatch($inquiry);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Thank you, '.$validated['name'].'. Leivant Construction Desk has received your '.$service['name'].' request and will respond through your preferred contact channel.');
    }

    public function products(Request $request): View
    {
        $all = $this->filteredProductPayload($request);
        $perPage = 24;
        $totalPages = max(1, (int) ceil(count($all) / $perPage));
        $currentPage = min($totalPages, max(1, (int) $request->input('page', 1)));

        return view('products.index', [
            'products' => array_slice($all, ($currentPage - 1) * $perPage, $perPage),
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
        ]);
    }

    public function productShow(string $slug): View
    {
        $product = collect($this->productData())->firstWhere('slug', $slug);
        abort_unless($product, 404);

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => collect($this->productData())
                ->where('category', $product['category'])
                ->where('slug', '!=', $product['slug'])
                ->take(3)
                ->values()
                ->all(),
        ]);
    }

    public function about(): View
    {
        return view('seo.about');
    }

    public function projects(): View
    {
        return view('seo.projects');
    }

    public function discovery(Request $request): View
    {
        return view('discovery.index', [
            'providers' => $this->filteredProviderPayload($request),
            'categories' => $this->providerCategoryData(),
            'regions' => $this->regionData(),
        ]);
    }

    public function contact(): View
    {
        return view('seo.contact');
    }

    public function robots()
    {
        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            'Disallow: /dashboard/',
            'Disallow: /api/',
            'Disallow: /cart',
            'Disallow: /checkout',
            'Disallow: /orders/',
            'Disallow: /login',
            'Disallow: /register',
            'Sitemap: '.route('sitemap'),
            '',
        ]);

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    public function sitemap()
    {
        $urls = collect();
        $addRoute = function (string $route, mixed $parameters = []) use (&$urls): void {
            try {
                $urls->push(route($route, $parameters));
            } catch (\Throwable $e) {
                report($e);
            }
        };

        foreach ([
            'home',
            'about.index',
            'services.index',
            'projects.index',
            'products.index',
            'solution.index',
            'discovery.index',
            'quote.index',
            'contact.index',
            'faq.index',
            'safety.index',
            'careers.index',
            'blog.index',
            'privacy',
            'terms',
        ] as $route) {
            $addRoute($route);
        }

        collect($this->serviceData())
            ->pluck('slug')
            ->filter(fn ($slug) => is_string($slug) && $slug !== '')
            ->unique()
            ->each(fn ($slug) => $addRoute('services.show', $slug));

        collect($this->productData())
            ->pluck('slug')
            ->filter(fn ($slug) => is_string($slug) && $slug !== '')
            ->unique()
            ->take(120)
            ->each(fn ($slug) => $addRoute('products.show', $slug));

        $body = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $body .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls->filter()->unique()->values() as $url) {
            $body .= "    <url>\n";
            $body .= '        <loc>'.htmlspecialchars($url, ENT_XML1 | ENT_COMPAT, 'UTF-8')."</loc>\n";
            $body .= '        <lastmod>'.now()->toAtomString()."</lastmod>\n";
            $body .= "        <changefreq>weekly</changefreq>\n";
            $body .= "        <priority>0.80</priority>\n";
            $body .= "    </url>\n";
        }

        $body .= "</urlset>\n";

        return response($body, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function siteProducts(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->query() ? $this->filteredProductPayload($request) : $this->productData(),
            'categories' => $this->categoryData(),
        ]);
    }

    public function siteProviders(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->query() ? $this->filteredProviderPayload($request) : $this->providerData(),
            'categories' => $this->providerCategoryData(),
            'regions' => $this->regionData(),
        ]);
    }

    public function contactSubmit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:160'],
            'project_type' => ['nullable', 'string', 'max:120'],
            'site_location' => ['nullable', 'string', 'max:180'],
            'timeline' => ['nullable', 'string', 'max:120'],
            'budget_range' => ['nullable', 'string', 'max:120'],
            'preferred_contact' => ['nullable', 'in:phone,whatsapp,email'],
            'project_details' => ['required', 'string', 'max:8000'],
            'message' => ['nullable', 'string', 'max:2500'],
        ]);

        try {
            if (Schema::hasTable('inquiries')) {
                $inquiry = Inquiry::query()->create([
                    'user_id' => auth()->id(),
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'email' => $validated['email'] ?? null,
                    'subject' => $validated['subject'],
                    'project_type' => $validated['project_type'] ?? null,
                    'site_location' => $validated['site_location'] ?? null,
                    'budget_range' => $validated['budget_range'] ?? null,
                    'timeline' => $validated['timeline'] ?? null,
                    'preferred_contact' => $validated['preferred_contact'] ?? null,
                    'message' => trim(collect([
                        isset($validated['project_type']) ? 'Requirement: '.$validated['project_type'] : null,
                        isset($validated['site_location']) ? 'Site location: '.$validated['site_location'] : null,
                        isset($validated['timeline']) ? 'Timeline: '.$validated['timeline'] : null,
                        isset($validated['budget_range']) ? 'Budget range: '.$validated['budget_range'] : null,
                        isset($validated['preferred_contact']) ? 'Preferred contact: '.$validated['preferred_contact'] : null,
                        $validated['project_details'] ?? null,
                        $validated['message'] ?? null,
                    ])->filter()->implode("\n\n")),
                    'status' => 'new',
                ]);

                SendWhatsAppNotification::dispatch($inquiry);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        session()->flash('success', 'Asante '.$validated['name'].'. Leivant team will respond shortly by phone, WhatsApp, or email.');

        return back();
    }

    public function websiteFeedback(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('websiteFeedback', [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'visit_reason' => ['required', 'string', 'max:120'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback_areas' => ['nullable', 'array', 'max:8'],
            'feedback_areas.*' => ['string', 'max:80'],
            'experience' => ['required', 'string', 'max:2500'],
            'improve' => ['nullable', 'string', 'max:2500'],
            'add' => ['nullable', 'string', 'max:2500'],
            'remove' => ['nullable', 'string', 'max:2500'],
            'contact_permission' => ['nullable', 'boolean'],
            'page_url' => ['nullable', 'url', 'max:500'],
            'website' => ['nullable', 'size:0'],
        ]);

        $recipient = 'admin@leivantconstruction.com';
        $subject = 'Website feedback from '.$validated['name'];
        $feedbackAreas = collect($validated['feedback_areas'] ?? [])->filter()->values()->implode(', ');
        $body = trim(collect([
            'New website feedback was submitted from leivantconstruction.com.',
            'Name: '.$validated['name'],
            'Email: '.($validated['email'] ?? 'Not provided'),
            'Phone: '.($validated['phone'] ?? 'Not provided'),
            'Visitor goal: '.$validated['visit_reason'],
            'Rating: '.$validated['rating'].' / 5',
            'Feedback areas: '.($feedbackAreas ?: 'Not selected'),
            'Admin may contact visitor: '.(($validated['contact_permission'] ?? false) ? 'Yes' : 'No'),
            'Page reviewed: '.($validated['page_url'] ?? 'Not provided'),
            'Experience:',
            $validated['experience'],
            filled($validated['improve'] ?? null) ? "What to improve:\n".$validated['improve'] : null,
            filled($validated['add'] ?? null) ? "What to add:\n".$validated['add'] : null,
            filled($validated['remove'] ?? null) ? "What to remove:\n".$validated['remove'] : null,
        ])->filter()->implode("\n\n"));

        try {
            Mail::raw($body, function ($message) use ($recipient, $subject, $validated) {
                $message->to($recipient)
                    ->subject($subject);

                if (filled($validated['email'] ?? null)) {
                    $message->replyTo($validated['email'], $validated['name']);
                }
            });
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', 'Sorry, the feedback could not be sent right now. Please try again or contact Leivant directly.');
        }

        try {
            if (Schema::hasTable('inquiries')) {
                Inquiry::query()->create([
                    'user_id' => auth()->id(),
                    'name' => $validated['name'],
                    'phone' => $validated['phone'] ?? 'Not provided',
                    'email' => $validated['email'] ?? null,
                    'subject' => 'Website feedback',
                    'project_type' => 'Website feedback',
                    'preferred_contact' => filled($validated['email'] ?? null) ? 'email' : null,
                    'message' => $body,
                    'status' => 'new',
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return back()->with('success', 'Thank you, '.$validated['name'].'. Your website feedback has been sent to Leivant admin.');
    }

    private function filteredProductPayload(Request $request, ?\Illuminate\Support\Collection $sourceProducts = null): array
    {
        $products = $sourceProducts ? $sourceProducts->values() : collect($this->productData());

        if ($request->filled('search')) {
            $search = $request->string('search')->lower()->toString();
            $products = $products->filter(fn ($product) => str($product['name'].' '.$product['provider'].' '.$product['category'])->lower()->contains($search));
        }

        if ($request->filled('category')) {
            $products = $products->where('category', $request->string('category')->toString());
        }

        if ($request->filled('mode')) {
            $mode = $request->string('mode')->toString();
            $products = $products->filter(fn ($product) => match ($mode) {
                'buy' => $product['is_for_sale'] ?? false,
                'rent' => $this->productRentalEligible($product),
                default => true,
            });
        }

        if ($request->filled('min_price')) {
            $products = $products->filter(fn ($product) => $this->productFilterPrice($product, $request->string('mode')->toString()) >= (int) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $products = $products->filter(fn ($product) => $this->productFilterPrice($product, $request->string('mode')->toString()) <= (int) $request->input('max_price'));
        }

        return $products->values()->all();
    }

    private function filteredProviderPayload(Request $request): array
    {
        $providers = collect($this->providerData());

        if ($request->filled('search')) {
            $search = $request->string('search')->lower()->toString();
            $providers = $providers->filter(fn ($provider) => str($provider['name'].' '.$provider['category'].' '.$provider['region'].' '.($provider['location'] ?? '').' '.$provider['summary'])->lower()->contains($search));
        }

        if ($request->filled('category')) {
            $providers = $providers->where('category', $request->string('category')->toString());
        }

        if ($request->filled('region')) {
            $providers = $providers->where('region', $request->string('region')->toString());
        }

        return $providers->values()->all();
    }

    private function serviceData(): array
    {
        return Cache::remember('leivant.public.services.v4', now()->addMinutes(15), function (): array {
            try {
                if (Schema::hasTable('services') && LeivantService::query()->exists()) {
                    return LeivantService::query()
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->get()
                        ->map(function (LeivantService $service) {
                            $publicSlug = $this->publicServiceSlug($service->slug);
                            $polished = collect($this->fallbackServices())->firstWhere('slug', $publicSlug);

                            return array_merge($polished ?? [], [
                                'slug' => $publicSlug,
                                'name' => $polished['name'] ?? $service->name,
                                'provider_category' => $polished['provider_category'] ?? match ($publicSlug) {
                                    'architecture' => 'Architects',
                                    'engineering' => 'Engineers',
                                    'construction' => 'Contractors',
                                    'skilled-labour' => 'Skilled Labour',
                                    default => 'Providers',
                                },
                                'summary' => $polished['summary'] ?? $service->summary,
                                'description' => $polished['description'] ?? $service->description,
                                'features' => $polished['features'] ?? ['Scope review', 'Provider matching', 'Quote coordination', 'Site follow-up'],
                                'accent' => $polished['accent'] ?? match ($publicSlug) {
                                    'architecture' => 'bg-vant-blue',
                                    'engineering' => 'bg-vant-green',
                                    'construction' => 'bg-vant-orange',
                                    'skilled-labour' => 'bg-vant-gold',
                                    default => 'bg-vant-concrete',
                                },
                                'image' => $service->image_path ? asset('storage/'.$service->image_path) : $this->serviceFallbackImage($service->slug),
                            ]);
                        })
                        ->all();
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return $this->fallbackServices();
        });
    }


    private function homeServiceData(): array
    {
        return collect(array_slice($this->serviceData(), 0, 5))
            ->push([
                'slug' => 'architecture',
                'name' => '3D Building Design & Architectural Drawings',
                'provider_category' => 'Design Service',
                'summary' => '3D building concepts, architectural drawings, layouts, and permit-ready documentation that help clients understand the project before construction starts.',
                'features' => ['3D concepts', 'Drawings', 'Layouts'],
                'accent' => 'bg-vant-blue',
                'image' => $this->siteImage('architecture-drawings'),
            ])
            ->all();
    }

    private function productData(): array
    {
        if ($this->publicProducts !== null) {
            return $this->publicProducts;
        }

        return $this->publicProducts = Cache::remember('leivant.public.products.v8', now()->addMinutes(10), function (): array {
        try {
            if (Schema::hasTable('products') && Product::query()->exists()) {
                $equipmentCategories = $this->fallbackCategories();
                $categoryOrder = array_flip($this->marketplaceCategoryPriority($equipmentCategories));

                $databaseProducts = Product::query()
                    ->with(['category', 'provider.region', 'images'])
                    ->where('is_active', true)
                    ->whereHas('category', fn ($query) => $query->whereIn('name', $equipmentCategories))
                    ->get()
                    ->map(function (Product $product) {
                        $images = $product->images
                            ->map(fn ($image) => $this->versionedStorageAsset($image->path))
                            ->values()
                            ->all();

                        $gallery = $this->productGallery($product->slug, $product->name, $product->category?->name ?? 'Construction Equipment', $images);

                        return [
                            'slug' => $product->slug,
                            'name' => $product->name,
                            'category' => $product->category?->name ?? 'Construction Materials',
                            'provider' => $product->provider?->name ?? 'Leivant Provider',
                            'region' => $product->region ?: ($product->provider?->region?->name ?? 'Dar es Salaam'),
                            'price' => $product->price,
                            'unit' => $product->unit,
                            'stock' => $product->stock,
                            'is_for_sale' => $product->is_for_sale,
                            'is_for_rent' => $product->is_for_rent,
                            'rental_price_per_day' => $product->rental_price_per_day,
                            'availability_status' => $product->availability_status ?: 'available',
                            'equipment_condition' => $product->equipment_condition ?: 'Good working condition',
                            'rating' => 4.8,
                            'summary' => $this->productSummary($product->name, $product->description),
                            'image' => $gallery[0],
                            'gallery' => $gallery,
                        ];
                    });

                $databaseSlugs = $databaseProducts->pluck('slug')->all();
                $catalogProducts = collect($this->fallbackProducts())
                    ->reject(fn (array $product) => in_array($product['slug'], $databaseSlugs, true));

                return $databaseProducts
                    ->concat($catalogProducts)
                    ->map(fn (array $product) => $this->normalizeMarketplaceProduct($product))
                    ->sortBy(fn (array $product) => sprintf('%02d-%s', $categoryOrder[$product['category']] ?? 99, $product['name']))
                    ->values()
                    ->all();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        $categoryOrder = array_flip($this->marketplaceCategoryPriority($this->fallbackCategories()));

        return collect($this->fallbackProducts())
            ->map(fn (array $product) => $this->normalizeMarketplaceProduct($product))
            ->sortBy(fn (array $product) => sprintf('%02d-%s', $categoryOrder[$product['category']] ?? 99, $product['name']))
            ->values()
            ->all();
        });
    }

    private function marketplaceDisplayProducts(\Illuminate\Support\Collection $products): array
    {
        $products = $products->unique('slug')->values();
        $rentals = $products->filter(fn (array $product) => $this->productRentalEligible($product))->take(18);
        $sales = $products->reject(fn (array $product) => $this->productRentalEligible($product))->take(self::MARKETPLACE_PUBLIC_LIMIT - $rentals->count());
        $selected = $rentals->concat($sales);

        if ($selected->count() < self::MARKETPLACE_PUBLIC_LIMIT) {
            $selectedSlugs = $selected->pluck('slug')->all();
            $selected = $selected->concat(
                $products
                    ->reject(fn (array $product) => in_array($product['slug'], $selectedSlugs, true))
                    ->take(self::MARKETPLACE_PUBLIC_LIMIT - $selected->count())
            );
        }

        return $selected
            ->sortBy(fn (array $product) => sprintf('%d-%s', $this->productRentalEligible($product) ? 0 : 1, $product['name']))
            ->values()
            ->all();
    }

    private function providerData(): array
    {
        if ($this->publicProviders !== null) {
            return $this->publicProviders;
        }

        return $this->publicProviders = Cache::remember('leivant.public.providers.v4', now()->addMinutes(10), function (): array {
        try {
            if (Schema::hasTable('providers') && Provider::query()->exists()) {
                return Provider::query()
                    ->with('region')
                    ->withCount('products')
                    ->where('is_active', true)
                    ->where('slug', '!=', 'vant-equipment-desk')
                    ->orderByDesc('is_verified')
                    ->orderByDesc('rating')
                    ->get()
                    ->map(fn (Provider $provider) => [
                        'name' => $provider->name,
                        'slug' => $provider->slug,
                        'category' => $provider->category,
                        'region' => $provider->region?->name ?? $provider->location ?? 'Dar es Salaam',
                        'location' => $provider->location,
                        'rating' => $provider->rating,
                        'jobs' => (int) $provider->products_count + 25,
                        'summary' => $provider->description ?? 'Verified provider available through Leivant Construction Solutions.',
                        'is_verified' => $provider->is_verified,
                        'response' => $provider->is_verified ? 'Reviewed provider' : 'Pending review',
                    ])
                    ->all();
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $this->fallbackProviders();
        });
    }

    private function categoryData(): array
    {
        return Cache::remember('leivant.public.categories.v3', now()->addMinutes(30), function (): array {
            try {
                if (Schema::hasTable('categories') && Category::query()->exists()) {
                    $equipmentCategories = $this->fallbackCategories();

                    return Category::query()
                        ->where('is_active', true)
                        ->whereIn('name', $equipmentCategories)
                        ->orderByRaw('case name ' . collect($equipmentCategories)->map(fn ($name, $index) => "when '{$name}' then {$index}")->implode(' ') . ' end')
                        ->pluck('name')
                        ->all();
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return $this->fallbackCategories();
        });
    }


    private function providerCategoryData(): array
    {
        return Cache::remember('leivant.public.provider_categories.v3', now()->addMinutes(30), function (): array {
            try {
                if (Schema::hasTable('providers') && Provider::query()->exists()) {
                    return collect($this->fallbackProviderCategories())
                        ->merge(Provider::query()->where('is_active', true)->where('slug', '!=', 'vant-equipment-desk')->distinct()->orderBy('category')->pluck('category'))
                        ->unique()
                        ->values()
                        ->all();
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return $this->fallbackProviderCategories();
        });
    }


    private function regionData(): array
    {
        return Cache::remember('leivant.public.regions.v3', now()->addMinutes(30), function (): array {
            try {
                if (Schema::hasTable('regions') && Region::query()->exists()) {
                    return Region::query()->orderBy('name')->pluck('name')->all();
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return $this->fallbackRegions();
        });
    }


    private function serviceFallbackImage(string $slug): string
    {
        return collect($this->fallbackServices())->firstWhere('slug', $this->publicServiceSlug($slug))['image']
            ?? $this->siteImage('hero-construction-site');
    }

    private function publicServiceSlug(string $slug): string
    {
        return $slug === 'unskilled-labour' ? 'site-support' : $slug;
    }

    private function productFallbackImage(string $slug): string
    {
        return $this->versionedPublicAsset('images/tools/tool-placeholder.svg');
    }

    private function productSummary(string $name, ?string $description): string
    {
        $summary = trim((string) $description);
        $summary = preg_replace('/\s*This item is listed in the Leivant tools and equipment catalog.*$/', '', $summary) ?: $summary;
        $summary = preg_replace('/\s*Leivant confirms stock, site access, payment status, and rental or purchase details before approval\.$/', '', $summary) ?: $summary;
        $summary = preg_replace('/\s*Leivant confirms stock, site access, payment instructions, and rental or purchase details before approval\.$/', '', $summary) ?: $summary;
        $summary = preg_replace('/\s*Leivant confirms stock, site access, delivery timing, and rental or purchase details before dispatch\.$/', '', $summary) ?: $summary;
        $summary = preg_replace('/\s*Leivant Equipment Desk\s*-\s*final stock and delivery reviewed by Leivant\.?$/i', '', $summary) ?: $summary;

        return $this->productUseDescription($name, $summary);
    }

    private function productRentalEligible(array $product): bool
    {
        return $this->isMarketplaceRentalOnly($product['category'] ?? null, $product['name'] ?? null)
            && ($product['is_for_rent'] ?? false)
            && ! empty($product['rental_price_per_day']);
    }

    private function normalizeMarketplaceProduct(array $product): array
    {
        $rentalOnly = $this->isMarketplaceRentalOnly($product['category'] ?? null, $product['name'] ?? null);

        $product['is_for_sale'] = ! $rentalOnly;
        $product['is_for_rent'] = $rentalOnly;

        if (! $rentalOnly) {
            $product['rental_price_per_day'] = null;
        } elseif (empty($product['rental_price_per_day'])) {
            $product['rental_price_per_day'] = max(25000, (int) ceil(((int) ($product['price'] ?? 250000)) * 0.04));
        }

        return $product;
    }

    private function isMarketplaceRentalOnly(?string $category, ?string $name): bool
    {
        $category = trim((string) $category);
        $name = str($name ?? '')->lower()->toString();

        if (in_array($category, [
            'Earthmoving & Heavy Machinery',
            'Foundation & Drilling Equipment',
            'Dewatering Equipment',
            'Compaction & Generators',
            'Lifting & Access',
        ], true)) {
            return true;
        }

        foreach ([
            'excavator',
            'bulldozer',
            'loader',
            'grader',
            'dump truck',
            'hauler',
            'trencher',
            'compactor',
            'roller',
            'concrete mixer',
            'mixer truck',
            'concrete pump',
            'batch plant',
            'cutting machine',
            'concrete buggy',
            'concrete hopper',
            'power trowel',
            'concrete vibrator',
            'tower crane',
            'mobile crane',
            'chain hoist',
            'forklift',
            'manlift',
            'boom lift',
            'scissor lift',
            'scaffolding',
            'drill rig',
            'pile',
            'rock breaker',
            'foundation auger',
            'generator',
            'light tower',
            'submersible pump',
            'wellpoint',
            'centrifugal pump',
        ] as $keyword) {
            if (str_contains($name, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function productUseDescription(string $name, ?string $useOrDescription = null): string
    {
        $descriptions = [
            'Bull Float' => 'Smooths and levels large poured concrete slabs before final finishing - essential for floor pours and slab work.',
            'Caulk Gun' => 'Applies sealant to joints, window frames, and wall penetrations to prevent water ingress in finished buildings.',
            'Chisel Set' => 'Cuts, shapes, and carves concrete, brick, stone, and timber for construction and finishing work.',
            'Claw Hammer' => 'Drives and removes nails and handles light demolition, formwork, and general carpentry tasks on site.',
            'Concrete Float' => 'Smooths freshly poured concrete to an even surface ready for tiling, screed, or final finishing.',
            'Digging Bar' => 'Breaks hard compacted ground, rocky soil, and trench obstructions during foundation and excavation work.',
            'Hacksaw' => 'Cuts steel pipes, rods, and bolts used in plumbing, electrical, and structural site work.',
            'Hand Saw' => 'Manually cuts timber, boards, and light structural sections without power tools.',
            'Hawk Board' => 'Holds plaster or mortar for the plasterer while applying and finishing wall and ceiling surfaces.',
            'Heavy Duty Wheelbarrow' => 'Moves concrete, sand, blocks, aggregate, and rubble efficiently across construction sites.',
            'Masonry Trowel' => 'Applies, spreads, and shapes mortar during brick and block laying.',
            'Pickaxe' => 'Breaks and loosens compacted soil, clay, and light rock during excavation and trench preparation.',
            'Plastering Trowel' => 'Applies and smooths plaster on walls and ceilings to a professional finish.',
            'Pliers Set' => 'Grips, bends, and cuts wire and pipe for electrical, plumbing, and general site work.',
            'Screwdriver Set' => 'Drives flathead and Phillips screws for fixture installation and general site assembly.',
            'Shovel / Spade' => 'Digs, backfills, moves sand, and cleans up sites during all phases of construction.',
            'Sledgehammer' => 'Breaks concrete slabs, masonry walls, and large rock obstructions during demolition work.',
            'Spirit Level' => 'Checks horizontal and vertical alignment of walls, frames, beams, and fixings during installation.',
        ];

        if (isset($descriptions[$name])) {
            return $descriptions[$name];
        }

        $clean = trim((string) $useOrDescription);
        $clean = preg_replace('/\s*Leivant confirms.*$/i', '', $clean) ?: $clean;
        $clean = trim(preg_replace('/\s+/', ' ', $clean), " \t\n\r\0\x0B.-");

        if ($clean === '') {
            return $name.' for construction site work, material handling, installation, or finishing tasks.';
        }

        if (str_starts_with(strtolower($clean), strtolower($name).' for ')) {
            $clean = trim(substr($clean, strlen($name) + 5));
        }

        if (str_contains($clean, '.')) {
            $clean = trim(Str::before($clean, '.'));
        }

        $sentence = ucfirst($clean);

        if (! str($sentence)->endsWith(['.', '!', '?'])) {
            $sentence .= '.';
        }

        return $sentence;
    }

    private function siteImage(string $name): string
    {
        return asset("images/site/{$name}.jpg");
    }

    private function versionedPublicAsset(string $path): string
    {
        $url = asset($path);
        $fullPath = public_path($path);

        return is_file($fullPath) ? $url.'?v='.filemtime($fullPath) : $url;
    }

    private function versionedStorageAsset(string $path): string
    {
        $url = asset('storage/'.$path);
        $fullPath = storage_path('app/public/'.$path);

        return is_file($fullPath) ? $url.'?v='.filemtime($fullPath) : $url;
    }

    private function toolImage(string $slug): string
    {
        if ($catalogImage = $this->catalogToolImage($slug)) {
            return $catalogImage;
        }

        return $this->versionedPublicAsset('images/tools/tool-placeholder.svg');
    }

    private function catalogToolImage(string $slug): ?string
    {
        foreach (['webp', 'jpg', 'jpeg', 'png', 'svg'] as $extension) {
            if (is_file(public_path("images/catalog-tools/{$slug}.{$extension}"))) {
                return $this->versionedPublicAsset("images/catalog-tools/{$slug}.{$extension}");
            }
        }

        foreach (['webp', 'jpg', 'jpeg', 'png', 'svg'] as $extension) {
            if (in_array($slug, $this->legacyToolImageSlugs(), true) && is_file(public_path("images/tools/{$slug}.{$extension}"))) {
                return $this->versionedPublicAsset("images/tools/{$slug}.{$extension}");
            }
        }

        return null;
    }

    private function legacyToolImageSlugs(): array
    {
        return [];
    }

    private function productGallery(string $slug, string $name, string $category, array $images): array
    {
        $catalogImage = $this->catalogToolImage($slug);

        if ($catalogImage && $this->catalogToolImageIsReal($slug)) {
            return collect([$catalogImage])
                ->merge($images)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        if ($catalogImage) {
            return collect([$catalogImage])
                ->merge($images)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        return collect($images)
            ->filter()
            ->unique()
            ->values()
            ->all() ?: [$this->productFallbackImage($slug)];
    }

    private function catalogToolImageIsReal(string $slug): bool
    {
        $source = strtolower((string) ($this->catalogImageSources()[$slug]['source'] ?? ''));

        return $source !== '' && ! str_contains($source, 'generated') && ! str_contains($source, 'placeholder');
    }

    private function catalogImageSources(): array
    {
        static $sources = null;

        if ($sources !== null) {
            return $sources;
        }

        $path = public_path('images/catalog-tools/sources.json');
        $sources = is_file($path)
            ? json_decode((string) file_get_contents($path), true) ?: []
            : [];

        return $sources;
    }

    private function productFilterPrice(array $product, string $mode = ''): int
    {
        if ($mode === 'rent') {
            return (int) ($product['rental_price_per_day'] ?? $product['price']);
        }

        if ($mode === 'buy') {
            return (int) $product['price'];
        }

        $prices = array_filter([
            $product['is_for_sale'] ?? false ? (int) $product['price'] : null,
            $product['is_for_rent'] ?? false ? (int) ($product['rental_price_per_day'] ?? 0) : null,
        ]);

        return $prices ? min($prices) : (int) $product['price'];
    }

    private function serviceRequestOptions(): array
    {
        return [
            'budget_ranges' => [
                'Below TZS 5 million',
                'TZS 5 million - 20 million',
                'TZS 20 million - 75 million',
                'TZS 75 million - 250 million',
                'Above TZS 250 million',
                'To be confirmed after scope review',
            ],
            'timelines' => [
                'Urgent - within 7 days',
                'Within 2 to 4 weeks',
                'Within 1 to 3 months',
                'More than 3 months',
                'Planning stage only',
            ],
            'stages' => [
                'Idea / early planning',
                'Land or site already secured',
                'Drawings or BOQ available',
                'Construction already started',
                'Renovation or corrective works',
            ],
        ];
    }

    private function fallbackServices(): array
    {
        return [
            [
                'slug' => 'architecture',
                'name' => 'Architecture',
                'provider_category' => 'Design Service',
                'summary' => 'Concept design, working drawings, space planning, and permit-ready documentation prepared around site conditions, budget, approval needs, and buildability.',
                'description' => 'Leivant supports architectural planning for homes, apartments, commercial buildings, mixed-use developments, lodges, offices, and renovation work. The service focuses on clear layouts, practical construction decisions, and documentation that helps clients move from idea to approval and site execution.',
                'features' => ['Concept drawings', 'Permit-ready plans', 'Layout planning', 'BOQ support'],
                'deliverables' => ['Concept sketches and layout direction', 'Floor plans, elevations, and roof layouts', 'Permit-ready drawing coordination', 'Space planning for residential and commercial use', 'Design revisions aligned to budget and site realities'],
                'best_for' => ['New residential homes and rental units', 'Commercial spaces, offices, and shops', 'Apartment and mixed-use developments', 'Renovations, extensions, and redesigns', 'International clients planning projects in Tanzania'],
                'process' => ['Review site location, plot details, and client objectives', 'Define space needs, budget expectations, and approval requirements', 'Prepare concept direction and drawing scope', 'Refine plans for construction practicality', 'Support handover to engineering and construction planning'],
                'required_details' => ['Plot or site location', 'Expected building use', 'Approximate number of rooms or floors', 'Budget range and timeline', 'Any existing sketches, title details, or survey information'],
                'project_types' => ['New home design', 'Apartment or rental unit design', 'Commercial building design', 'Renovation or extension design', 'Layout review and redesign'],
                'accent' => 'bg-vant-blue',
                'image' => $this->siteImage('architecture-drawings'),
            ],
            [
                'slug' => 'engineering',
                'name' => 'Engineering',
                'provider_category' => 'Technical Service',
                'summary' => 'Structural review, civil works advice, foundation guidance, drainage planning, MEP coordination, and site inspections for safer project decisions.',
                'description' => 'Leivant engineering support helps clients reduce construction risk before and during site work. The service covers technical review, foundation and structural coordination, drainage and civil considerations, building services coordination, and professional inspection support.',
                'features' => ['Structural review', 'Foundation guidance', 'Site inspection', 'MEP coordination'],
                'deliverables' => ['Structural and foundation review', 'Civil works and drainage guidance', 'MEP coordination notes for electrical and plumbing planning', 'Site inspection and technical reporting', 'Construction method recommendations'],
                'best_for' => ['Clients with drawings needing technical review', 'Projects with foundation or drainage concerns', 'Buildings that require site inspection', 'Commercial and multi-storey developments', 'Renovation projects with structural uncertainty'],
                'process' => ['Collect drawings, site photos, and technical concerns', 'Review soil, foundation, structure, and service requirements', 'Advise on engineering scope and inspection needs', 'Coordinate technical recommendations with the project team', 'Support follow-up during construction milestones'],
                'required_details' => ['Existing drawings or sketches if available', 'Site location and soil or drainage concerns', 'Number of floors or structural type', 'Current project stage', 'Specific technical issue to be reviewed'],
                'project_types' => ['Structural review', 'Foundation advice', 'Drainage and civil works', 'MEP coordination', 'Site inspection'],
                'accent' => 'bg-vant-green',
                'image' => $this->siteImage('engineering-review'),
            ],
            [
                'slug' => 'construction',
                'name' => 'Construction',
                'provider_category' => 'Site Delivery',
                'summary' => 'Managed construction execution for foundations, frames, roofing, finishing, renovations, supervision, and coordinated site progress.',
                'description' => 'Leivant construction service is built for clients who need organized site delivery with clear scope, supervision, material coordination, labour planning, and progress control. The service supports new builds, renovations, finishing works, and project completion support.',
                'features' => ['New builds', 'Renovations', 'Roofing and finishing', 'Site supervision'],
                'deliverables' => ['Construction scope review and work planning', 'Foundation, walling, roofing, and finishing coordination', 'Labour and material planning support', 'Site supervision and progress checks', 'Renovation and corrective works coordination'],
                'best_for' => ['Residential houses and apartments', 'Commercial fit-outs and small business buildings', 'Renovations, extensions, and finishing works', 'Clients needing construction supervision', 'Sites needing temporary construction tools or equipment'],
                'process' => ['Review drawings, site condition, and desired scope', 'Define work stages, budget range, and timeline', 'Plan labour, materials, and supervision needs', 'Coordinate execution with progress reporting', 'Close out completed work with client review'],
                'required_details' => ['Project drawings or scope summary', 'Site location and access details', 'Current construction stage', 'Budget range and preferred timeline', 'Any urgent quality, delay, or contractor issues'],
                'project_types' => ['New building construction', 'Renovation or extension', 'Finishing works', 'Roofing works', 'Site supervision'],
                'accent' => 'bg-vant-orange',
                'image' => $this->siteImage('hero-construction-site'),
            ],
            [
                'slug' => 'skilled-labour',
                'name' => 'Skilled Labour',
                'provider_category' => 'Trade Teams',
                'summary' => 'Planned access to skilled trades including masons, steel fixers, carpenters, plumbers, electricians, painters, gypsum installers, and tile setters.',
                'description' => 'Leivant helps organize skilled labour for defined construction tasks where workmanship, availability, and scope clarity matter. The service supports short assignments, milestone work, and longer site programmes that require multiple trades.',
                'features' => ['Masonry', 'Steel fixing', 'Plumbing and electrical', 'Tiles, gypsum, painting'],
                'deliverables' => ['Trade requirement review', 'Skill-specific labour planning', 'Daily, weekly, or milestone-based labour coordination', 'Scope and rate clarity before deployment', 'Site follow-up for work quality and availability'],
                'best_for' => ['Masonry, blockwork, plastering, and concrete tasks', 'Steel fixing and formwork preparation', 'Electrical and plumbing rough-ins', 'Tiling, painting, gypsum, and finishing work', 'Sites that need multiple trades coordinated'],
                'process' => ['Define the trade task and expected output', 'Confirm site location, tools, and material readiness', 'Estimate team size and deployment period', 'Coordinate labour availability and start date', 'Follow up on progress and scope completion'],
                'required_details' => ['Trade type required', 'Estimated work quantity or site photos', 'Tools and materials already available', 'Start date and expected duration', 'Site supervisor or contact person'],
                'project_types' => ['Masonry and plastering', 'Steel fixing and formwork', 'Carpentry and roofing support', 'Electrical or plumbing works', 'Finishing trades'],
                'accent' => 'bg-vant-gold',
                'image' => $this->siteImage('skilled-labour'),
            ],
            [
                'slug' => 'site-support',
                'name' => 'Site Support',
                'provider_category' => 'Site Support',
                'summary' => 'Reliable site support for preparation, loading, trenching, mixing, cleaning, material movement, excavation assistance, and supervised general tasks.',
                'description' => 'Leivant manages practical site support for projects that need disciplined manpower under supervision. The service is useful for preparation work, material movement, cleaning, loading, trenching, and other high-volume site tasks across reachable locations.',
                'features' => ['Material handling', 'Site cleaning', 'Concrete mixing support', 'Excavation assistance'],
                'deliverables' => ['Labour quantity planning', 'Site task clarification before deployment', 'Daily or short-term manpower coordination', 'Support for loading, cleaning, trenching, and movement tasks', 'Coordination with site supervisor for attendance and discipline'],
                'best_for' => ['Site clearing and preparation', 'Material loading and movement', 'Trenching and excavation support', 'Concrete mixing and general assistance', 'Projects needing temporary labour quickly'],
                'process' => ['Clarify the task and number of people needed', 'Confirm site location, start time, and supervisor contact', 'Review safety and task conditions', 'Coordinate labour arrival and attendance', 'Follow up after the work period'],
                'required_details' => ['Number of labourers needed', 'Task description and site photos if available', 'Start date, working hours, and duration', 'Site location and access instructions', 'Supervisor name and phone number'],
                'project_types' => ['Site preparation', 'Material handling', 'Trenching or excavation support', 'Concrete mixing support', 'Cleaning and general site work'],
                'accent' => 'bg-vant-concrete',
                'image' => $this->siteImage('site-support'),
            ],
        ];
    }

    private function fallbackProducts(): array
    {
        $catalogProducts = collect(config('vant_products.products', []));

        if ($catalogProducts->isNotEmpty()) {
            $regions = $this->fallbackRegions();

            return $catalogProducts
                ->map(function (array $product, int $index) use ($regions): array {
                    $gallery = $this->productGallery($product['slug'], $product['name'], $product['category'], []);

                    return [
                        'slug' => $product['slug'],
                        'name' => $product['name'],
                        'category' => $product['category'],
                        'provider' => 'Leivant Equipment Desk',
                        'region' => $regions[$index % count($regions)] ?? 'Dar es Salaam',
                        'price' => $product['price'],
                        'unit' => $product['unit'],
                        'stock' => $product['stock'],
                        'is_for_sale' => $product['is_for_sale'],
                        'is_for_rent' => $product['is_for_rent'],
                        'rental_price_per_day' => $product['rental_price_per_day'],
                        'availability_status' => $product['availability_status'],
                        'equipment_condition' => $product['equipment_condition'],
                        'rating' => 4.7,
                        'summary' => $this->productUseDescription($product['name'], $product['use'] ?? null),
                        'image' => $gallery[0],
                        'gallery' => $gallery,
                    ];
                })
                ->all();
        }

        return [
            [
                'slug' => 'portable-concrete-mixer-350l',
                'name' => 'Portable Concrete Mixer 350L',
                'category' => 'Concrete Equipment',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Dar es Salaam',
                'price' => 1850000,
                'unit' => 'unit',
                'stock' => 6,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 85000,
                'availability_status' => 'available',
                'equipment_condition' => 'Site-tested',
                'rating' => 4.8,
                'summary' => 'Company-owned concrete mixer for slab, beam, column, and block production work where consistent mixing is needed.',
                'image' => $this->toolImage('portable-concrete-mixer-350l'),
                'gallery' => [$this->toolImage('portable-concrete-mixer-350l')],
            ],
            [
                'slug' => 'plate-compactor-honda-engine',
                'name' => 'Plate Compactor Honda Engine',
                'category' => 'Compaction & Generators',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Mwanza',
                'price' => 2450000,
                'unit' => 'unit',
                'stock' => 4,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 120000,
                'availability_status' => 'limited',
                'equipment_condition' => 'Good working condition',
                'rating' => 4.7,
                'summary' => 'Reliable compactor for base preparation, paving works, trenches, walkways, and small road sections.',
                'image' => $this->toolImage('plate-compactor-honda-engine'),
                'gallery' => [$this->toolImage('plate-compactor-honda-engine')],
            ],
            [
                'slug' => 'concrete-vibrator-38mm',
                'name' => 'Concrete Vibrator 38mm',
                'category' => 'Concrete Equipment',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Arusha',
                'price' => 690000,
                'unit' => 'unit',
                'stock' => 9,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 45000,
                'availability_status' => 'available',
                'equipment_condition' => 'Good working condition',
                'rating' => 4.6,
                'summary' => 'Concrete vibrator for columns, beams, slabs, and foundations where proper compaction matters.',
                'image' => $this->toolImage('concrete-vibrator-38mm'),
                'gallery' => [$this->toolImage('concrete-vibrator-38mm')],
            ],
            [
                'slug' => 'mobile-scaffolding-set',
                'name' => 'Mobile Scaffolding Set',
                'category' => 'Lifting & Access',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Dodoma',
                'price' => 1350000,
                'unit' => 'set',
                'stock' => 12,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 65000,
                'availability_status' => 'available',
                'equipment_condition' => 'Site-tested',
                'rating' => 4.9,
                'summary' => 'Access scaffolding for plastering, painting, roof work, facade repairs, and elevated site tasks.',
                'image' => $this->toolImage('mobile-scaffolding-set'),
                'gallery' => [$this->toolImage('mobile-scaffolding-set')],
            ],
            [
                'slug' => 'site-generator-5kva',
                'name' => 'Site Generator 5kVA',
                'category' => 'Compaction & Generators',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Morogoro',
                'price' => 2100000,
                'unit' => 'unit',
                'stock' => 5,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 95000,
                'availability_status' => 'available',
                'equipment_condition' => 'Good working condition',
                'rating' => 4.5,
                'summary' => 'Power backup for sites, finishing teams, drilling, lighting, welding support, and remote works.',
                'image' => $this->toolImage('site-generator-5kva'),
                'gallery' => [$this->toolImage('site-generator-5kva')],
            ],
            [
                'slug' => 'professional-angle-grinder',
                'name' => 'Professional Angle Grinder',
                'category' => 'Power Tools',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Dar es Salaam',
                'price' => 180000,
                'unit' => 'item',
                'stock' => 18,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 18000,
                'availability_status' => 'available',
                'equipment_condition' => 'New',
                'rating' => 4.7,
                'summary' => 'Durable grinder for cutting, polishing, metal preparation, tile trimming, and site maintenance tasks.',
                'image' => $this->toolImage('professional-angle-grinder'),
                'gallery' => [$this->toolImage('professional-angle-grinder')],
            ],
            [
                'slug' => 'heavy-duty-wheelbarrow',
                'name' => 'Heavy Duty Wheelbarrow',
                'category' => 'Hand Tools',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Dar es Salaam',
                'price' => 145000,
                'unit' => 'item',
                'stock' => 25,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 12000,
                'availability_status' => 'available',
                'equipment_condition' => 'New',
                'rating' => 4.7,
                'summary' => 'Heavy duty wheelbarrow for concrete, sand, blocks, aggregate, rubble, and general site movement.',
                'image' => $this->toolImage('heavy-duty-wheelbarrow'),
                'gallery' => [$this->toolImage('heavy-duty-wheelbarrow')],
            ],
            [
                'slug' => 'extension-ladder-24ft',
                'name' => 'Extension Ladder 24ft',
                'category' => 'Lifting & Access',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Arusha',
                'price' => 360000,
                'unit' => 'item',
                'stock' => 10,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 25000,
                'availability_status' => 'limited',
                'equipment_condition' => 'Good working condition',
                'rating' => 4.7,
                'summary' => 'Extension ladder for roofing, ceiling, painting, electrical, gutter, and maintenance works.',
                'image' => $this->toolImage('extension-ladder-24ft'),
                'gallery' => [$this->toolImage('extension-ladder-24ft')],
            ],
            [
                'slug' => 'rotary-hammer-drill',
                'name' => 'Rotary Hammer Drill',
                'category' => 'Power Tools',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Dar es Salaam',
                'price' => 420000,
                'unit' => 'unit',
                'stock' => 8,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 30000,
                'availability_status' => 'available',
                'equipment_condition' => 'Good working condition',
                'rating' => 4.7,
                'summary' => 'Rotary hammer drill for concrete drilling, anchor fixing, light demolition, and renovation work.',
                'image' => $this->toolImage('rotary-hammer-drill'),
                'gallery' => [$this->toolImage('rotary-hammer-drill')],
            ],
            [
                'slug' => 'site-safety-gear-kit',
                'name' => 'Site Safety Gear Kit',
                'category' => 'Site Safety',
                'provider' => 'Leivant Equipment Desk',
                'region' => 'Dar es Salaam',
                'price' => 95000,
                'unit' => 'kit',
                'stock' => 40,
                'is_for_sale' => true,
                'is_for_rent' => true,
                'rental_price_per_day' => 8000,
                'availability_status' => 'available',
                'equipment_condition' => 'New',
                'rating' => 4.6,
                'summary' => 'Safety kit with helmet, vest, gloves, and protective basics for supervised construction sites.',
                'image' => $this->toolImage('site-safety-gear-kit'),
                'gallery' => [$this->toolImage('site-safety-gear-kit')],
            ],
        ];
    }

    private function fallbackProviders(): array
    {
        return [
            ['name' => 'Kijenge Supplies Ltd', 'slug' => 'kijenge-supplies', 'category' => 'Material Suppliers', 'region' => 'Dar es Salaam', 'location' => 'Kinondoni', 'rating' => 4.8, 'jobs' => 214, 'is_verified' => true, 'summary' => 'Fast cement, sand, and block delivery across Kinondoni, Ubungo, and Ilala.'],
            ['name' => 'Mwanza Steel Traders', 'slug' => 'mwanza-steel-traders', 'category' => 'Material Suppliers', 'region' => 'Mwanza', 'location' => 'Ilemela', 'rating' => 4.7, 'jobs' => 148, 'is_verified' => true, 'summary' => 'Rebar, binding wire, and structural steel for lake-zone projects.'],
            ['name' => 'Arusha Buildline Architects', 'slug' => 'arusha-buildline-architects', 'category' => 'Architects', 'region' => 'Arusha', 'location' => 'Arusha City', 'rating' => 4.9, 'jobs' => 93, 'is_verified' => true, 'summary' => 'Modern residential and lodge design with permit-ready documentation.'],
            ['name' => 'Dodoma Civil Works Group', 'slug' => 'dodoma-civil-works', 'category' => 'Contractors', 'region' => 'Dodoma', 'location' => 'Chamwino', 'rating' => 4.6, 'jobs' => 121, 'is_verified' => true, 'summary' => 'Foundations, drainage, boundary walls, and commercial renovations.'],
            ['name' => 'Morogoro Engineering Bureau', 'slug' => 'morogoro-engineering-bureau', 'category' => 'Engineers', 'region' => 'Morogoro', 'location' => 'Morogoro Urban', 'rating' => 4.8, 'jobs' => 76, 'is_verified' => true, 'summary' => 'Structural design, site inspections, and technical reports.'],
            ['name' => 'Tanga Equipment Hire', 'slug' => 'tanga-equipment-hire', 'category' => 'Equipment Rental', 'region' => 'Tanga', 'location' => 'Tanga City', 'rating' => 4.6, 'jobs' => 88, 'is_verified' => true, 'summary' => 'Concrete mixers, compactors, scaffolding, generators, and site equipment rental for coastal projects.'],
            ['name' => 'Mbeya Trade Fundis', 'slug' => 'mbeya-trade-fundis', 'category' => 'Skilled Labour', 'region' => 'Mbeya', 'location' => 'Mbeya City', 'rating' => 4.5, 'jobs' => 132, 'is_verified' => true, 'summary' => 'Masons, carpenters, plumbers, painters, electricians, and tile setters for regional construction sites.'],
            ['name' => 'Kilimanjaro Plumbing & Electrical', 'slug' => 'kilimanjaro-plumbing-electrical', 'category' => 'Electrical & Plumbing', 'region' => 'Kilimanjaro', 'location' => 'Moshi', 'rating' => 4.6, 'jobs' => 97, 'is_verified' => true, 'summary' => 'Building wiring, plumbing installation, maintenance, and testing support for residential and commercial work.'],
        ];
    }

    private function fallbackCategories(): array
    {
        return collect(config('vant_products.categories', []))
            ->pluck('name')
            ->merge(['Concrete Equipment', 'Lifting & Access', 'Site Safety', 'Compaction & Generators'])
            ->unique()
            ->values()
            ->all();
    }

    private function marketplaceCategoryPriority(array $categories): array
    {
        $priority = [
            'Earthmoving & Heavy Machinery',
            'Concrete & Masonry Equipment',
            'Concrete Equipment',
            'Lifting & Material Handling',
            'Lifting & Access',
            'Power Tools',
            'Electrical & Power Equipment',
            'Hand Tools',
        ];

        return collect($priority)
            ->merge($categories)
            ->unique()
            ->values()
            ->all();
    }

    private function fallbackProviderCategories(): array
    {
        return ['Material Suppliers', 'Equipment Rental', 'Architects', 'Engineers', 'Contractors', 'Skilled Labour', 'Site Support', 'Electrical & Plumbing', 'Transport & Logistics'];
    }

    private function fallbackRegions(): array
    {
        return [
            'Arusha',
            'Dar es Salaam',
            'Dodoma',
            'Geita',
            'Iringa',
            'Kagera',
            'Katavi',
            'Kigoma',
            'Kilimanjaro',
            'Lindi',
            'Manyara',
            'Mara',
            'Mbeya',
            'Morogoro',
            'Mtwara',
            'Mwanza',
            'Njombe',
            'Pwani',
            'Rukwa',
            'Ruvuma',
            'Shinyanga',
            'Simiyu',
            'Singida',
            'Songwe',
            'Tabora',
            'Tanga',
            'Kaskazini Pemba',
            'Kusini Pemba',
            'Mjini Magharibi',
            'Kaskazini Unguja',
            'Kusini Unguja',
        ];
    }
}

