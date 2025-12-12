import './bootstrap';

// Importar Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Importar GSAP y ScrollTrigger
import gsap from 'gsap';
import ScrollTrigger from 'gsap/ScrollTrigger';

// Importar Three.js y GLTFLoader
import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';

// Registrar ScrollTrigger
gsap.registerPlugin(ScrollTrigger);

// Exportar gsap globalmente para uso en otros scripts
window.gsap = gsap;
window.ScrollTrigger = ScrollTrigger;

// ============================================
// CONFIGURACIÓN DE LA ESCENA 3D DEL TROFEO
// ============================================
function initTrophy3D() {
    const canvas = document.getElementById('trophy-canvas');
    if (!canvas) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(45, canvas.clientWidth / canvas.clientHeight, 0.1, 1000);
    camera.position.set(0, 0.5, 7); // Adjust camera to see detailing
    camera.lookAt(0, 0.2, 0);

    const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
    renderer.setSize(canvas.clientWidth, canvas.clientHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.0;
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    // --- Iluminación ---
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
    scene.add(ambientLight);

    const dirLight = new THREE.DirectionalLight(0xfff5e1, 3);
    dirLight.position.set(5, 5, 5);
    dirLight.castShadow = true;
    dirLight.shadow.mapSize.width = 1024;
    dirLight.shadow.mapSize.height = 1024;
    scene.add(dirLight);

    // Luz de borde para resaltar contornos
    const spotLight = new THREE.SpotLight(0xffd700, 5);
    spotLight.position.set(-5, 5, 2);
    spotLight.angle = Math.PI / 4;
    spotLight.penumbra = 0.5;
    scene.add(spotLight);

    // --- Grupo Principal ---
    const trophyGroup = new THREE.Group();

    // --- Materiales ---
    const goldMaterial = new THREE.MeshPhysicalMaterial({
        color: 0xffd700,
        emissive: 0x221100, // brillo interno sutil
        roughness: 0.25,
        metalness: 1.0,
        clearcoat: 0.8,
        clearcoatRoughness: 0.1,
        reflectivity: 1
    });

    const reliefMaterial = new THREE.MeshPhysicalMaterial({
        color: 0xe6b800, // Un oro ligeramente diferente para contraste
        roughness: 0.3,
        metalness: 1.0,
        clearcoat: 0.5
    });


    // --- Geometrías ---

    // 1. Base (Escalonada)
    const baseGeo1 = new THREE.CylinderGeometry(1.2, 1.4, 0.3, 64);
    const base1 = new THREE.Mesh(baseGeo1, goldMaterial);
    base1.position.y = -2;
    base1.receiveShadow = true;
    trophyGroup.add(base1);

    const baseGeo2 = new THREE.CylinderGeometry(0.9, 1.1, 0.6, 64);
    const base2 = new THREE.Mesh(baseGeo2, goldMaterial);
    base2.position.y = -1.55;
    base2.receiveShadow = true;
    trophyGroup.add(base2);

    // 2. Columna Central Decorada
    const stemGeo = new THREE.CylinderGeometry(0.3, 0.4, 1.2, 32);
    const stem = new THREE.Mesh(stemGeo, goldMaterial);
    stem.position.y = -0.7;
    trophyGroup.add(stem);

    // Detalles en la columna (Anillos)
    const ringGeo = new THREE.TorusGeometry(0.4, 0.08, 16, 64);
    const ring1 = new THREE.Mesh(ringGeo, goldMaterial);
    ring1.rotation.x = Math.PI / 2;
    ring1.position.y = -1.1;
    trophyGroup.add(ring1);

    const ring2 = new THREE.Mesh(ringGeo, goldMaterial);
    ring2.rotation.x = Math.PI / 2;
    ring2.position.y = -0.3;
    trophyGroup.add(ring2);

    // 3. La Copa
    const cupPoints = [];
    for (let i = 0; i < 10; i++) {
        cupPoints.push(new THREE.Vector2(Math.sin(i * 0.2) * 1.5 + 0.3, (i * 0.25)));
    }
    const cupGeo = new THREE.LatheGeometry(cupPoints, 64);
    const cup = new THREE.Mesh(cupGeo, goldMaterial);
    cup.position.y = -0.1;
    cup.castShadow = true;
    cup.receiveShadow = true;
    trophyGroup.add(cup);

    // 4. Asas (Handles) - Torus parciales o Tubos
    const handleShape = new THREE.CatmullRomCurve3([
        new THREE.Vector3(1.2, 0.5, 0),
        new THREE.Vector3(2.0, 1.5, 0),
        new THREE.Vector3(1.5, 2.2, 0),
        new THREE.Vector3(1.0, 1.8, 0)
    ]);
    const handleGeo = new THREE.TubeGeometry(handleShape, 64, 0.12, 16, false);
    const handle1 = new THREE.Mesh(handleGeo, goldMaterial);
    trophyGroup.add(handle1);

    const handle2 = handle1.clone();
    handle2.rotation.y = Math.PI;
    trophyGroup.add(handle2);

    // --- RELIEVE DE BIRRETE (Graduation Cap Relief) ---
    // Construimos una geometría compuesta para simular el relieve en el frente

    const capGroup = new THREE.Group();

    // Tablero del birrete (Rombo plano)
    const boardGeo = new THREE.BoxGeometry(0.8, 0.05, 0.8);
    const board = new THREE.Mesh(boardGeo, reliefMaterial);
    // Rotar para forma de rombo vista de frente
    board.rotation.x = Math.PI / 4; // Inclinar un poco
    board.rotation.y = Math.PI / 4; // Girar para que apunte la esquina el frente
    capGroup.add(board);

    // Gorro base (Skullcap)
    const capBaseGeo = new THREE.CylinderGeometry(0.25, 0.25, 0.2, 32);
    const capBase = new THREE.Mesh(capBaseGeo, reliefMaterial);
    capBase.position.y = -0.15;
    capGroup.add(capBase);

    // Borla (Tassel)
    const tasselGeo = new THREE.CylinderGeometry(0.02, 0.05, 0.4, 8);
    const tassel = new THREE.Mesh(tasselGeo, reliefMaterial);
    tassel.position.set(0.35, -0.1, 0.1);
    tassel.rotation.z = -0.3;
    capGroup.add(tassel);

    const tasselKnot = new THREE.SphereGeometry(0.04, 16, 16);
    const knot = new THREE.Mesh(tasselKnot, reliefMaterial);
    knot.position.set(0, 0.05, 0); // Centro arriba
    capGroup.add(knot);

    // Posicionar el relieve en la superficie de la copa
    // La copa tiene radio aprox 1.5 en y=1.5
    capGroup.position.set(0, 1.2, 1.35); // En frente (Z positivo), altura media
    capGroup.rotation.x = -0.2; // Alinear con la curvatura de la copa

    trophyGroup.add(capGroup);

    scene.add(trophyGroup);

    // --- Animación ---

    // Rotación suave constante
    gsap.to(trophyGroup.rotation, {
        y: Math.PI * 2,
        duration: 20,
        repeat: -1,
        ease: "none"
    });

    // Flotación arriba/abajo
    gsap.to(trophyGroup.position, {
        y: 0.2,
        duration: 3,
        yoyo: true,
        repeat: -1,
        ease: "sine.inOut"
    });

    function animate() {
        requestAnimationFrame(animate);
        renderer.render(scene, camera);
    }
    animate();

    // Resize Handler
    window.addEventListener('resize', () => {
        if (!canvas) return;
        const width = canvas.clientWidth;
        const height = canvas.clientHeight;
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
    });
}

// ============================================
// PARTÍCULAS DE FONDO (COPOS DE NIEVE DIFUMINADOS)
// ============================================
function initParticles() {
    const canvas = document.getElementById('particles-canvas');
    if (!canvas) return;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    camera.position.z = 5;

    const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);

    const canvasTexture = document.createElement('canvas');
    canvasTexture.width = 32;
    canvasTexture.height = 32;
    const ctx = canvasTexture.getContext('2d');
    const gradient = ctx.createRadialGradient(16, 16, 0, 16, 16, 16);
    gradient.addColorStop(0, 'rgba(255, 255, 255, 1)');
    gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, 32, 32);
    const texture = new THREE.CanvasTexture(canvasTexture);

    const particlesGeometry = new THREE.BufferGeometry();
    const particlesCount = 100;
    const posArray = new Float32Array(particlesCount * 3);

    for (let i = 0; i < particlesCount * 3; i++) {
        posArray[i] = (Math.random() - 0.5) * 15;
    }

    particlesGeometry.setAttribute('position', new THREE.BufferAttribute(posArray, 3));

    const particlesMaterial = new THREE.PointsMaterial({
        size: 0.2,
        map: texture,
        transparent: true,
        opacity: 0.6,
        depthWrite: false,
        blending: THREE.AdditiveBlending
    });

    const particlesMesh = new THREE.Points(particlesGeometry, particlesMaterial);
    scene.add(particlesMesh);

    function animate() {
        requestAnimationFrame(animate);
        particlesMesh.rotation.y += 0.001;
        particlesMesh.rotation.x += 0.0005;
        renderer.render(scene, camera);
    }
    animate();

    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });
}

// ============================================
// ANIMACIÓN DEL LIBRO
// ============================================
function initBookAnimation() {
    if (!document.getElementById('book-section')) return;

    gsap.registerPlugin(ScrollTrigger);

    const bookContainer = document.getElementById('book-container');
    const bookCover = document.getElementById('book-cover');
    const page1 = document.getElementById('page-1');
    const page2 = document.getElementById('page-2');
    const page3 = document.getElementById('page-3');
    const bookControls = document.getElementById('book-controls');
    const bookText = document.getElementById('book-text');

    if (!bookContainer || !bookCover || !page1 || !page2 || !page3) return;

    // 1. CONFIGURACIÓN INICIAL (Estado Hero)
    // El libro empieza fijo en el fondo, inclinado y semitransparente
    gsap.set(bookContainer, {
        position: 'fixed',
        top: '20%',
        left: '50%',
        xPercent: -50,
        yPercent: -50,
        rotationX: 30,
        rotationY: -10,
        rotationZ: -5,
        scale: 0.5,
        opacity: 0,
        zIndex: 0,
        filter: 'blur(4px)'
    });

    // Configuración de páginas y cubierta para rotación realista
    const pages = [bookCover, page1, page2, page3];
    gsap.set(pages, {
        rotationY: 0,
        transformOrigin: "left center",
        transformStyle: "preserve-3d"
    });

    // Aparecer suavemente en el Hero (Fade In inicial)
    gsap.to(bookContainer, { opacity: 0.4, duration: 1, delay: 0.5 });

    // 2. FASE 1: TRANSICIÓN HERO -> BOOK SECTION
    const transitionTl = gsap.timeline({
        scrollTrigger: {
            trigger: "#book-section",
            start: "top bottom",
            end: "center center",
            scrub: 1,
            immediateRender: false
        }
    });

    transitionTl
        .to(bookContainer, {
            top: '50%',
            rotationX: 0,
            rotationY: 0,
            rotationZ: 0,
            scale: 1,
            opacity: 1,
            filter: 'blur(0px)',
            zIndex: 40,
            ease: "power2.inOut"
        })
        .to([bookText, bookControls], { opacity: 1, duration: 0.5 }, "-=0.5");

    // 3. FASE 2: APERTURA Y NAVEGACIÓN (PINNED)
    const bookTl = gsap.timeline({
        scrollTrigger: {
            trigger: "#book-section",
            start: "center center",
            end: "+=5000", // Más largo al tener más páginas
            pin: true,
            scrub: 1,
            anticipatePin: 1
        }
    });

    // Función helper para pasar página y revelar rastro con delay
    // Función helper para pasar página y revelar rastro con delay
    // Agregamos parametro finalZ para corregir superposición
    function turnPage(element, finalZ) {
        // Girar página
        bookTl.to(element, {
            rotationY: -180,
            zIndex: finalZ, // Fix Z-Index
            duration: 2,
            ease: "power2.inOut"
        });

        // FADE OUT DE CONTENIDO INTERNO (Para la portada: "Bienvenido")
        // Soluciona visualmente el overlapping quitando el texto, dejando solo el borde/fondo
        const insideContent = element.querySelector('.inside-cover-content');
        if (insideContent) {
            bookTl.to(insideContent, {
                opacity: 0,
                duration: 0.5,
                ease: "power2.in"
            }, "-=0.5"); // Se desvanece justo al terminar de abrirse
        }

        // Revelar contenido "trace" al finalizar giro (en la parte trasera)
        const traceContent = element.querySelector('.trace-content');
        if (traceContent) {
            bookTl.to(traceContent, {
                opacity: 1,
                duration: 0.5,
                ease: "power2.out"
            }, "-=1.5");
        }
    }

    // A: Abrir Portada - Al abrirse debe ir al fondo del stack izquierdo (Z=1)
    turnPage(bookCover, 1);

    // B: Pasar Página 1 - Queda sobre la portada (Z=2)
    turnPage(page1, 2);

    // C: Pasar Página 2 - Queda sobre la página 1 (Z=3)
    turnPage(page2, 3);

    // D: Pasar Página 3 - Queda sobre la página 2 (Z=4)
    turnPage(page3, 4);

    // Mantener final
    bookTl.to({}, { duration: 1 });

    // 4. FASE 3: SALIDA
    bookTl.to(bookContainer, {
        opacity: 0,
        scale: 1.1,
        filter: 'blur(10px)',
        duration: 1.5,
        ease: "power2.in"
    });

    console.log('Book animation initialized: Covers + 3 Pages logic');
    ScrollTrigger.refresh();
}

function initAuthAnimations() {
    const authCard = document.querySelector('.auth-card');
    if (authCard) {
        gsap.to(authCard, {
            opacity: 1,
            scale: 1,
            duration: 0.8,
            ease: "back.out(1.7)",
            delay: 0.2
        });

        gsap.from(".auth-header", {
            y: 20,
            opacity: 0,
            duration: 0.8,
            ease: "power2.out",
            delay: 0.4
        });

        gsap.from(".form-item", {
            y: 20,
            opacity: 0,
            duration: 0.6,
            stagger: 0.1,
            ease: "power2.out",
            delay: 0.6
        });
    }
}

function initHelpWidgetAnimations() {
    const helpWidget = document.querySelector('[x-data="{ open: false }"]');
    if (helpWidget) {
        gsap.from(helpWidget, {
            y: 50,
            opacity: 0,
            duration: 1,
            ease: "elastic.out(1, 0.5)",
            delay: 1
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initParticles();
    initTrophy3D();
    initBookAnimation();
    initAuthAnimations();
    initHelpWidgetAnimations();
});