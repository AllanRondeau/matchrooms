<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps([
  'hotels',
  'query',
  'suggestedHotels',
  'keywords',
  'suggestions',
  'viewType'
])

const searchQuery = ref(props.query || '')

const searchHotels = () => {
  router.get('/map', { q: searchQuery.value }, {
    preserveState: true,
    replace: true,
  })
}
</script>

<template>
  <div class="min-h-screen bg-[#d9e2e7] flex flex-col">

    <!-- Header -->
    <header class="w-full py-4 flex justify-center items-center">
      <img src="https://placehold.co/32x32?text=🏠" class="w-8 h-8 mr-2" />
      <h1 class="text-2xl font-bold text-pink-600">
        Match<span class="text-gray-900">Room</span>
      </h1>
    </header>

    <!-- Bannière -->
    <div class="w-full px-4 mt-2 mb-4">
      <div class="bg-[#f7e6e6] border-4 border-pink-500 rounded-xl text-center py-3 text-lg font-bold text-black w-full">
        💘 Ton crush peut se trouver n’importe où !
      </div>
    </div>

    <!-- Grille -->
    <div class="flex flex-1 px-4 pb-6 gap-4">
      <!-- Colonne gauche -->
      <div class="w-1/3 bg-[#d1d5db] rounded-xl p-4 space-y-4">

        <!-- Recherche -->
        <input
            v-model="searchQuery"
            @keydown.enter="searchHotels"
            type="text"
            placeholder="Barre de recherche"
            class="w-full px-4 py-2 rounded-full text-center font-semibold bg-white shadow"
        />

        <!-- Résultats -->
        <div v-for="hotel in hotels" :key="hotel.id" class="bg-white rounded-lg shadow-md overflow-hidden">
          <img :src="hotel.image_url || 'https://placehold.co/300x200'" :alt="hotel.name" class="w-full h-40 object-cover" />
          <div class="p-3 text-sm font-semibold text-gray-800">
            {{ hotel.name }}<br />
            {{ hotel.location }}
          </div>
        </div>
      </div>

      <!-- Carte -->
      <div class="flex-1 rounded-xl overflow-hidden">
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
