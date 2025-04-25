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
  <div class="min-h-screen bg-[#d9e2e7] flex flex-col">

    <!-- SIDEBAR -->
    <transition name="slide">
      <aside
        v-if="sidebarVisible"
        class="fixed top-0 left-0 h-full w-64 bg-[#d6E1E5] shadow-md z-50 flex flex-col p-6 space-y-6 border-r border-gray-300"
      >
        <!-- Header du menu -->
        <div class="flex justify-between items-center mb-6">
          <img src="/images/logoMR.png" alt="Logo MatchRoom" class="w-24" />
          <button @click="toggleSidebar" class="text-gray-500 hover:text-pink-500 text-2xl transition">
            ✖
          </button>
        </div>

        <!-- Liens du menu -->
        <nav class="flex flex-col gap-4 text-[#2D2C33] font-semibold">
          <a href="/liked-rooms" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
            <span>Toutes les chambres likées</span>
          </a>
          <a href="/map" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
            <span>Map Finder</span>
          </a>
          <a href="/negociations" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
            <span>Négociations en cours</span>
          </a>
          <a href="#" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
            <span>Mes réservations</span>
          </a>
          <NavLink
            :href="route('logout')"
            method="post"
          >
            Se déconnecter
          </NavLink>
        </nav>
      </aside>
    </transition>

    <!-- HEADER -->
    <header class="w-full flex items-center justify-between px-6 py-4 bg-[#d9e2e7]">
      <button class="text-3xl" @click="toggleSidebar">☰</button>

      <div class="flex items-center space-x-2">
        <img src="/images/logoMR.png" alt="Logo MatchRoom"
             class="h-10 mb-4 transform transition-transform duration-300 hover:scale-105" />
      </div>

      <div class="w-8 h-8"></div>
    </header>

    <!-- CONTENU -->
    <div class="flex flex-1 px-4 pb-6 gap-4">

      <!-- Colonne gauche -->
      <div class="w-1/3 bg-[#d1d5db] rounded-xl p-4 space-y-4">
        <!-- Barre de recherche -->
        <div class="flex justify-center gap-2 mt-2 mb-4 px-2">
          <input
              v-model="searchQuery"
              @keydown.enter="searchHotels"
              type="text"
              placeholder="Barre de recherche"
              class="w-full px-4 py-2 rounded-full text-center font-semibold bg-white shadow focus:outline-none focus:ring-2 focus:ring-pink-500"
          />
          <button
              @click="searchHotels"
              class="bg-pink-600 hover:bg-pink-700 text-white font-bold px-4 py-2 rounded-full shadow"
          >
            Rechercher
          </button>
        </div>

        <!-- Loader -->
        <div v-if="isLoading" class="flex justify-center my-6">
          <svg class="animate-spin h-6 w-6 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
          </svg>
        </div>

        <!-- Mots-clés -->
        <div v-if="!isLoading && keywords && Object.keys(keywords).length" class="text-sm text-gray-700">
          <p class="mt-2">🔍 Mots-clés détectés :</p>
          <ul class="flex flex-wrap gap-1 mt-1">
            <li v-for="(val, key) in keywords" :key="key" class="px-2 py-1 bg-pink-300 text-white rounded-full text-xs">
              {{ val }}
            </li>
          </ul>
        </div>

        <!-- Accueil par défaut -->
        <div v-if="!isLoading && searchQuery.trim() === ''" class="p-6 mt-6 text-center">
          <h2 class="text-xl font-bold text-gray-800 mb-2">Commence ton voyage</h2>
          <p class="text-gray-600 text-sm">Tape une envie, une ambiance ou un lieu pour découvrir des hôtels qui matchent avec toi.</p>
        </div>

        <!-- Résultats -->
        <div v-else-if="!isLoading && hotels.length > 0" class="mt-4 space-y-4">
          <div v-for="hotel in hotels" :key="hotel.id" class="bg-white rounded-lg shadow-md overflow-hidden">
            <img :src="hotel.image_url || 'https://placehold.co/300x200'" :alt="hotel.name" class="w-full h-40 object-cover" />
            <div class="p-3 text-sm font-semibold text-gray-800">
              {{ hotel.name }}<br />
              {{ hotel.location }}
            </div>
          </div>
        </div>

        <!-- Suggestions -->
        <div v-else-if="!isLoading && suggestedHotels.length > 0" class="mt-4">
          <h2 class="text-md font-semibold text-pink-800 mb-2">Suggestions proches 💡</h2>
          <div v-for="hotel in suggestedHotels" :key="hotel.id" class="bg-white rounded-lg shadow-md overflow-hidden">
            <img :src="hotel.image_url || 'https://placehold.co/300x200'" :alt="hotel.name" class="w-full h-40 object-cover" />
            <div class="p-3 text-sm font-semibold text-gray-800">
              {{ hotel.name }}<br />
              {{ hotel.location }}
              <p class="text-xs text-pink-600 italic mt-1">(Suggestion basée sur "{{ hotel.suggestion_term }}")</p>
            </div>
          </div>
        </div>

        <!-- Aucun résultat -->
        <div v-else-if="!isLoading" class="text-gray-600 mt-4">
          Aucun hôtel trouvé pour cette recherche 🥲
        </div>
      </div>

      <!-- Carte -->
      <div class="flex-1 rounded-xl overflow-hidden">
        <div class="w-full px-4 mt-2 mb-4">
          <div class="bg-[#f7e6e6] border-4 border-pink-500 rounded-xl text-center py-3 text-lg font-bold text-black w-full">
            Ton crush peut se trouver n’importe où !
          </div>
        </div>
        <iframe
            class="w-full h-full rounded-xl"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.999555553087!2d2.2922923156733315!3d48.85884407928761!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66fdd37bffa1d%3A0x5a4aa7b94aa4f9c!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1633012747560!5m2!1sfr!2sfr"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
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
