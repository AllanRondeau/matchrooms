<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3' ;
import NavLink from "@/Components/NavLink.vue";

const props = defineProps([
  'hotels',
  'query',
  'suggestedHotels',
  'keywords',
  'suggestions',
  'viewType'
])

const searchQuery = ref(props.query || '')
const isLoading = ref(false)

// Toggle Sidebar
const sidebarVisible = ref(false)
const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value
}

const searchHotels = () => {
  if (!searchQuery.value.trim()) return

  isLoading.value = true

  router.get('/map', { q: searchQuery.value }, {
    preserveState: true,
    replace: true,
    onFinish: () => {
      isLoading.value = false
    }
  })
}
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
          <a href="/map" class="flex items-center gap-3 bg-[#F7E6E2] text-[#FF135F] px-4 py-3 rounded-xl transition-all duration-300 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <span>Map Finder</span>
          </a>
          <a href="/negociations" class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
    <div class="flex flex-1 p-4 gap-4">
      <!-- Colonne gauche -->
      <div class="w-1/3 bg-white rounded-xl shadow-md overflow-hidden border border-[#E9E1DC]">
        <!-- Barre de recherche -->
        <div class="p-4 border-b border-[#E9E1DC]">
          <h2 class="text-lg font-semibold text-[#0E2748] mb-4 text-center">Trouvez votre hôtel idéal</h2>
          <div class="flex gap-2">
            <input
              v-model="searchQuery"
              @keydown.enter="searchHotels"
              type="text"
              placeholder="Ville, quartier, ambiance..."
              class="w-full px-4 py-2 rounded-xl text-[#2E2E38] bg-[#F4F3F3] border border-[#E9E1DC] focus:outline-none focus:ring-2 focus:ring-[#FF135F] focus:border-transparent"
            />
            <button
              @click="searchHotels"
              class="bg-[#FF135F] hover:bg-[#FF135F]/90 text-white font-medium px-4 py-2 rounded-xl shadow-sm hover:shadow transition-all duration-300"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Loader -->
        <div v-if="isLoading" class="flex justify-center my-6">
          <svg class="animate-spin h-8 w-8 text-[#FF135F]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
          </svg>
        </div>

        <!-- Mots-clés -->
        <div v-if="!isLoading && keywords && Object.keys(keywords).length" class="px-4 py-3 border-b border-[#E9E1DC]">
          <p class="text-sm font-medium text-[#2E2E38] mb-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            Mots-clés détectés
          </p>
          <div class="flex flex-wrap gap-2">
            <span v-for="(val, key) in keywords" :key="key" class="px-3 py-1 bg-[#F7E6E2] text-[#FF135F] rounded-full text-xs font-medium">
              {{ val }}
            </span>
          </div>
        </div>

        <!-- Accueil par défaut -->
        <div v-if="!isLoading && searchQuery.trim() === ''" class="p-6 text-center flex flex-col items-center">
          <div class="w-16 h-16 rounded-full bg-[#F7E6E2] flex items-center justify-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
            </svg>
          </div>
          
          <h2 class="text-xl font-semibold text-[#0E2748] mb-2">Commence ton voyage</h2>
          <p class="text-[#2E2E38]/70 text-sm mb-6">Tape une envie, une ambiance ou un lieu pour découvrir des hôtels qui matchent avec toi.</p>
          <div class="text-xs text-[#2E2E38]/60 italic">Exemples : "Paris romantique", "Plage tranquille", "Centre-ville animé"</div>
        </div>

        <!-- Résultats -->
        <div v-else-if="!isLoading && hotels.length > 0" class="overflow-auto max-h-[calc(100vh-220px)]">
          <div v-for="hotel in hotels" :key="hotel.id" class="p-4 border-b border-[#E9E1DC] hover:bg-[#F7E6E2]/30 transition-colors duration-300">
            <div class="flex gap-3">
              <img :src="hotel.image_url || 'https://placehold.co/300x200'" :alt="hotel.name" class="w-24 h-24 object-cover rounded-lg" />
              <div class="flex-1">
                <h3 class="font-semibold text-[#0E2748]">{{ hotel.name }}</h3>
                <p class="text-sm text-[#2E2E38]/70 flex items-center mt-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  {{ hotel.location }}
                </p>
                <div class="flex items-center mt-2">
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
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Suggestions -->
        <div v-else-if="!isLoading && suggestedHotels.length > 0" class="overflow-auto max-h-[calc(100vh-220px)]">
          <div class="p-4 border-b border-[#E9E1DC] bg-[#F7E6E2]/30">
            <h2 class="text-md font-semibold text-[#FF135F] mb-2 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
              </svg>
              Suggestions proches
            </h2>
          </div>
          <div v-for="hotel in suggestedHotels" :key="hotel.id" class="p-4 border-b border-[#E9E1DC] hover:bg-[#F7E6E2]/30 transition-colors duration-300">
            <div class="flex gap-3">
              <img :src="hotel.image_url || 'https://placehold.co/300x200'" :alt="hotel.name" class="w-24 h-24 object-cover rounded-lg" />
              <div class="flex-1">
                <h3 class="font-semibold text-[#0E2748]">{{ hotel.name }}</h3>
                <p class="text-sm text-[#2E2E38]/70 flex items-center mt-1">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  {{ hotel.location }}
                </p>
                <p class="text-xs text-[#FF135F] italic mt-2 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Suggestion basée sur "{{ hotel.suggestion_term }}"
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Aucun résultat -->
        <div v-else-if="!isLoading && searchQuery.trim() !== ''" class="p-6 text-center">
          <div class="w-16 h-16 rounded-full bg-[#F7E6E2] flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
          </div>
          <h2 class="text-xl font-semibold text-[#0E2748] mb-2">Aucun résultat trouvé</h2>
          <p class="text-[#2E2E38]/70 text-sm mb-4">Nous n'avons pas trouvé d'hôtel correspondant à votre recherche.</p>
          <button @click="searchQuery = ''" class="bg-[#FF135F] text-white px-4 py-2 rounded-xl font-medium hover:bg-[#FF135F]/90 transition-colors duration-300">
            Effacer la recherche
          </button>
        </div>
      </div>

      <!-- Carte -->
      <div class="flex-1 flex flex-col rounded-xl overflow-hidden bg-white shadow-md border border-[#E9E1DC]">
        <div class="p-4 border-b border-[#E9E1DC]">
          <div class="bg-[#F7E6E2] rounded-xl text-center py-3 px-4 text-[#FF135F] font-medium flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            Ton crush peut se trouver n'importe où !
          </div>
        </div>
        <div class="flex-1">
          <iframe
            class="w-full h-full"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.999555553087!2d2.2922923156733315!3d48.85884407928761!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66fdd37bffa1d%3A0x5a4aa7b94aa4f9c!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1633012747560!5m2!1sfr!2sfr"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
          ></iframe>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active {
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