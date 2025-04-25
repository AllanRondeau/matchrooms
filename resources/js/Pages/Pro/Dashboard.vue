<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedProLayout from '@/Layouts/AuthenticatedProLayout.vue'

const props = defineProps({
  hotel: {
    type: Object,
    default: null
  },
  roomTypes: {
    type: Array,
    default: () => []
  }
})
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedProLayout>
    <template #header>
      <div class="flex justify-between items-center w-full">
        <h1 class="text-xl font-semibold text-[#0E2748]">Ravie de vous revoir, <span class="text-[#FF135F]">Aya Hanine</span> 👋</h1>
        <div class="w-10 h-10 rounded-full bg-[#F7E6E2] flex items-center justify-center text-[#FF135F] font-bold">
          AH
        </div>
      </div>
    </template>

    <div class="flex min-h-screen font-sans bg-[#F4F3F3]">
      <!-- Sidebar -->
      <aside class="w-64 bg-white shadow-md text-[#2E2E38] flex flex-col justify-between py-6 px-4 border-r border-[#E9E1DC]">
        <div>
          <div class="text-center mb-10">
            <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/logoMR-TakbHOAaljFXg5P560GFVsP1M2q0Tx.png" alt="MatchRoom Logo" class="h-10 mx-auto mb-2" />
          </div>
          <nav class="space-y-2 text-sm font-medium">
            <Link :href="route('room-types.create')" class="flex items-center gap-2 px-4 py-3 rounded-xl hover:bg-[#F7E6E2] transition-all duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Ajouter une chambre
            </Link>
            <a href="#" class="flex items-center gap-2 px-4 py-3 rounded-xl hover:bg-[#F7E6E2] transition-all duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Gérer les réservations
            </a>
            <a href="#" class="flex items-center gap-2 px-4 py-3 rounded-xl hover:bg-[#F7E6E2] transition-all duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
              </svg>
              Gérer les négociations
            </a>
          </nav>
        </div>
      </aside>

      <!-- Main content -->
      <div class="flex-1 p-6">
        <!-- Alerts -->
        <div v-if="!hotel" class="mb-6 p-4 bg-[#F7E6E2] rounded-xl border border-[#FF135F]/20 text-[#FF135F] flex items-start">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="font-medium">Commencez par configurer les informations de votre hôtel.</p>
            <p class="text-sm mt-1">Ajoutez les détails de votre établissement pour commencer à recevoir des réservations.</p>
          </div>
        </div>

        <div v-else-if="roomTypes && roomTypes.length === 0" class="mb-6 p-4 bg-[#D6E1E5] rounded-xl border border-[#0E2748]/20 text-[#0E2748] flex items-start">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="font-medium">Votre hôtel est configuré !</p>
            <p class="text-sm mt-1">Maintenant, ajoutez des types de chambres pour commencer à recevoir des réservations.</p>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm p-6 text-center border border-[#E9E1DC] hover:shadow-md transition-shadow duration-300">
            <div class="w-12 h-12 rounded-full bg-[#F7E6E2] flex items-center justify-center mx-auto mb-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
            </div>
            <div class="text-[#FF135F] text-3xl font-bold">{{ roomTypes.length }}</div>
            <div class="text-sm text-[#2E2E38]/70 mt-1">Types de chambres</div>
          </div>
          <div class="bg-white rounded-xl shadow-sm p-6 text-center border border-[#E9E1DC] hover:shadow-md transition-shadow duration-300">
            <div class="w-12 h-12 rounded-full bg-[#F7E6E2] flex items-center justify-center mx-auto mb-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div class="text-[#FF135F] text-3xl font-bold">34</div>
            <div class="text-sm text-[#2E2E38]/70 mt-1">Réservations</div>
          </div>
          <div class="bg-white rounded-xl shadow-sm p-6 text-center border border-[#E9E1DC] hover:shadow-md transition-shadow duration-300">
            <div class="w-12 h-12 rounded-full bg-[#F7E6E2] flex items-center justify-center mx-auto mb-3">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="text-[#FF135F] text-3xl font-bold">€2,500</div>
            <div class="text-sm text-[#2E2E38]/70 mt-1">Revenus</div>
          </div>
        </div>

        <!-- Room Types -->
        <div v-if="roomTypes && roomTypes.length > 0" class="mt-8">
          <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-[#0E2748] flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Vos types de chambres
            </h3>
            <Link :href="route('room-types.create')" class="px-4 py-2 bg-[#FF135F] text-white rounded-xl font-medium hover:bg-[#FF135F]/90 transition-all duration-300 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Ajouter
            </Link>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="roomType in roomTypes"
                :key="roomType.id"
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-[#E9E1DC] hover:shadow-md transition-all duration-300"
            >
              <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                  <h4 class="text-lg font-semibold text-[#0E2748]">{{ roomType.name }}</h4>
                  <div class="text-xl font-bold text-[#FF135F]">{{ roomType.base_price }}€<span class="text-sm text-[#2E2E38]/70">/nuit</span></div>
                </div>
                <p class="text-[#2E2E38]/70 text-sm mb-4 line-clamp-2">{{ roomType.description }}</p>
                <div class="flex flex-wrap gap-4 text-sm text-[#2E2E38]/70 mb-4">
                  <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    {{ roomType.capacity }} personne{{ roomType.capacity > 1 ? 's' : '' }}
                  </div>
                  <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    {{ roomType.quantity }} disponible{{ roomType.quantity > 1 ? 's' : '' }}
                  </div>
                </div>
                <div class="flex justify-end space-x-2">
                  <Link
                      :href="route('room-types.edit', roomType.id)"
                      class="px-3 py-1.5 bg-[#F7E6E2] hover:bg-[#F7E6E2]/70 rounded-lg text-[#FF135F] text-sm font-medium transition-all duration-300 flex items-center"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Modifier
                  </Link>
                  <Link
                      :href="route('room-types.destroy', roomType.id)"
                      method="delete"
                      type="button"
                      class="px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg text-red-600 text-sm font-medium transition-all duration-300 flex items-center"
                      @click="(e) => {
                      e.preventDefault();
                      if (confirm('Êtes-vous sûr de vouloir supprimer ce type de chambre ?')) {
                        $inertia.delete(route('room-types.destroy', roomType.id));
                      }
                    }"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Supprimer
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedProLayout>
</template>