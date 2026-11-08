import Alpine from 'alpinejs';
import { inicializarVisores3D } from './visor-3d';

window.Alpine = Alpine;

Alpine.start();

const arrancarVisores = () => {
    inicializarVisores3D();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', arrancarVisores, { once: true });
} else {
    arrancarVisores();
}
