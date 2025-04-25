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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Types de chambres</h2>
        <Link
          :href="route('room-types.create')"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded text-white"
        >
          Ajouter un type
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div v-if="roomTypes.length === 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
          <p>Aucun type de chambre n'a été ajouté. Commencez par en créer un.</p>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="roomType in roomTypes"
            :key="roomType.id"
            class="bg-white overflow-hidden shadow-sm sm:rounded-lg"
          >
            <div class="p-6">
              <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold text-gray-800">{{ roomType.name }}</h3>
                <div class="text-xl font-bold text-blue-600">{{ roomType.price_per_night }}€<span class="text-sm text-gray-600">/nuit</span></div>
              </div>

              <p class="text-gray-600 mb-4">{{ roomType.description }}</p>

              <div class="flex flex-wrap gap-4 text-sm text-gray-700 mb-4">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                  {{ roomType.capacity }} personne{{ roomType.capacity > 1 ? 's' : '' }}
                </div>
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                  </svg>
                  {{ roomType.quantity }} disponible{{ roomType.quantity > 1 ? 's' : '' }}
                </div>
              </div>

              <div class="flex justify-end space-x-2">
                <Link
                  :href="route('room-types.edit', roomType.id)"
                  class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700"
                >
                  Modifier
                </Link>
                <button
                  @click="confirmDelete(roomType.id)"
                  class="px-3 py-1 bg-red-100 hover:bg-red-200 rounded text-red-700"
                >
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
