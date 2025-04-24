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
  
  <!-- Curseur personnalisé pour les éléments interactifs -->
  <div v-if="cursorVisible" 
       class="fixed w-12 h-12 pointer-events-none z-50 mix-blend-difference transition-transform duration-200 ease-out"
       :style="{
         transform: `translate(${mousePosition.x - 24}px, ${mousePosition.y - 24}px) scale(${cursorVisible ? 1 : 0})`
       }">
    <div class="absolute inset-0 bg-white rounded-full opacity-30 animate-ping"></div>
    <div class="absolute inset-3 bg-white rounded-full"></div>
  </div>
  
  <div class="min-h-screen bg-neutral-50 overflow-x-hidden">
    <!-- En-tête/Navigation avec animation -->
    <header 
      class="fixed w-full bg-white/90 backdrop-blur-sm z-50 shadow-sm transition-all duration-300"
      :class="{ 'py-2': scrollY > 100, 'py-4': scrollY <= 100 }">
      <div class="container mx-auto px-6 flex justify-between items-center">
        <div class="flex items-center">
          <img src="/images/logoMR.png" alt="Logo MatchRoom" 
               class="h-10 transition-transform duration-300 hover:scale-105" />
        </div>
        
        <!-- Bouton menu mobile -->
        <button @click="isNavOpen = !isNavOpen" class="md:hidden text-gray-700 focus:outline-none">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="!isNavOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        
        <nav class="hidden md:flex space-x-8 text-gray-700">
          <a href="#features" 
             class="font-medium relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-pink-500 hover:after:w-full after:transition-all after:duration-300">
            Fonctionnalités
          </a>
          <a href="#how-it-works" 
             class="font-medium relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-pink-500 hover:after:w-full after:transition-all after:duration-300">
            Comment ça marche
          </a>
          <a href="#cities" 
             class="font-medium relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-pink-500 hover:after:w-full after:transition-all after:duration-300">
            Destinations populaires
          </a>
          <a href="#testimonials" 
             class="font-medium relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-pink-500 hover:after:w-full after:transition-all after:duration-300">
            Témoignages
          </a>
        </nav>
        
        <!-- Menu mobile -->
        <div v-if="isNavOpen" 
             class="absolute top-full left-0 right-0 bg-white shadow-lg z-50 md:hidden p-4 animate-fadeDown">
          <nav class="flex flex-col space-y-4">
            <a href="#features" 
               @click="isNavOpen = false"
               class="font-medium py-2 px-4 hover:bg-gray-100 rounded-lg transition">
              Fonctionnalités
            </a>
            <a href="#how-it-works" 
               @click="isNavOpen = false"
               class="font-medium py-2 px-4 hover:bg-gray-100 rounded-lg transition">
              Comment ça marche
            </a>
            <a href="#cities" 
               @click="isNavOpen = false"
               class="font-medium py-2 px-4 hover:bg-gray-100 rounded-lg transition">
              Destinations populaires
            </a>
            <a href="#testimonials" 
               @click="isNavOpen = false"
               class="font-medium py-2 px-4 hover:bg-gray-100 rounded-lg transition">
              Témoignages
            </a>
            <div class="pt-2 border-t border-gray-200">
              <a href="/login" class="block w-full py-2 px-4 text-center font-medium text-gray-700 hover:bg-gray-100 rounded-lg transition mb-2">
                Connexion
              </a>
              <a href="/register" class="block w-full py-2 px-4 text-center font-medium bg-pink-500 text-white hover:bg-pink-600 rounded-lg transition">
                Inscription
              </a>
            </div>
          </nav>
        </div>
        
        <div class="hidden md:flex space-x-4">
          <a href="/login" 
             class="font-medium text-gray-700 px-4 py-2 rounded-lg transition-all duration-300 hover:bg-gray-100 hover:shadow-md">
            Connexion
          </a>
          <a href="/register" 
             class="font-medium bg-pink-500 text-white px-6 py-2 rounded-lg transition-all duration-300 hover:bg-pink-600 hover:shadow-xl hover:-translate-y-0.5">
            Inscription
          </a>
        </div>
      </div>
    </header>
    
    <!-- Section Hero avec animations améliorées -->
    <section class="relative pt-32 pb-20 px-6 md:min-h-[90vh] flex items-center" id="hero">
      <!-- Éléments de fond animés -->
      <div class="absolute inset-0 overflow-hidden -z-10">
        <div class="parallax-bg absolute w-96 h-96 rounded-full bg-pink-100/50 -top-20 -left-20 blur-3xl" data-depth="0.2"></div>
        <div class="parallax-bg absolute w-96 h-96 rounded-full bg-blue-100/50 bottom-20 right-20 blur-3xl" data-depth="0.3"></div>
        <div class="absolute top-1/4 left-1/3 w-8 h-8 rounded-full bg-pink-300/30 animate-ping" style="animation-duration: 3s; animation-delay: 0.5s"></div>
        <div class="absolute bottom-1/3 right-1/4 w-6 h-6 rounded-full bg-blue-300/30 animate-ping" style="animation-duration: 4s; animation-delay: 1s"></div>
      </div>
      
      <div class="container mx-auto grid md:grid-cols-2 gap-12 items-center">
        <div class="z-10">
          <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6">
            <span class="hero-title inline-block text-pink-500 animate-fadeInUp" style="animation-delay: 0.1s">Match</span>
            <span class="hero-title inline-block animate-fadeInUp" style="animation-delay: 0.2s"> Votre Séjour,</span>
            <br />
            <span class="hero-title inline-block text-pink-500 animate-fadeInUp" style="animation-delay: 0.3s">Room</span>
            <span class="hero-title inline-block animate-fadeInUp" style="animation-delay: 0.4s"> Pour Négocier</span>
          </h1>
          <p class="hero-description text-xl text-gray-700 mb-8 max-w-lg animate-fadeInUp" style="animation-delay: 0.5s">
            La plateforme innovante où les voyageurs proposent leur prix et les hôtels
            décident. Trouvez votre séjour parfait tout en gardant le contrôle de votre budget.
          </p>
          <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
            <a href="/register" 
               class="hero-cta bg-pink-500 text-white px-8 py-3 rounded-lg text-lg font-medium transition-all duration-300 shadow-lg inline-flex items-center justify-center animate-fadeInUp hover:bg-pink-600 hover:shadow-xl hover:-translate-y-1" 
               style="animation-delay: 0.6s">
              Commencer
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="#how-it-works" 
               class="hero-cta bg-white text-gray-800 px-8 py-3 rounded-lg text-lg font-medium border border-gray-200 transition-all duration-300 shadow-md inline-flex items-center justify-center animate-fadeInUp hover:bg-gray-50 hover:shadow-lg" 
               style="animation-delay: 0.7s">
              Comment ça marche
            </a>
          </div>
          <div class="mt-8 flex items-center text-gray-600 animate-fadeInUp" style="animation-delay: 0.8s">
            <span class="flex items-center mr-6 group">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-500 mr-2 group-hover:scale-110 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="group-hover:text-pink-500 transition-colors">Sans frais de commission</span>
            </span>
            <span class="flex items-center group">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-500 mr-2 group-hover:scale-110 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="group-hover:text-pink-500 transition-colors">Prix transparents</span>
            </span>
          </div>
        </div>
        
        <!-- Carte d'hôtel interactive avec effet parallaxe -->
        <div class="relative z-10" id="hotel-card-container">
          <div class="absolute -inset-0.5 bg-gradient-to-r from-pink-500 to-blue-800 rounded-2xl blur opacity-20 animate-pulse"></div>
          <div class="relative bg-white rounded-2xl shadow-xl overflow-hidden hotel-room-card">
            <img src="/images/hotel-room.jpg" alt="Chambre d'hôtel" class="w-full h-auto transform transition-transform duration-700 hover:scale-110" 
                 :class="{ 'opacity-0': !heroImageLoaded }" />
                 
            <!-- Carte de prix interactive -->
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-gray-900/80 to-transparent p-6">
              <div class="bg-white/90 backdrop-blur-sm rounded-xl p-4 shadow-lg max-w-xs mx-auto hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                <div class="flex justify-between items-center mb-3">
                  <div>
                    <h3 class="font-bold text-gray-900">Suite de Luxe</h3>
                    <p class="text-sm text-gray-600">Paris, France</p>
                  </div>
                  <div class="bg-pink-500 text-white px-3 py-1 rounded-lg text-sm font-medium animate-pulse">
                    -{{ negotiationPercentage }}%
                  </div>
                </div>
                
                <!-- Démo de négociation de prix interactive -->
                <div class="mb-3 flex items-center">
                  <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-pink-500 h-2.5 rounded-full transition-all duration-1000" 
                         :style="{ width: `${100 - negotiationPercentage}%` }"></div>
                  </div>
                </div>
                
                <div class="flex justify-between items-center">
                  <div class="flex items-center">
                    <span class="text-gray-500 line-through mr-2">€{{ originalPrice }}</span>
                    <span class="text-lg font-bold text-gray-900">€{{ negotiationPrice }}</span>
                  </div>
                  <button @click="animateNegotiation" 
                          class="bg-blue-800 text-white px-3 py-1 rounded-lg text-sm font-medium transition-all duration-300 hover:bg-blue-900 hover:shadow-lg hover:-translate-y-0.5">
                    {{ negotiationStep === 0 ? 'Faire une offre' : 
                       negotiationStep === 1 ? 'Contre-offre' : 
                       negotiationStep === 2 ? 'Accepter' : 'Recommencer' }}
                  </button>
                </div>
                
                <!-- Message de succès après négociation -->
                <div v-if="negotiationComplete" 
                     class="mt-3 text-xs text-green-600 animate-fadeIn">
                  Négociation réussie ! Économisez €{{ originalPrice - negotiationPrice }} sur votre séjour.
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Section Fonctionnalités avec animations améliorées -->
    <section id="features" class="py-20 px-6 bg-white relative overflow-hidden">
      <!-- Éléments décoratifs -->
      <div class="absolute top-0 left-0 w-full h-20 bg-gradient-to-b from-neutral-50 to-transparent"></div>
      <div class="absolute -left-32 top-40 w-64 h-64 rounded-full bg-pink-100/50 blur-3xl"></div>
      <div class="absolute -right-32 bottom-40 w-64 h-64 rounded-full bg-blue-100/50 blur-3xl"></div>
      
      <div class="container mx-auto relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="section-title text-3xl md:text-4xl font-bold text-gray-900 mb-4 opacity-0 translate-y-8">Pourquoi choisir MatchRoom ?</h2>
          <p class="section-description text-xl text-gray-700 opacity-0 translate-y-8">
            Notre plateforme vous donne un contrôle sans précédent sur votre budget d'hébergement
            tout en aidant les hôtels à optimiser leur occupation.
          </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-8">
          <div v-for="(feature, index) in features" :key="index" 
               class="animate-item bg-gray-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-500 hover:-translate-y-2 opacity-0 translate-y-8"
               :style="{ 'transition-delay': `${index * 0.1}s` }">
            <div class="bg-pink-500 text-white p-4 rounded-xl inline-flex mb-6 transform transition-transform duration-500 hover:rotate-12 hover:scale-110">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="feature.icon" />
              </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">{{ feature.title }}</h3>
            <p class="text-gray-700">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Section Comment ça marche avec animation interactive -->
    <section id="how-it-works" class="py-20 px-6 bg-gray-50 relative">
      <div class="absolute top-1/2 left-0 w-full transform -translate-y-1/2">
        <div class="container mx-auto">
          <div class="w-full h-0.5 bg-gray-200 relative">
            <!-- Ligne de progression animée -->
            <div class="absolute left-0 top-0 h-full bg-pink-500 transition-all duration-1000"
                 :class="{'w-0': currentSection !== 'how-it-works', 'w-full': currentSection === 'how-it-works'}"></div>
          </div>
        </div>
      </div>
      
      <div class="container mx-auto relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="section-title text-3xl md:text-4xl font-bold text-gray-900 mb-4 opacity-0 translate-y-8">Comment fonctionne MatchRoom</h2>
          <p class="section-description text-xl text-gray-700 opacity-0 translate-y-8">
            Un processus simple en trois étapes qui révolutionne la façon dont vous réservez votre prochain séjour.
          </p>
        </div>
        
        <div class="grid md:grid-cols-3 gap-12">
          <div class="text-center animate-item opacity-0 translate-y-8">
            <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6 shadow-lg transform transition-transform duration-500 hover:scale-110 hover:shadow-xl">
              <span class="text-2xl font-bold text-pink-500">1</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Recherchez & Proposez</h3>
            <p class="text-gray-700">
              Trouvez votre hébergement idéal et proposez un prix qui vous convient.
            </p>
          </div>
          
          <div class="text-center animate-item opacity-0 translate-y-8" style="transition-delay: 0.2s">
            <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6 shadow-lg transform transition-transform duration-500 hover:scale-110 hover:shadow-xl">
              <span class="text-2xl font-bold text-pink-500">2</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Négociation</h3>
            <p class="text-gray-700">
              Les hôtels examinent votre offre et peuvent accepter, contre-proposer ou refuser en quelques heures.
            </p>
          </div>
          
          <div class="text-center animate-item opacity-0 translate-y-8" style="transition-delay: 0.4s">
            <div class="bg-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-6 shadow-lg transform transition-transform duration-500 hover:scale-110 hover:shadow-xl">
              <span class="text-2xl font-bold text-pink-500">3</span>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-4">Réservez & Profitez</h3>
            <p class="text-gray-700">
              Une fois l'accord conclu, finalisez votre réservation avec un paiement sécurisé.
            </p>
          </div>
        </div>
        
        <div class="mt-16 text-center animate-item opacity-0 translate-y-8" style="transition-delay: 0.6s">
          <a href="/register" 
             class="bg-pink-500 text-white px-8 py-3 rounded-lg text-lg font-medium transition-all duration-300 shadow-lg inline-flex items-center justify-center hover:bg-pink-600 hover:shadow-xl hover:-translate-y-1">
            Démarrez votre première négociation
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </a>
        </div>
      </div>
    </section>
    
    <!-- Section Destinations Populaires -->
    <section id="cities" class="py-20 px-6 bg-white">
      <div class="container mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="section-title text-3xl md:text-4xl font-bold text-gray-900 mb-4 opacity-0 translate-y-8">Destinations Populaires</h2>
          <p class="section-description text-xl text-gray-700 opacity-0 translate-y-8">
            Découvrez les meilleurs hôtels avec des prix négociables dans ces destinations populaires.
          </p>
        </div>
        
        <!-- Sélecteur de préférence de type d'hôtel -->
        <div class="flex justify-center mb-12 space-x-4 overflow-x-auto animate-item opacity-0 translate-y-8">
          <button 
            @click="hotelTypePreference = 'all'" 
            class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300"
            :class="hotelTypePreference === 'all' ? 'bg-pink-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Tous les hôtels
          </button>
          <button 
            @click="hotelTypePreference = 'luxury'" 
            class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300"
            :class="hotelTypePreference === 'luxury' ? 'bg-pink-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Luxe
          </button>
          <button 
            @click="hotelTypePreference = 'budget'" 
            class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300"
            :class="hotelTypePreference === 'budget' ? 'bg-pink-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Petit budget
          </button>
          <button 
            @click="hotelTypePreference = 'family'" 
            class="px-6 py-2 rounded-full text-sm font-medium transition-all duration-300"
            :class="hotelTypePreference === 'family' ? 'bg-pink-500 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Famille
          </button>
        </div>
        
        <!-- Grille de villes avec effets au survol -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="(city, index) in popularCities" :key="index"
               class="animate-item opacity-0 translate-y-8 group relative h-64 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-500 hover:-translate-y-2"
               :style="{ 'transition-delay': `${index * 0.1}s` }">
            <!-- Image d'arrière-plan (utilisant un placeholder comme fallback) -->
            <div class="absolute inset-0 bg-gray-300 bg-cover bg-center transition-transform duration-700 group-hover:scale-110"
                 :style="{ backgroundImage: `url('/images/Paris.webp')` }"></div>
            
            <!-- Superposition dégradée -->
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/60 to-transparent"></div>
            
            <!-- Contenu -->
            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
              <h3 class="text-2xl font-bold mb-1 transform transition-all duration-300 group-hover:translate-x-2">{{ city.name }}</h3>
              <p class="flex items-center text-white/80 transform transition-all duration-300 group-hover:translate-x-2" style="transition-delay: 0.1s">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                {{ city.count }} établissements
              </p>
              
              <!-- Bouton Explorer avec animation slide-in -->
              <button class="mt-4 px-5 py-2 bg-pink-500 rounded-lg text-sm font-medium group-hover:opacity-100 transition-all duration-500 opacity-0 transform translate-y-4 group-hover:translate-y-0">
                Explorer les hôtels
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Section Témoignages avec animation améliorée -->
    <section id="testimonials" class="py-20 px-6 bg-gray-50">
      <div class="container mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="section-title text-3xl md:text-4xl font-bold text-gray-900 mb-4 opacity-0 translate-y-8">Ce que disent nos utilisateurs</h2>
          <p class="section-description text-xl text-gray-700 opacity-0 translate-y-8">
            Rejoignez des milliers de voyageurs et d'hôteliers satisfaits qui ont adopté l'expérience MatchRoom.
          </p>
        </div>
        
        <div class="relative max-w-4xl mx-auto animate-item opacity-0 translate-y-8">
          <div class="rounded-2xl bg-white p-8 md:p-12 shadow-xl transition-all duration-500 hover:shadow-2xl">
            <div class="text-pink-500 mb-6 transform transition-transform duration-500 hover:scale-110 hover:rotate-6">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
              </svg>
            </div>
            
            <div class="min-h-[200px]">
              <transition name="fade" mode="out-in">
                <div :key="activeTestimonial" class="animate-fadeIn">
                  <p class="text-xl md:text-2xl text-gray-800 mb-8 italic leading-relaxed">
                    "{{ testimonials[activeTestimonial].quote }}"
                  </p>
                  <div class="flex items-center">
                    <!-- Utilisant un div placeholder jusqu'à ce que de vraies images soient disponibles -->
                    <div class="w-14 h-14 bg-gray-300 rounded-full mr-4 flex items-center justify-center text-gray-500 overflow-hidden">
                      <span class="text-xl font-bold">{{ testimonials[activeTestimonial].author.charAt(0) }}</span>
                    </div>
                    <div>
                      <h4 class="font-bold text-gray-900">{{ testimonials[activeTestimonial].author }}</h4>
                      <p class="text-gray-600">{{ testimonials[activeTestimonial].role }}</p>
                    </div>
                  </div>
                </div>
              </transition>
            </div>
            
            <div class="flex justify-center mt-8 space-x-3">
              <button 
                v-for="(_, index) in testimonials" 
                :key="index"
                @click="activeTestimonial = index"
                class="w-3 h-3 rounded-full transition-all duration-300 transform hover:scale-150"
                :class="activeTestimonial === index ? 'bg-pink-500 scale-125' : 'bg-gray-300'"
              ></button>
            </div>
          </div>
          
          <!-- Éléments décoratifs -->
          <div class="absolute -top-6 -right-6 w-12 h-12 rounded-full bg-pink-100 animate-ping opacity-70" style="animation-duration: 3s"></div>
          <div class="absolute -bottom-6 -left-6 w-12 h-12 rounded-full bg-blue-100 animate-ping opacity-70" style="animation-duration: 4s; animation-delay: 1s"></div>
        </div>
      </div>
    </section>
    
    <!-- Section CTA avec animations améliorées -->
    <section id="cta" class="py-20 px-6 bg-gradient-to-r from-blue-800 to-pink-500 text-white relative overflow-hidden">
      <!-- Arrière-plans animés -->
      <div class="absolute inset-0 overflow-hidden">
        <div class="absolute w-64 h-64 -top-32 -right-32 rounded-full bg-white/10 animate-blob"></div>
        <div class="absolute w-64 h-64 -bottom-32 -left-32 rounded-full bg-white/10 animate-blob" style="animation-delay: 2s"></div>
      </div>
      
      <div class="container mx-auto relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <h2 class="section-title text-3xl md:text-4xl font-bold mb-6 opacity-0 translate-y-8">Prêt à révolutionner votre expérience de voyage ?</h2>
          <p class="section-description text-xl mb-8 text-white/90 opacity-0 translate-y-8">
            Rejoignez MatchRoom aujourd'hui et prenez le contrôle de votre budget d'hébergement.
          </p>
          <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center">
            <a href="/register" 
               class="animate-item bg-white text-blue-800 px-8 py-3 rounded-lg text-lg font-medium transition-all duration-300 shadow-lg inline-flex items-center justify-center hover:bg-gray-100 hover:shadow-xl hover:-translate-y-1 opacity-0 translate-y-8">
              Inscription gratuite
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="/login" 
               class="animate-item bg-transparent text-white border-2 border-white px-8 py-3 rounded-lg text-lg font-medium transition-all duration-300 inline-flex items-center justify-center hover:bg-white/10 hover:shadow-lg opacity-0 translate-y-8" 
               style="transition-delay: 0.2s">
              Connexion
            </a>
          </div>
          
          <!-- Compteurs statistiques -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
            <div class="animate-item bg-white/10 backdrop-blur-sm rounded-xl p-6 opacity-0 translate-y-8" style="transition-delay: 0.3s">
              <div class="text-4xl font-bold mb-2">25k+</div>
              <div class="text-white/80">Utilisateurs satisfaits</div>
            </div>
            <div class="animate-item bg-white/10 backdrop-blur-sm rounded-xl p-6 opacity-0 translate-y-8" style="transition-delay: 0.4s">
              <div class="text-4xl font-bold mb-2">5 000+</div>
              <div class="text-white/80">Hôtels partenaires</div>
            </div>
            <div class="animate-item bg-white/10 backdrop-blur-sm rounded-xl p-6 opacity-0 translate-y-8" style="transition-delay: 0.5s">
              <div class="text-4xl font-bold mb-2">2M+ €</div>
              <div class="text-white/80">Économies générées</div>
            </div>
          </div>
        </div>
      </div>
    </section>
    
    <!-- Pied de page avec animations au survol -->
    <footer class="bg-gray-900 text-white py-12 px-6">
      <div class="container mx-auto">
        <div class="grid md:grid-cols-4 gap-8 mb-8">
          <div>
            <img src="/images/logoMR.png" alt="Logo MatchRoom" 
                 class="h-10 mb-4 transform transition-transform duration-300 hover:scale-105" />
            <p class="text-gray-400">
              La plateforme innovante pour la négociation des chambres d'hôtel.
            </p>
          </div>
          
          <div>
            <h3 class="font-bold text-lg mb-4">Plateforme</h3>
            <ul class="space-y-2">
              <li>
                <a href="#how-it-works" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Comment ça marche
                </a>
              </li>
              <li>
                <a href="#features" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Fonctionnalités
                </a>
              </li>
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Tarification
                </a>
              </li>
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Pour les hôtels
                </a>
              </li>
            </ul>
          </div>
          
          <div>
            <h3 class="font-bold text-lg mb-4">Entreprise</h3>
            <ul class="space-y-2">
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  À propos de nous
                </a>
              </li>
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Carrières
                </a>
              </li>
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Blog
                </a>
              </li>
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Presse
                </a>
              </li>
            </ul>
          </div>
          
          <div>
            <h3 class="font-bold text-lg mb-4">Support</h3>
            <ul class="space-y-2">
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Nous contacter
                </a>
              </li>
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  FAQ
                </a>
              </li>
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Conditions d'utilisation
                </a>
              </li>
              <li>
                <a href="#" 
                   class="text-gray-400 inline-flex items-center transition-all duration-300 hover:text-white hover:translate-x-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-2 opacity-0 transition-all duration-300 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                  Politique de confidentialité
                </a>
              </li>
            </ul>
          </div>
        </div>
        
        <div class="pt-8 border-t border-gray-800 flex flex-col md:flex-row justify-between items-center">
          <p class="text-gray-500">© 2025 MatchRoom. Tous droits réservés.</p>
          <div class="flex space-x-6 mt-4 md:mt-0">
            <a href="#" class="text-gray-400 hover:text-white transition-all duration-300 transform hover:scale-125">
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition-all duration-300 transform hover:scale-125">
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
              </svg>
            </a>
            <a href="#" class="text-gray-400 hover:text-white transition-all duration-300 transform hover:scale-125">
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
              </svg>
            </a>
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

/* Styles de curseur de page */
.cursor {
  pointer-events: none;
  mix-blend-mode: difference;
}

/* Animations améliorées */
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

@keyframes fadeDown {
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

.animate-fadeDown {
  animation: fadeDown 0.6s ease forwards;
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