<script setup>
import { ref, onMounted } from 'vue';
import NavLink from "@/Components/NavLink.vue";

const negotiations = ref([]);
const sidebarVisible = ref(false);

const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value;
};

const loadNegotiations = () => {
  const stored = JSON.parse(localStorage.getItem('negotiations')) || [];
  negotiations.value = stored;
};

const getTimeRemaining = (expiresAt) => {
  const remaining = new Date(expiresAt) - new Date();
  if (remaining <= 0) return 'Expiré';

  const hours = Math.floor(remaining / (1000 * 60 * 60));
  const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

  return `${hours}h ${minutes}m ${seconds}s`;
};

onMounted(() => {
  loadNegotiations();
  setInterval(loadNegotiations, 60000); // Refresh every 60s
});
</script>

<template>
  <div class="min-h-screen bg-[#F4F3F3] flex flex-col">
    <!-- SIDEBAR -->
    <transition name="slide">
      <aside
        v-if="sidebarVisible"
        class="fixed top-0 left-0 h-full w-72 bg-white shadow-xl z-50 flex flex-col p-6 space-y-6 border-r border-[#E9E1DC]"
      >
        <!-- Header du menu -->
        <div class="flex justify-between items-center mb-6">
          <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/logoMR-TakbHOAaljFXg5P560GFVsP1M2q0Tx.png" alt="Logo MatchRoom" class="h-10" />
          <button @click="toggleSidebar" class="text-[#2E2E38] hover:text-[#FF135F] transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Liens du menu -->
        <nav class="flex flex-col gap-2 text-[#2E2E38] font-medium">
          <a href="/liked-rooms" class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span>Chambres likées</span>
          </a>
          <a href="/map" class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <span>Map Finder</span>
          </a>
          <a href="/negociations" class="flex items-center gap-3 bg-[#F7E6E2] text-[#FF135F] px-4 py-3 rounded-xl transition-all duration-300 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <span>Négociations en cours</span>
          </a>
          <a href="#" class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Mes réservations</span>
          </a>
          <div class="pt-4 mt-4 border-t border-[#E9E1DC]">
            <NavLink
              :href="route('logout')"
              method="post"
              class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300 text-[#2E2E38]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Se déconnecter
            </NavLink>
          </div>
        </nav>
      </aside>
    </transition>

    <!-- HEADER -->
    <header class="w-full flex items-center justify-between px-6 py-4 bg-white shadow-sm border-b border-[#E9E1DC]">
      <button @click="toggleSidebar" class="text-[#2E2E38] hover:text-[#FF135F] transition-colors duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <div class="flex items-center">
        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/logoMR-TakbHOAaljFXg5P560GFVsP1M2q0Tx.png" alt="Logo MatchRoom" class="h-10 transform transition-transform duration-300 hover:scale-105" />
      </div>

      <div class="w-6"></div>
    </header>

    <!-- CONTENU -->
    <main class="p-8 flex-1">
      <h1 class="text-2xl font-bold mb-8 text-center text-[#0E2748]">
        <span class="inline-block bg-[#F7E6E2] text-[#FF135F] px-4 py-1 rounded-full text-sm font-medium mb-2">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          En attente
        </span>
        <br />
        Négociations en cours
      </h1>

      <div v-if="negotiations.length === 0" class="text-center py-12">
        <div class="bg-white rounded-xl shadow-md p-8 max-w-md mx-auto">
          <div class="text-[#FF135F] mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
          </div>
          <h2 class="text-xl font-semibold text-[#2E2E38] mb-2">Aucune négociation en cours</h2>
          <p class="text-[#2E2E38]/70 mb-6">Commencez à négocier le prix des chambres qui vous intéressent pour les voir apparaître ici.</p>
          <a href="/" class="inline-block bg-[#FF135F] text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:shadow-[#FF135F]/20 hover:-translate-y-0.5">
            Découvrir des chambres
          </a>
        </div>
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
            v-for="nego in negotiations"
            :key="nego.id"
            class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-[#E9E1DC]"
        >
          <div class="relative">
            <img :src="nego.image" alt="Hôtel" class="w-full h-48 object-cover" />
            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium text-[#FF135F] shadow-lg">
              {{ getTimeRemaining(nego.expiresAt) }}
            </div>
          </div>
          
          <div class="p-6">
            <h2 class="text-lg font-semibold text-[#0E2748] mb-1">{{ nego.name }}</h2>
            <p class="text-sm text-[#2E2E38]/70 mb-4 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              {{ nego.location }}
            </p>

            <div class="flex justify-between items-center mb-4 bg-[#F4F3F3] p-3 rounded-lg">
              <div>
                <div class="text-xs text-[#2E2E38]/70 mb-1">Prix de base</div>
                <div class="text-lg font-semibold text-[#2E2E38] line-through">{{ nego.basePrice }}€</div>
              </div>
              <div class="text-right">
                <div class="text-xs text-[#2E2E38]/70 mb-1">Prix proposé</div>
                <div class="text-lg font-semibold text-[#FF135F]">{{ nego.proposedPrice }}€</div>
              </div>
            </div>
            
            <div class="relative pt-4">
              <div class="flex justify-between mb-2 text-xs">
                <span class="font-medium text-[#2E2E38]">Économie: {{ Math.round((nego.basePrice - nego.proposedPrice) / nego.basePrice * 100) }}%</span>
                <span class="font-medium text-[#FF135F]">{{ nego.basePrice - nego.proposedPrice }}€</span>
              </div>
              <div class="h-1.5 bg-[#E9E1DC] rounded-full overflow-hidden">
                <div class="h-full bg-[#FF135F] rounded-full"
                     :style="{ width: `${Math.round((nego.basePrice - nego.proposedPrice) / nego.basePrice * 100)}%` }"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from {
  transform: translateX(-100%);
  opacity: 0;
}
.slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}
</style>