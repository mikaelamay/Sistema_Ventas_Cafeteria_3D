import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';

const LIMITE_PIXEL_RATIO = 2;
const visoresActivos = new Set();
const inicializados = new WeakSet();

/**
 * Initialize every 3D viewer present on the current page.
 */
export function inicializarVisores3D() {
    document.querySelectorAll('[data-visor-3d]').forEach((canvas) => {
        if (inicializados.has(canvas)) {
            return;
        }

        inicializados.add(canvas);
        inicializarVisor(canvas);
    });
}

/**
 * @param {HTMLCanvasElement} canvas
 */
function inicializarVisor(canvas) {
    const urlModelo = (canvas.dataset.modeloUrl ?? '').trim();

    // Without a real model we leave the server-rendered empty state untouched.
    if (urlModelo === '') {
        return;
    }

    const contenedor = canvas.closest('[data-visor-contenedor]') ?? canvas.parentElement;

    if (!contenedor) {
        return;
    }

    const indicadorCarga = contenedor.querySelector('[data-visor-cargando]');
    const pistaRotacion = contenedor.querySelector('[data-visor-pista]');

    let renderer;

    try {
        renderer = new THREE.WebGLRenderer({
            canvas,
            antialias: true,
            alpha: true,
            powerPreference: 'high-performance',
        });
    } catch {
        mostrarError(contenedor, canvas, null);

        return;
    }

    renderer.setClearColor(0x000000, 0);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, LIMITE_PIXEL_RATIO));

    const escena = new THREE.Scene();
    const camara = new THREE.PerspectiveCamera(35, 1, 0.1, 200);

    const ambiente = new THREE.AmbientLight(0xffffff, 1.5);
    escena.add(ambiente);

    const luzPrincipal = new THREE.DirectionalLight(0xfff2dd, 2.6);
    luzPrincipal.position.set(4, 6, 5);
    escena.add(luzPrincipal);

    const luzRelleno = new THREE.DirectionalLight(0xffffff, 1.1);
    luzRelleno.position.set(-4, 2, -3);
    escena.add(luzRelleno);

    const controles = new OrbitControls(camara, canvas);
    controles.enableDamping = true;
    controles.dampingFactor = 0.08;
    controles.enablePan = false;
    controles.rotateSpeed = 0.8;
    controles.zoomSpeed = 0.9;

    const prefiereMenosMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!prefiereMenosMovimiento) {
        controles.autoRotate = true;
        controles.autoRotateSpeed = 0.6;
    }

    const estado = {
        renderer,
        escena,
        camara,
        controles,
        frameId: null,
        resizeObserver: null,
        ajustarTamano: null,
        liberado: false,
    };

    visoresActivos.add(estado);

    estado.ajustarTamano = () => {
        const ancho = contenedor.clientWidth || 1;
        const alto = contenedor.clientHeight || 1;

        camara.aspect = ancho / alto;
        camara.updateProjectionMatrix();
        renderer.setSize(ancho, alto, false);
    };

    estado.resizeObserver = new ResizeObserver(estado.ajustarTamano);
    estado.resizeObserver.observe(contenedor);
    window.addEventListener('resize', estado.ajustarTamano);

    estado.ajustarTamano();

    const renderizar = () => {
        estado.frameId = requestAnimationFrame(renderizar);
        controles.update();
        renderer.render(escena, camara);
    };

    const cargador = new GLTFLoader();

    cargador.load(
        urlModelo,
        (gltf) => {
            try {
                const modelo = gltf.scene;
                const caja = new THREE.Box3().setFromObject(modelo);
                const tamano = caja.getSize(new THREE.Vector3());
                const centro = caja.getCenter(new THREE.Vector3());
                const dimensionMayor = Math.max(tamano.x, tamano.y, tamano.z) || 1;

                modelo.position.sub(centro);
                escena.add(modelo);

                const distancia = (dimensionMayor / (2 * Math.tan(THREE.MathUtils.degToRad(camara.fov / 2)))) * 1.55;

                camara.near = Math.max(distancia / 100, 0.01);
                camara.far = distancia * 100;
                camara.position.set(distancia * 0.8, distancia * 0.45, distancia);
                camara.updateProjectionMatrix();

                controles.target.set(0, 0, 0);
                controles.minDistance = dimensionMayor * 0.35;
                controles.maxDistance = dimensionMayor * 8;

                indicadorCarga?.classList.add('hidden');
                canvas.classList.remove('opacity-0');
                canvas.classList.add('opacity-100');

                renderizar();
            } catch {
                mostrarError(contenedor, canvas, estado);
            }
        },
        undefined,
        () => {
            mostrarError(contenedor, canvas, estado);
        },
    );

    const manejarSalida = () => {
        liberarRecursos(estado);
    };

    window.addEventListener('pagehide', manejarSalida, { once: true });

    // Keep the chip visible only while the rendered model is available.
    pistaRotacion?.classList.remove('hidden');
}

/**
 * @param {HTMLElement} contenedor
 * @param {HTMLCanvasElement} canvas
 * @param {object|null} estado
 */
function mostrarError(contenedor, canvas, estado) {
    liberarRecursos(estado);

    canvas.classList.add('hidden');
    canvas.setAttribute('aria-hidden', 'true');

    contenedor.querySelector('[data-visor-cargando]')?.classList.add('hidden');
    contenedor.querySelector('[data-visor-pista]')?.classList.add('hidden');
    contenedor.parentElement?.querySelector('[data-visor-ayuda]')?.classList.add('hidden');

    const error = contenedor.querySelector('[data-visor-error]');

    error?.classList.remove('hidden');
    error?.classList.add('flex');
}

/**
 * Dispose every GPU resource associated with a viewer.
 *
 * @param {object|null} estado
 */
function liberarRecursos(estado) {
    if (!estado || estado.liberado) {
        return;
    }

    estado.liberado = true;
    visoresActivos.delete(estado);

    if (estado.frameId !== null) {
        cancelAnimationFrame(estado.frameId);
    }

    estado.resizeObserver?.disconnect();
    window.removeEventListener('resize', estado.ajustarTamano);
    estado.controles?.dispose();

    estado.escena?.traverse((objeto) => {
        try {
            objeto.geometry?.dispose();

            const materiales = Array.isArray(objeto.material)
                ? objeto.material
                : objeto.material
                  ? [objeto.material]
                  : [];

            materiales.forEach((material) => {
                Object.values(material).forEach((valor) => {
                    if (valor && valor.isTexture) {
                        valor.dispose();
                    }
                });

                material.dispose();
            });
        } catch {
            // A partially built model must never block the rest of the cleanup.
        }
    });

    estado.renderer?.dispose();
    estado.renderer?.forceContextLoss?.();
}

window.addEventListener('pagehide', () => {
    visoresActivos.forEach((estado) => liberarRecursos(estado));
    visoresActivos.clear();
});
