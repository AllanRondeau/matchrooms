<script setup>
import {ref} from 'vue';
import {Link, router} from '@inertiajs/vue3';
import {Head} from '@inertiajs/vue3';
import {route} from "ziggy-js";
import AuthenticatedProLayout from "@/Layouts/AuthenticatedProLayout.vue";

const props = defineProps({
  roomTypes: Array,
  hotel: Object,
});

const confirmDelete = (id) => {
  if (confirm('Êtes-vous sûr de vouloir supprimer ce type de chambre ?')) {
    router.delete(route('room-types.destroy', id));
  }
};
</script>

<template>
  <Head title="Types de chambres" />

  <AuthenticatedProLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-[#0E2748] leading-tight flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          Types de chambres
        </h2>
        <Link
            :href="route('room-types.create')"
            class="px-4 py-2 bg-[#FF135F] hover:bg-[#FF135F]/90 text-white rounded-xl font-medium transition-all duration-300 flex items-center shadow-sm"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Ajouter un type
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div v-if="roomTypes.length === 0" class="bg-white rounded-xl shadow-sm p-8 text-center border border-[#E9E1DC]">
          <div class="w-16 h-16 rounded-full bg-[#F7E6E2] flex items-center justify-center mx-auto mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-[#0E2748] mb-2">Aucun type de chambre</h3>
          <p class="text-[#2E2E38]/70 mb-6">Aucun type de chambre n'a été ajouté. Commencez par en créer un.</p>
          <Link
            :href="route('room-types.create')"
            class="inline-block px-6 py-3 bg-[#FF135F] hover:bg-[#FF135F]/90 text-white rounded-xl font-medium transition-all duration-300 shadow-sm"
          >
            Créer mon premier type de chambre
          </Link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
              v-for="roomType in roomTypes"
              :key="roomType.id"
              class="bg-white rounded-xl shadow-sm overflow-hidden border border-[#E9E1DC] hover:shadow-md transition-all duration-300"
          >
            <div class="p-6">
              <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-[#0E2748]">{{ roomType.name }}</h3>
                <div class="text-xl font-bold text-[#FF135F]">{{ roomType.price_per_night }}€<span class="text-sm text-[#2E2E38]/70">/nuit</span></div>
              </div>

              <p class="text-sm text-[#2E2E38]/70 mb-4 line-clamp-2">{{ roomType.description }}</p>

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
                <button
                    @click="confirmDelete(roomType.id)"
                    class="px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded-lg text-red-600 text-sm font-medium transition-all duration-300 flex items-center"
                >
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                  Supprimer
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedProLayout>
</template>