import './bootstrap';
import './live-navigation';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('cartDrawer', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    },
}));

Alpine.data('mobileMenu', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
}));

Alpine.data('gallery', (images = []) => ({
    images,
    active: images[0] ?? null,
    setActive(image) {
        this.active = image;
    },
}));

Alpine.start();
