(function () {
  const money = (value) => `TZS ${Number(value || 0).toLocaleString()}`;
  const escapeHtml = (value) => String(value || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;');

  const realImage = (url) => {
    const value = String(url || '');
    if (!value || /unsplash|picsum|placeholder|generated/i.test(value)) {
      return '/leivant-webp/20260523_142200.jpg.webp';
    }
    return value;
  };

  const revealNodes = (root) => {
    root.querySelectorAll('[data-reveal]').forEach(function (node) {
      node.classList.add('is-visible');
    });
  };

  document.addEventListener('scroll', function () {
    document.getElementById('site-header')?.classList.toggle('is-scrolled', window.scrollY > 8);
  }, { passive: true });

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -24px 0px' });
    document.querySelectorAll('[data-reveal]').forEach(function (node) { observer.observe(node); });
  } else {
    document.querySelectorAll('[data-reveal]').forEach(function (node) { node.classList.add('is-visible'); });
  }

  // Mobile menu: close on link tap / Escape, expose state to AT.
  document.querySelectorAll('[data-mobile-menu]').forEach(function (menu) {
    const summary = menu.querySelector('summary');
    const sync = function () {
      if (summary) summary.setAttribute('aria-expanded', menu.open ? 'true' : 'false');
    };
    menu.addEventListener('toggle', sync);
    sync();
    menu.querySelectorAll('.mobile-panel a').forEach(function (link) {
      link.addEventListener('click', function () { menu.open = false; });
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && menu.open) {
        menu.open = false;
        if (summary) summary.focus();
      }
    });
  });

  const productGrid = document.getElementById('home-product-grid');
  if (productGrid) {
    fetch('/api/site/products')
      .then(function (response) { return response.json(); })
      .then(function (payload) {
        const all = payload.data || [];
        const products = all.slice(0, 3);
        const countNode = document.getElementById('home-catalogue-count');
        if (countNode && all.length) {
          countNode.textContent = `${all.length} items currently listed in the Leivant catalogue.`;
        }
        if (!products.length) {
          productGrid.innerHTML = '<p class="loading"><a class="text-link" href="/products">Browse the full resource catalogue</a></p>';
          return;
        }
        productGrid.innerHTML = products.map(function (product) {
          const price = product.is_for_rent && product.rental_price_per_day
            ? `${money(product.rental_price_per_day)} / day`
            : money(product.price);
          const image = realImage(product.image);
          return `<article class="pc" data-reveal>
            <div class="pc-img"><img src="${escapeHtml(image)}" alt="${escapeHtml(product.name)}" loading="lazy" onerror="this.onerror=null;this.src='/leivant-webp/20260523_142200.jpg.webp'"></div>
            <div class="pc-body">
            <div class="pc-top"><span class="cm-tag muted">${escapeHtml(product.category || 'Construction')}</span></div>
            <h3>${escapeHtml(product.name)}</h3>
            <div class="pc-price"><strong>${price}</strong></div>
            <a class="pc-btn" style="text-decoration:none;" aria-label="View details for ${escapeHtml(product.name)}" href="/products/${encodeURIComponent(product.slug)}">View details</a>
            </div>
          </article>`;
        }).join('');
        revealNodes(productGrid);
      })
      .catch(function () {
        productGrid.innerHTML = '<p class="loading"><a class="text-link" href="/products">Open resource catalogue</a></p>';
      });
  }

  const providerGrid = document.getElementById('home-provider-grid');
  if (providerGrid) {
    fetch('/api/site/providers')
      .then(function (response) { return response.json(); })
      .then(function (payload) {
        const providers = (payload.data || []).slice(0, 4);
        if (!providers.length) {
          providerGrid.innerHTML = '<p class="loading"><a class="text-link" href="/discovery">Browse project partners</a></p>';
          return;
        }
        providerGrid.innerHTML = providers.map(function (provider) {
          const verified = provider.is_verified ? '<span class="cm-tag">Verified</span>' : '';
          return `<article class="pv" data-reveal>
            <div class="pv-body">
              <div class="pv-meta">
                <h3>${escapeHtml(provider.name)}</h3>
                ${verified}
              </div>
              <p class="pv-sum">${escapeHtml(provider.category || 'Provider')} · ${escapeHtml(provider.region || 'Tanzania')}</p>
              <p class="pv-sum">${escapeHtml((provider.summary || '').slice(0, 120))}</p>
              <a class="pv-act" href="/discovery">View profile</a>
            </div>
          </article>`;
        }).join('');
        revealNodes(providerGrid);
      })
      .catch(function () {
        providerGrid.innerHTML = '<p class="loading"><a class="text-link" href="/discovery">Open provider directory</a></p>';
      });
  }
})();
