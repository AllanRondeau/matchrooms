<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Enregistrement des plugins GSAP
gsap.registerPlugin(ScrollTrigger);

// État
const heroImageLoaded = ref(false);
const mousePosition = reactive({ x: 0, y: 0 });
const cursorVisible = ref(false);
const isNavOpen = ref(false);
const scrollY = ref(0);
const parallaxElements = ref([]);
const currentSection = ref('hero');
const negotiationPrice = ref(135);
const originalPrice = 180;
const negotiationComplete = ref(false);
const negotiationStep = ref(0);
const negotiationPercentage = computed(() => Math.round((originalPrice - negotiationPrice.value) / originalPrice * 100));

// Données de la section fonctionnalités
const features = [
  {
    title: 'Proposez votre prix',
    description: 'Faites des offres sur des chambres d\'hôtel et négociez directement avec les hôteliers.',
    icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  {
    title: 'Match parfait',
    description: 'Notre algorithme trouve l\'hébergement idéal correspondant à votre budget et vos préférences.',
    icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'
  },
  {
    title: 'Réservez en confiance',
    description: 'Obtenez les meilleures offres avec des prix transparents et sans frais cachés.',
    icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
  },
];

// Villes avec hôtels
const popularCities = [
  { name: 'Paris', count: 842, img: '/images/paris.jpg' },
  { name: 'Londres', count: 1024, img: '/images/london.jpg' },
  { name: 'New York', count: 1357, img: '/images/new-york.jpg' },
  { name: 'Tokyo', count: 721, img: '/images/tokyo.jpg' },
  { name: 'Barcelone', count: 512, img: '/images/barcelona.jpg' },
  { name: 'Rome', count: 498, img: '/images/rome.jpg' },
];

// Témoignages
const testimonials = [
  {
    quote: "MatchRoom m'a permis d'économiser plus de 30% sur mon séjour à Paris. Le processus de négociation était simple et l'hôtel a répondu en moins d'une heure !",
    author: "Sophie Laurant",
    role: "Voyageuse d'affaires",
    avatar: "/images/avatar-1.jpg"
  },
  {
    quote: "En tant que gérant d'hôtel, MatchRoom nous aide à remplir des chambres qui seraient autrement restées vides. C'est gagnant-gagnant pour tous.",
    author: "Jean Dupont",
    role: "Gérant d'hôtel",
    avatar: "/images/avatar-2.jpg"
  },
  {
    quote: "J'adore la transparence de la plateforme. Je peux voir exactement ce pour quoi je paie, sans surprises ni frais cachés.",
    author: "Marc Williams",
    role: "Voyageur régulier",
    avatar: "/images/avatar-3.jpg"
  }
];

const activeTestimonial = ref(0);
const isIntersecting = ref({});
const hotelTypePreference = ref('luxury');

// Méthodes
const updateMousePosition = (e) => {
  mousePosition.x = e.clientX;
  mousePosition.y = e.clientY;
  cursorVisible.value = true;
};

const hideCursor = () => {
  cursorVisible.value = false;
};

const changeTestimonial = () => {
  activeTestimonial.value = (activeTestimonial.value + 1) % testimonials.length;
};

const updateParallax = () => {
  scrollY.value = window.scrollY;
  
  // Mise à jour de la section actuelle basée sur la position de défilement
  const sections = document.querySelectorAll('section');
  sections.forEach(section => {
    const rect = section.getBoundingClientRect();
    if (rect.top <= window.innerHeight / 2 && rect.bottom >= window.innerHeight / 2) {
      currentSection.value = section.id || 'hero';
    }
  });
};

const animateNegotiation = () => {
  if (negotiationStep.value < 3) {
    negotiationStep.value++;
    
    if (negotiationStep.value === 1) {
      // Offre initiale
      gsap.to(negotiationPrice, {
        value: 135,
        duration: 1,
        onUpdate: () => negotiationPrice.value = Math.round(negotiationPrice.value)
      });
    } else if (negotiationStep.value === 2) {
      // Contre-offre
      gsap.to(negotiationPrice, {
        value: 150,
        duration: 1,
        onUpdate: () => negotiationPrice.value = Math.round(negotiationPrice.value)
      });
    } else if (negotiationStep.value === 3) {
      // Accord final
      gsap.to(negotiationPrice, {
        value: 143,
        duration: 1,
        onUpdate: () => negotiationPrice.value = Math.round(negotiationPrice.value),
        onComplete: () => negotiationComplete.value = true
      });
    }
  } else {
    // Réinitialiser la démo
    negotiationStep.value = 0;
    negotiationComplete.value = false;
    gsap.to(negotiationPrice, {
      value: originalPrice,
      duration: 0.5,
      onUpdate: () => negotiationPrice.value = Math.round(negotiationPrice.value)
    });
  }
};

const initScrollAnimations = () => {
  // Animations de la section hero
  gsap.from('.hero-title', {
    duration: 1,
    y: 50,
    opacity: 0,
    ease: 'power3.out',
    stagger: 0.2
  });
  
  gsap.from('.hero-description', {
    duration: 1,
    y: 30,
    opacity: 0,
    ease: 'power3.out',
    delay: 0.4
  });
  
  gsap.from('.hero-cta', {
    duration: 1,
    y: 20,
    opacity: 0,
    ease: 'power3.out',
    delay: 0.6,
    stagger: 0.2
  });
  
  // Animation au défilement pour les sections
  const sections = ['features', 'how-it-works', 'cities', 'testimonials', 'cta'];
  
  sections.forEach(section => {
    ScrollTrigger.create({
      trigger: `#${section}`,
      start: 'top 80%',
      onEnter: () => {
        gsap.to(`#${section} .section-title`, {
          y: 0,
          opacity: 1,
          duration: 0.8,
          ease: 'power3.out'
        });
        
        gsap.to(`#${section} .section-description`, {
          y: 0,
          opacity: 1,
          duration: 0.8,
          delay: 0.2,
          ease: 'power3.out'
        });
        
        gsap.to(`#${section} .animate-item`, {
          y: 0,
          opacity: 1,
          duration: 0.8,
          stagger: 0.15,
          delay: 0.3,
          ease: 'power3.out'
        });
      },
      once: true
    });
  });
  
  // Effets de parallaxe au défilement
  gsap.utils.toArray('.parallax-bg').forEach(layer => {
    const depth = layer.dataset.depth || 0.2;
    const movement = -(layer.offsetHeight * depth);
    
    gsap.to(layer, {
      y: movement,
      ease: 'none',
      scrollTrigger: {
        trigger: layer.parentElement,
        start: 'top top',
        end: 'bottom top',
        scrub: true
      }
    });
  });
};

const initHotelCardAnimation = () => {
  // Animation au survol des cartes d'hôtel
  const roomCards = document.querySelectorAll('.hotel-room-card');
  roomCards.forEach(card => {
    card.addEventListener('mouseenter', () => {
      gsap.to(card, {
        y: -10,
        scale: 1.03,
        boxShadow: '0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)',
        duration: 0.3
      });
    });
    
    card.addEventListener('mouseleave', () => {
      gsap.to(card, {
        y: 0,
        scale: 1,
        boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
        duration: 0.3
      });
    });
  });
};

// Cycles de vie
onMounted(() => {
  // Initialisation des animations
  initScrollAnimations();
  setTimeout(initHotelCardAnimation, 1000);
  
  // Configuration des écouteurs d'événements
  window.addEventListener('mousemove', updateMousePosition);
  window.addEventListener('mouseleave', hideCursor);
  window.addEventListener('scroll', updateParallax);
  
  // Démarrer la rotation des témoignages
  const testimonialInterval = setInterval(changeTestimonial, 8000);
  
  // Fonction de nettoyage
  onBeforeUnmount(() => {
    window.removeEventListener('mousemove', updateMousePosition);
    window.removeEventListener('mouseleave', hideCursor);
    window.removeEventListener('scroll', updateParallax);
    clearInterval(testimonialInterval);
  });
  
  // Chargement de l'image hero
  const heroImg = new Image();
  heroImg.onload = () => {
    heroImageLoaded.value = true;
  };
  heroImg.src = '/images/hotel-room.jpg';
  
  // Initialisation de l'observateur pour les animations
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      isIntersecting[entry.target.id] = entry.isIntersecting;
    });
  }, { threshold: 0.1 });
  
  document.querySelectorAll('.animate-on-scroll').forEach(el => {
    observer.observe(el);
  });
});
</script>

<template>
  <Head title="MatchRoom - Négociez votre chambre d'hôtel idéale" />
  
  <!-- Conteneur principal avec design 2025 -->
  <div class="min-h-screen bg-[#F4F3F3] overflow-x-hidden font-sans">
    <!-- En-tête avec design glassmorphism 2025 -->
    <header 
      class="fixed w-full z-50 transition-all duration-500"
      :class="{ 
        'py-3 backdrop-blur-xl bg-white/70': scrollY > 100, 
        'py-5 backdrop-blur-sm bg-transparent': scrollY <= 100 
      }">
      <div class="container mx-auto px-6 flex justify-between items-center">
        <div class="flex items-center">
          <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/logoMR-TakbHOAaljFXg5P560GFVsP1M2q0Tx.png" alt="Logo MatchRoom" class="h-10 transition-transform duration-300 hover:scale-105" />
        </div>
        
        <!-- Bouton menu mobile avec animation moderne -->
        <button @click="isNavOpen = !isNavOpen" 
                class="md:hidden relative w-10 h-10 flex items-center justify-center focus:outline-none group">
          <span class="relative block w-6">
            <span class="absolute h-0.5 w-6 bg-[#2E2E38] transform transition-all duration-300"
                  :class="isNavOpen ? 'rotate-45 top-0' : '-translate-y-1.5'"></span>
            <span class="absolute h-0.5 w-6 bg-[#2E2E38] transform transition-all duration-300"
                  :class="isNavOpen ? 'opacity-0' : 'opacity-100'"></span>
            <span class="absolute h-0.5 w-6 bg-[#2E2E38] transform transition-all duration-300"
                  :class="isNavOpen ? '-rotate-45 top-0' : 'translate-y-1.5'"></span>
          </span>
        </button>
        
        <!-- Navigation desktop avec effet hover moderne -->
        <nav class="hidden md:flex space-x-8 text-[#2E2E38]">
          <a v-for="(item, index) in ['Fonctionnalités', 'Comment ça marche', 'Destinations', 'Témoignages']" 
             :key="index"
             :href="`#${['features', 'how-it-works', 'cities', 'testimonials'][index]}`" 
             class="relative font-medium text-sm tracking-wide uppercase transition-colors duration-300 hover:text-[#FF135F] after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-[#FF135F] hover:after:w-full after:transition-all after:duration-300">
            {{ item }}
          </a>
        </nav>
        
        <!-- Menu mobile avec animation slide-in -->
        <div v-if="isNavOpen" 
             class="absolute top-full left-0 right-0 bg-white/95 backdrop-blur-xl shadow-2xl z-50 md:hidden p-6 animate-slideDown">
          <nav class="flex flex-col space-y-5">
            <a v-for="(item, index) in ['Fonctionnalités', 'Comment ça marche', 'Destinations', 'Témoignages']" 
               :key="index"
               :href="`#${['features', 'how-it-works', 'cities', 'testimonials'][index]}`"
               @click="isNavOpen = false"
               class="font-medium py-3 px-4 rounded-xl transition-all duration-300 hover:bg-[#F7E6E2] hover:text-[#FF135F] flex items-center">
              <span class="w-8 h-8 mr-3 rounded-full bg-[#F7E6E2] flex items-center justify-center text-[#FF135F]">
                {{ index + 1 }}
              </span>
              {{ item }}
            </a>
            <div class="pt-4 border-t border-[#E9E1DC] space-y-3">
              <a href="/login" class="block w-full py-3 px-4 text-center font-medium text-[#2E2E38] bg-[#E9E1DC] hover:bg-[#D6E1E5] rounded-xl transition-all duration-300">
                Connexion
              </a>
              <a href="/register" class="block w-full py-3 px-4 text-center font-medium bg-[#FF135F] text-white rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-[#FF135F]/20">
                Inscription
              </a>
            </div>
          </nav>
        </div>
        
        <!-- Boutons d'action avec design 2025 -->
        <div class="hidden md:flex space-x-4">
          <a href="/login" 
             class="font-medium text-[#2E2E38] px-5 py-2.5 rounded-xl transition-all duration-300 hover:bg-[#E9E1DC]">
            Connexion
          </a>
          <a href="/register" 
             class="font-medium bg-[#FF135F] text-white px-6 py-2.5 rounded-xl transition-all duration-300 hover:shadow-lg hover:shadow-[#FF135F]/20 hover:-translate-y-0.5">
            Inscription
          </a>
        </div>
      </div>
    </header>
    
    <!-- Section Hero avec design 2025 -->
    <section class="relative pt-40 pb-24 px-6 md:min-h-screen flex items-center" id="hero">
      <!-- Arrière-plan moderne avec formes organiques et effet glassmorphism -->
      <div class="absolute inset-0 overflow-hidden -z-10">
        <!-- Formes organiques avec dégradés modernes -->
        <div class="absolute top-0 right-0 w-[800px] h-[800px] rounded-full bg-gradient-to-br from-[#F7E6E2]/30 to-[#D6E1E5]/30 blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        <div class="absolute bottom-0 left-0 w-[600px] h-[600px] rounded-full bg-gradient-to-tr from-[#E9E1DC]/40 to-[#F7E6E2]/40 blur-3xl translate-y-1/3 -translate-x-1/4"></div>
        
        <!-- Grille moderne -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGZpbGw9IiNmOGZhZmMiIGQ9Ik0wIDBoNjB2NjBIMHoiLz48cGF0aCBkPSJNNjAgMzBjMCAxNi41NjktMTMuNDMxIDMwLTMwIDMwQzEzLjQzMSA2MCAwIDQ2LjU2OSAwIDMwIDAgMTMuNDMxIDEzLjQzMSAwIDMwIDBjMTYuNTY5IDAgMzAgMTMuNDMxIDMwIDMweiIgc3Ryb2tlPSIjZTJlOGYwIiBzdHJva2Utd2lkdGg9Ii41Ii8+PC9nPjwvc3ZnPg==')] opacity-[0.03]"></div>
      </div>
      
      <div class="container mx-auto grid md:grid-cols-12 gap-12 items-center">
        <!-- Contenu Hero avec typographie moderne -->
        <div class="md:col-span-6 z-10">
          <div class="inline-flex items-center px-3 py-1.5 mb-6 rounded-full bg-[#F7E6E2] border border-[#FF135F]/20 text-[#FF135F] text-sm font-medium">
            <span class="flex h-2 w-2 mr-2">
              <span class="animate-ping absolute h-2 w-2 rounded-full bg-[#FF135F] opacity-75"></span>
              <span class="relative rounded-full h-2 w-2 bg-[#FF135F]"></span>
            </span>
            Nouveau en 2025
          </div>
          
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-[#2E2E38] leading-tight mb-6">
            <span class="hero-title inline-block text-[#FF135F] animate-fadeInUp" style="animation-delay: 0.1s">Match</span>
            <span class="hero-title inline-block animate-fadeInUp" style="animation-delay: 0.2s"> votre séjour,</span>
            <br />
            <span class="hero-title inline-block text-[#FF135F] animate-fadeInUp" style="animation-delay: 0.3s">Room</span>
            <span class="hero-title inline-block animate-fadeInUp" style="animation-delay: 0.4s"> pour négocier</span>
          </h1>
          
          <p class="hero-description text-xl text-[#2E2E38]/80 mb-8 max-w-lg animate-fadeInUp" style="animation-delay: 0.5s">
            La plateforme innovante où les voyageurs proposent leur prix et les hôtels
            décident. Trouvez votre séjour parfait tout en gardant le contrôle de votre budget.
          </p>
          
          <!-- Boutons d'action avec design 2025 -->
          <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="/register" 
               class="hero-cta bg-[#FF135F] text-white px-8 py-4 rounded-xl text-lg font-medium transition-all duration-300 shadow-lg shadow-[#FF135F]/20 inline-flex items-center justify-center animate-fadeInUp hover:shadow-xl hover:shadow-[#FF135F]/30 hover:-translate-y-1" 
               style="animation-delay: 0.6s">
              Commencer
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="#how-it-works" 
               class="hero-cta bg-white text-[#2E2E38] px-8 py-4 rounded-xl text-lg font-medium border border-[#E9E1DC] transition-all duration-300 shadow-md inline-flex items-center justify-center animate-fadeInUp hover:bg-[#F7E6E2] hover:shadow-lg" 
               style="animation-delay: 0.7s">
              Comment ça marche
            </a>
          </div>
          
          <!-- Badges de confiance avec design moderne -->
          <div class="mt-10 flex items-center text-[#2E2E38]/80 animate-fadeInUp" style="animation-delay: 0.8s">
            <span class="flex items-center mr-6 group">
              <span class="w-8 h-8 rounded-full bg-[#F7E6E2] flex items-center justify-center mr-2 group-hover:scale-110 transition-transform text-[#FF135F]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </span>
              <span class="group-hover:text-[#FF135F] transition-colors">Sans frais de commission</span>
            </span>
            <span class="flex items-center group">
              <span class="w-8 h-8 rounded-full bg-[#F7E6E2] flex items-center justify-center mr-2 group-hover:scale-110 transition-transform text-[#FF135F]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </span>
              <span class="group-hover:text-[#FF135F] transition-colors">Prix transparents</span>
            </span>
          </div>
        </div>
        
        <!-- Démo interactive de négociation avec design 2025 -->
        <div class="md:col-span-6 z-10">
          <div class="relative bg-white/70 backdrop-blur-xl p-8 rounded-2xl shadow-2xl shadow-[#2E2E38]/5 border border-[#E9E1DC] overflow-hidden">
            <!-- Effet de brillance -->
            <div class="absolute -inset-0.5 bg-gradient-to-r from-[#FF135F] to-[#FF135F]/70 rounded-2xl blur opacity-20 group-hover:opacity-30 transition duration-1000 group-hover:duration-200 animate-pulse"></div>
            
            <!-- Contenu de la démo -->
            <div class="relative">
              <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-[#2E2E38]">Négociation en direct</h3>
                <div class="text-sm font-medium text-[#2E2E38]/60">Étape {{ negotiationStep.value }}/3</div>
              </div>
              
              <!-- Carte d'hôtel avec design moderne -->
              <div class="bg-white rounded-xl shadow-lg p-4 mb-6 hotel-room-card">
                <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden mb-4">
                  <img src="/images/hotel-room.jpg" alt="Chambre d'hôtel" class="object-cover w-full h-full" 
                       onerror="this.src='https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1470&q=80'"/>
                </div>
                <h4 class="text-lg font-bold text-[#2E2E38] mb-1">Hôtel Le Magnifique</h4>
                <div class="flex items-center mb-2">
                  <div class="flex text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="text-sm text-[#2E2E38]/60 ml-2">5.0 (128 avis)</span>
                </div>
                <p class="text-[#2E2E38]/70 text-sm mb-4">Chambre Deluxe avec vue sur la ville, petit-déjeuner inclus</p>
                <div class="flex justify-between items-center">
                  <div>
                    <div class="text-sm text-[#2E2E38]/60">Prix affiché</div>
                    <div class="text-xl font-bold text-[#2E2E38]">{{ originalPrice }}€</div>
                  </div>
                  <div class="text-right">
                    <div class="text-sm text-[#2E2E38]/60">Votre offre</div>
                    <div class="text-xl font-bold text-[#FF135F]">{{ negotiationPrice.value }}€</div>
                  </div>
                </div>
              </div>
              
              <!-- Interface de négociation avec design 2025 -->
              <div class="space-y-4">
                <div class="relative pt-6">
                  <div class="flex justify-between mb-2">
                    <span class="text-sm font-medium text-[#2E2E38]">Économie: {{ negotiationPercentage }}%</span>
                    <span class="text-sm font-medium text-[#FF135F]">{{ originalPrice - negotiationPrice.value }}€</span>
                  </div>
                  <div class="h-2 bg-[#E9E1DC] rounded-full overflow-hidden">
                    <div class="h-full bg-[#FF135F] rounded-full transition-all duration-1000"
                         :style="{ width: `${negotiationPercentage}%` }"></div>
                  </div>
                </div>
                
                <div v-if="!negotiationComplete">
                  <button @click="animateNegotiation" 
                          class="w-full py-4 px-6 bg-[#FF135F] text-white rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:shadow-[#FF135F]/20 flex items-center justify-center">
                    <span v-if="negotiationStep === 0">Faire une offre</span>
                    <span v-else-if="negotiationStep === 1">Contre-offre reçue</span>
                    <span v-else-if="negotiationStep === 2">Accepter l'offre</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
                
                <div v-else class="text-center p-4 bg-[#F7E6E2] rounded-xl border border-[#FF135F]/20">
                  <div class="text-[#FF135F] mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                  </div>
                  <h4 class="text-lg font-bold text-[#2E2E38] mb-1">Négociation réussie!</h4>
                  <p class="text-[#2E2E38]/70 mb-4">Vous avez économisé {{ originalPrice - negotiationPrice.value }}€ ({{ negotiationPercentage }}%)</p>
                  <button @click="animateNegotiation" 
                          class="py-2 px-4 bg-[#FF135F] text-white rounded-lg font-medium transition-all duration-300 hover:bg-[#FF135F]/90">
                    Réessayer
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Section Fonctionnalités avec design 2025 -->
    <section id="features" class="py-24 px-6 bg-white relative">
      <!-- Arrière-plan moderne -->
      <div class="absolute inset-0 overflow-hidden -z-10">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGZpbGw9IiNmOGZhZmMiIGQ9Ik0wIDBoNjB2NjBIMHoiLz48cGF0aCBkPSJNNjAgMzBjMCAxNi41NjktMTMuNDMxIDMwLTMwIDMwQzEzLjQzMSA2MCAwIDQ2LjU2OSAwIDMwIDAgMTMuNDMxIDEzLjQzMSAwIDMwIDBjMTYuNTY5IDAgMzAgMTMuNDMxIDMwIDMweiIgc3Ryb2tlPSIjZTJlOGYwIiBzdHJva2Utd2lkdGg9Ii41Ii8+PC9nPjwvc3ZnPg==')] opacity-[0.03]"></div>
      </div>
      
      <div class="container mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center px-3 py-1.5 mb-6 rounded-full bg-[#F7E6E2] border border-[#FF135F]/20 text-[#FF135F] text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd" />
            </svg>
            Fonctionnalités exclusives
          </div>
          <h2 class="section-title text-3xl md:text-4xl font-bold text-[#2E2E38] mb-4 opacity-0 translate-y-8">
            Une nouvelle façon de réserver
          </h2>
          <p class="section-description text-xl text-[#2E2E38]/70 opacity-0 translate-y-8">
            MatchRoom révolutionne la réservation d'hôtels en vous donnant le pouvoir de négocier directement avec les établissements.
          </p>
        </div>
        
        <!-- Cartes de fonctionnalités avec design 2025 -->
        <div class="grid md:grid-cols-3 gap-8">
          <div v-for="(feature, index) in features" :key="index" 
               class="animate-item bg-white rounded-2xl p-8 shadow-xl shadow-[#2E2E38]/5 border border-[#E9E1DC] hover:shadow-2xl hover:shadow-[#2E2E38]/10 transition-all duration-500 hover:-translate-y-2 opacity-0 translate-y-8 group"
               :style="{ 'transition-delay': `${index * 0.1}s` }">
            <div class="bg-[#FF135F] text-white p-4 rounded-xl inline-flex mb-6 transform transition-transform duration-500 group-hover:rotate-6 group-hover:scale-110">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-[#2E2E38] mb-4 group-hover:text-[#FF135F] transition-colors duration-300">{{ feature.title }}</h3>
            <p class="text-[#2E2E38]/70">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Section Comment ça marche avec design 2025 -->
    <section id="how-it-works" class="py-24 px-6 bg-[#F4F3F3] relative">
      <!-- Ligne de progression moderne -->
      <div class="absolute top-1/2 left-0 w-full transform -translate-y-1/2">
        <div class="container mx-auto">
          <div class="w-full h-0.5 bg-[#E9E1DC] relative">
            <div class="absolute left-0 top-0 h-full bg-[#FF135F] transition-all duration-1000"
                 :class="{'w-0': currentSection !== 'how-it-works', 'w-full': currentSection === 'how-it-works'}"></div>
          </div>
        </div>
      </div>
      
      <div class="container mx-auto relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center px-3 py-1.5 mb-6 rounded-full bg-[#F7E6E2] border border-[#FF135F]/20 text-[#FF135F] text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M6.672 1.911a1 1 0 10-1.932.518l.259.966a1 1 0 001.932-.518l-.26-.966zM2.429 4.74a1 1 0 10-.517 1.932l.966.259a1 1 0 00.517-1.932l-.966-.26zm8.814-.569a1 1 0 00-1.415-1.414l-.707.707a1 1 0 101.415 1.415l.707-.708zm-7.071 7.072l.707-.707A1 1 0 003.465 9.12l-.708.707a1 1 0 001.415 1.415zm3.2-5.171a1 1 0 00-1.3 1.3l4 10a1 1 0 001.823.075l1.38-2.759 3.018 3.02a1 1 0 001.414-1.415l-3.019-3.02 2.76-1.379a1 1 0 00-.076-1.822l-10-4z" clip-rule="evenodd" />
            </svg>
            Processus simple
          </div>
          <h2 class="section-title text-3xl md:text-4xl font-bold text-[#2E2E38] mb-4 opacity-0 translate-y-8">Comment fonctionne MatchRoom</h2>
          <p class="section-description text-xl text-[#2E2E38]/70 opacity-0 translate-y-8">
            Un processus simple en trois étapes qui révolutionne la façon dont vous réservez votre prochain séjour.
          </p>
        </div>
        
        <!-- Étapes avec design 2025 -->
        <div class="grid md:grid-cols-3 gap-12">
          <div v-for="(step, index) in [
            {
              title: 'Recherchez & Proposez',
              description: 'Trouvez votre hébergement idéal et proposez un prix qui vous convient.',
              icon: 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'
            },
            {
              title: 'Négociation',
              description: 'Les hôtels examinent votre offre et peuvent accepter, contre-proposer ou refuser en quelques heures.',
              icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'
            },
            {
              title: 'Réservez & Profitez',
              description: 'Une fois l\'accord conclu, finalisez votre réservation avec un paiement sécurisé.',
              icon: 'M5 13l4 4L19 7'
            }
          ]" :key="index" class="text-center animate-item opacity-0 translate-y-8" :style="{ 'transition-delay': `${index * 0.2}s` }">
            <div class="relative">
              <!-- Cercle avec effet de brillance -->
              <div class="absolute inset-0 bg-[#FF135F] rounded-full blur-md opacity-20 animate-pulse"></div>
              <div class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6 shadow-xl relative z-10 transform transition-transform duration-500 hover:scale-110 hover:shadow-2xl hover:shadow-[#FF135F]/20">
                <span class="text-2xl font-bold text-[#FF135F]">{{ index + 1 }}</span>
              </div>
            </div>
            <h3 class="text-xl font-bold text-[#2E2E38] mb-4">{{ step.title }}</h3>
            <p class="text-[#2E2E38]/70">
              {{ step.description }}
            </p>
          </div>
        </div>
        
        <!-- CTA avec design 2025 -->
        <div class="mt-16 text-center animate-item opacity-0 translate-y-8" style="transition-delay: 0.6s">
          <a href="/register" 
             class="bg-[#FF135F] text-white px-8 py-4 rounded-xl text-lg font-medium transition-all duration-300 shadow-lg shadow-[#FF135F]/20 inline-flex items-center justify-center hover:shadow-xl hover:shadow-[#FF135F]/30 hover:-translate-y-1">
            Démarrez votre première négociation
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </a>
        </div>
      </div>
    </section>
    
    <!-- Section Destinations Populaires avec design 2025 -->
    <section id="cities" class="py-24 px-6 bg-white">
      <div class="container mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center px-3 py-1.5 mb-6 rounded-full bg-[#F7E6E2] border border-[#FF135F]/20 text-[#FF135F] text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
            </svg>
            Destinations populaires
          </div>
          <h2 class="section-title text-3xl md:text-4xl font-bold text-[#2E2E38] mb-4 opacity-0 translate-y-8">Explorez le monde</h2>
          <p class="section-description text-xl text-[#2E2E38]/70 opacity-0 translate-y-8">
            Découvrez les meilleurs hôtels avec des prix négociables dans ces destinations populaires.
          </p>
        </div>
        
        <!-- Filtres avec design 2025 -->
        <div class="flex justify-center mb-12 space-x-4 overflow-x-auto animate-item opacity-0 translate-y-8">
          <button 
            v-for="(type, index) in ['all', 'luxury', 'budget', 'family']" 
            :key="index"
            @click="hotelTypePreference = type" 
            class="px-6 py-2.5 rounded-xl text-sm font-medium transition-all duration-300 flex items-center"
            :class="hotelTypePreference === type ? 'bg-[#FF135F] text-white shadow-md shadow-[#FF135F]/20' : 'bg-[#E9E1DC] text-[#2E2E38] hover:bg-[#D6E1E5]'">
            <svg v-if="type === 'all'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
            </svg>
            <svg v-else-if="type === 'luxury'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <svg v-else-if="type === 'budget'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
            </svg>
            {{ {'all': 'Tous les hôtels', 'luxury': 'Luxe', 'budget': 'Petit budget', 'family': 'Famille'}[type] }}
          </button>
        </div>
        
        <!-- Grille de villes avec design 2025 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(city, index) in popularCities" :key="index"
               class="animate-item opacity-0 translate-y-8 group relative h-80 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 hover:-translate-y-2"
               :style="{ 'transition-delay': `${index * 0.1}s` }">
            <!-- Image d'arrière-plan avec effet de zoom -->
            <div class="absolute inset-0 bg-[#D6E1E5] bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                 :style="{ backgroundImage: `url('/images/Paris.webp')` }"></div>
            
            <!-- Superposition dégradée moderne -->
            <div class="absolute inset-0 bg-gradient-to-t from-[#0E2748] via-[#0E2748]/60 to-transparent"></div>
            
            <!-- Badge de statut moderne -->
            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-[#FF135F] shadow-lg">
              {{ city.count }} établissements
            </div>
            
            <!-- Contenu avec typographie moderne -->
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
              <h3 class="text-2xl font-bold mb-1 transform transition-all duration-300 group-hover:translate-x-2">{{ city.name }}</h3>
              <p class="flex items-center text-white/80 transform transition-all duration-300 group-hover:translate-x-2" style="transition-delay: 0.1s">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                Meilleurs prix négociés
              </p>
              
              <!-- Bouton Explorer avec animation slide-in -->
              <button class="mt-4 px-5 py-2 bg-[#FF135F] rounded-lg text-sm font-medium group-hover:opacity-100 transition-all duration-500 opacity-0 transform translate-y-4 group-hover:translate-y-0 shadow-lg shadow-[#FF135F]/20">
                Explorer les hôtels
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Section Témoignages avec design 2025 -->
    <section id="testimonials" class="py-24 px-6 bg-[#F4F3F3]">
      <div class="container mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <div class="inline-flex items-center px-3 py-1.5 mb-6 rounded-full bg-[#F7E6E2] border border-[#FF135F]/20 text-[#FF135F] text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd" />
            </svg>
            Témoignages clients
          </div>
          <h2 class="section-title text-3xl md:text-4xl font-bold text-[#2E2E38] mb-4 opacity-0 translate-y-8">Ce que disent nos utilisateurs</h2>
          <p class="section-description text-xl text-[#2E2E38]/70 opacity-0 translate-y-8">
            Rejoignez des milliers de voyageurs et d'hôteliers satisfaits qui ont adopté l'expérience MatchRoom.
          </p>
        </div>
        
        <!-- Carte de témoignage avec design 2025 -->
        <div class="relative max-w-4xl mx-auto animate-item opacity-0 translate-y-8">
          <div class="rounded-2xl bg-white/70 backdrop-blur-xl p-8 md:p-12 shadow-2xl shadow-[#2E2E38]/5 border border-[#E9E1DC] transition-all duration-500 hover:shadow-[#2E2E38]/10">
            <!-- Icône de citation moderne -->
            <div class="text-[#FF135F] mb-6 transform transition-transform duration-500 hover:scale-110 hover:rotate-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
              </svg>
            </div>
            
            <!-- Contenu du témoignage avec animation de transition -->
            <div class="min-h-[200px]">
              <transition name="fade" mode="out-in">
                <div :key="activeTestimonial" class="animate-fadeIn">
                  <p class="text-xl md:text-2xl text-[#2E2E38] mb-8 italic leading-relaxed">
                    "{{ testimonials[activeTestimonial].quote }}"
                  </p>
                  <div class="flex items-center">
                    <!-- Avatar moderne -->
                    <div class="w-14 h-14 bg-[#FF135F] rounded-full mr-4 flex items-center justify-center text-white overflow-hidden shadow-lg">
                      <span class="text-xl font-bold">{{ testimonials[activeTestimonial].author.charAt(0) }}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-[#2E2E38]">{{ testimonials[activeTestimonial].author }}</h4>
                      <p class="text-[#2E2E38]/70">{{ testimonials[activeTestimonial].role }}</p>
                    </div>
                  </div>
                </div>
              </transition>
            </div>
            
            <!-- Navigation de témoignage avec design 2025 -->
            <div class="flex justify-center mt-8 space-x-3">
              <button 
                v-for="(_, index) in testimonials" 
                :key="index"
                @click="activeTestimonial = index"
                class="w-3 h-3 rounded-full transition-all duration-300 transform hover:scale-150"
                :class="activeTestimonial === index ? 'bg-[#FF135F] scale-125' : 'bg-[#E9E1DC]'"
              ></button>
            </div>
          </div>
          
          <!-- Éléments décoratifs modernes -->
          <div class="absolute -top-6 -right-6 w-12 h-12 rounded-full bg-[#F7E6E2] animate-ping opacity-70" style="animation-duration: 3s"></div>
          <div class="absolute -bottom-6 -left-6 w-12 h-12 rounded-full bg-[#D6E1E5] animate-ping opacity-70" style="animation-duration: 4s; animation-delay: 1s"></div>
        </div>
      </div>
    </section>
    
    <!-- Section CTA avec design 2025 -->
    <section id="cta" class="py-24 px-6 bg-gradient-to-r from-[#0E2748] to-[#2E2E38] text-white relative overflow-hidden">
      <!-- Arrière-plans animés modernes -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute w-96 h-96 -top-48 -right-48 rounded-full bg-white/5 animate-blob"></div>
        <div class="absolute w-96 h-96 -bottom-48 -left-48 rounded-full bg-white/5 animate-blob" style="animation-delay: 2s"></div>
        
        <!-- Grille moderne -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGZpbGw9IiMwZjE3MmEiIGQ9Ik0wIDBoNjB2NjBIMHoiLz48cGF0aCBkPSJNNjAgMzBjMCAxNi41NjktMTMuNDMxIDMwLTMwIDMwQzEzLjQzMSA2MCAwIDQ2LjU2OSAwIDMwIDAgMTMuNDMxIDEzLjQzMSAwIDMwIDBjMTYuNTY5IDAgMzAgMTMuNDMxIDMwIDMweiIgc3Ryb2tlPSIjMTUyMDNkIiBzdHJva2Utd2lkdGg9Ii41Ii8+PC9nPjwvc3ZnPg==')] opacity-[0.1]"></div>
      </div>
      
      <div class="container mx-auto relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <div class="inline-flex items-center px-3 py-1.5 mb-6 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white text-sm font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd" />
            </svg>
            Offre limitée
          </div>
          <h2 class="section-title text-3xl md:text-4xl font-bold mb-6 opacity-0 translate-y-8">Prêt à révolutionner votre expérience de voyage ?</h2>
          <p class="section-description text-xl mb-8 text-white/90 opacity-0 translate-y-8">
            Rejoignez MatchRoom aujourd'hui et prenez le contrôle de votre budget d'hébergement.
          </p>
          
          <!-- Boutons d'action avec design 2025 -->
          <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center">
            <a href="/register" 
               class="animate-item bg-[#FF135F] text-white px-8 py-4 rounded-xl text-lg font-medium transition-all duration-300 shadow-lg shadow-[#FF135F]/20 inline-flex items-center justify-center hover:shadow-xl hover:shadow-[#FF135F]/30 hover:-translate-y-1 opacity-0 translate-y-8">
              Inscription gratuite
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="/login" 
               class="animate-item bg-white/10 backdrop-blur-sm text-white border border-white/20 px-8 py-4 rounded-xl text-lg font-medium transition-all duration-300 inline-flex items-center justify-center hover:bg-white/20 hover:shadow-lg opacity-0 translate-y-8" 
               style="transition-delay: 0.2s">
              Connexion
            </a>
          </div>
          
          <!-- Compteurs statistiques avec design 2025 -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
            <div v-for="(stat, index) in [
              { value: '25k+', label: 'Utilisateurs satisfaits' },
              { value: '5 000+', label: 'Hôtels partenaires' },
              { value: '2M+ €', label: 'Économies générées' }
            ]" :key="index" class="animate-item bg-white/10 backdrop-blur-sm rounded-xl p-6 opacity-0 translate-y-8 border border-white/10 hover:bg-white/20 transition-all duration-300" :style="{ 'transition-delay': `${0.3 + index * 0.1}s` }">
              <div class="text-4xl font-bold mb-2 text-[#FF135F]">{{ stat.value }}</div>
              <div class="text-white/80">{{ stat.label }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Pied de page avec design 2025 -->
    <footer class="bg-[#2E2E38] text-white py-16 px-6">
      <div class="container mx-auto">
        <div class="grid md:grid-cols-4 gap-10 mb-12">
          <div>
            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/logoMR-TakbHOAaljFXg5P560GFVsP1M2q0Tx.png" alt="Logo MatchRoom" class="h-10 mb-4 transform transition-transform duration-300 hover:scale-105" />
            <p class="text-[#E9E1DC] mb-6">
              La plateforme innovante pour la négociation des chambres d'hôtel.
            </p>
            <div class="flex space-x-4">
              <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-[#FF135F] transition-all duration-300">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                </svg>
              </a>
              <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-[#FF135F] transition-all duration-300">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                </svg>
              </a>
              <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white hover:bg-[#FF135F] transition-all duration-300">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                  <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                </svg>
              </a>
            </div>
          </div>
          
          <!-- Liens de navigation avec design 2025 -->
          <div v-for="(column, colIndex) in [
            {
              title: 'Plateforme',
              links: [
                { label: 'Comment ça marche', href: '#how-it-works' },
                { label: 'Fonctionnalités', href: '#features' },
                { label: 'Tarification', href: '#' },
                { label: 'Pour les hôtels', href: '#' }
              ]
            },
            {
              title: 'Entreprise',
              links: [
                { label: 'À propos de nous', href: '#' },
                { label: 'Carrières', href: '#' },
                { label: 'Blog', href: '#' },
                { label: 'Presse', href: '#' }
              ]
            },
            {
              title: 'Support',
              links: [
                { label: 'Nous contacter', href: '#' },
                { label: 'FAQ', href: '#' },
                { label: 'Conditions d\'utilisation', href: '#' },
                { label: 'Politique de confidentialité', href: '#' }
              ]
            }
          ]" :key="colIndex">
            <h3 class="font-bold text-lg mb-6">{{ column.title }}</h3>
            <ul class="space-y-4">
              <li v-for="(link, linkIndex) in column.links" :key="linkIndex">
                <a :href="link.href" 
                   class="text-[#E9E1DC] inline-flex items-center transition-all duration-300 hover:text-[#FF135F] hover:translate-x-1 group">
                  <span class="w-0 h-0.5 bg-[#FF135F] mr-0 opacity-0 transition-all duration-300 group-hover:w-2 group-hover:mr-2 group-hover:opacity-100"></span>
                  {{ link.label }}
                </a>
              </li>
            </ul>
          </div>
        </div>
        
        <!-- Pied de page avec design 2025 -->
        <div class="pt-8 border-t border-[#0E2748] flex flex-col md:flex-row justify-between items-center">
          <p class="text-[#E9E1DC]/70">© 2025 MatchRoom. Tous droits réservés.</p>
          <div class="mt-4 md:mt-0 text-[#E9E1DC]/70 text-sm">
            Conçu avec passion pour les voyageurs modernes
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style>
/* Transitions de base */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Animations modernes 2025 */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes blob {
  0% {
    transform: scale(1) translate(0, 0);
  }
  33% {
    transform: scale(1.1) translate(10%, 10%);
  }
  66% {
    transform: scale(0.9) translate(-10%, 10%);
  }
  100% {
    transform: scale(1) translate(0, 0);
  }
}

/* Appliquer les animations */
.animate-fadeInUp {
  animation: fadeInUp 0.8s ease forwards;
  opacity: 0;
}

.animate-fadeIn {
  animation: fadeIn 0.8s ease forwards;
}

.animate-slideDown {
  animation: slideDown 0.6s ease forwards;
}

.animate-blob {
  animation: blob 7s infinite;
}

/* Effet d'animation au défilement */
.animate-on-scroll {
  opacity: 0;
  transition: opacity 0.8s ease-out, transform 0.8s ease-out;
  transform: translateY(40px);
}

.animate-on-scroll.is-visible {
  opacity: 1;
  transform: translateY(0);
}

/* Animer les éléments lorsque la section devient active */
.animate-item {
  transition: opacity 0.5s ease, transform 0.5s ease;
}

#features.active .animate-item,
#how-it-works.active .animate-item,
#cities.active .animate-item,
#testimonials.active .animate-item,
#cta.active .animate-item {
  opacity: 1 !important;
  transform: translateY(0) !important;
}

/* Animations des titres et descriptions de section */
#features.active .section-title,
#how-it-works.active .section-title,
#cities.active .section-title,
#testimonials.active .section-title,
#cta.active .section-title {
  opacity: 1 !important;
  transform: translateY(0) !important;
  transition: opacity 0.8s ease, transform 0.8s ease;
}

#features.active .section-description,
#how-it-works.active .section-description,
#cities.active .section-description,
#testimonials.active .section-description,
#cta.active .section-description {
  opacity: 1 !important;
  transform: translateY(0) !important;
  transition: opacity 0.8s ease 0.2s, transform 0.8s ease 0.2s;
}

/* Effet de survol des éléments interactifs */
.interactive-element {
  transition: all 0.3s ease;
}

.interactive-element:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>