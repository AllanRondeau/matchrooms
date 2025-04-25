<script setup>
import {Head, Link} from '@inertiajs/vue3'
import AuthenticatedProLayout from "@/Layouts/AuthenticatedProLayout.vue";

const props = defineProps({
  hotel: {
    type: Object,
    default: null
  },
  roomTypes: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <Head title="Dashboard" />

  <AuthenticatedProLayout>
    <template #header>
      <div class="flex space-x-4">
        <div v-if="hotel" class="space-x-4">
          <Link
            :href="route('hotel.edit', hotel.id)"
            class="text-blue-600 hover:underline"
          >
            Informations de l'hôtel
          </Link>
          <Link
            :href="route('room-types.create')"
            class="text-blue-600 hover:underline"
          >
            Types de chambres
          </Link>
        </div>
        <div v-else>
          <Link
            :href="route('hotel.create')"
            class="text-blue-600 hover:underline"
          >
            Informations de l'hôtel
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
          <div class="p-6 text-gray-900">
            <h2 class="text-xl font-semibold mb-4">Bienvenue sur votre tableau de bord</h2>

            <div v-if="!hotel" class="mb-6 p-4 bg-yellow-50 rounded border border-yellow-200 text-yellow-700">
              Commencez par configurer les informations de votre hôtel.
            </div>

            <div v-else-if="roomTypes && roomTypes.length === 0" class="mb-6 p-4 bg-blue-50 rounded border border-blue-200 text-blue-700">
              Votre hôtel est configuré ! Maintenant, ajoutez des types de chambres pour commencer à recevoir des réservations.
            </div>

            <div v-if="roomTypes && roomTypes.length > 0" class="mt-8">
              <h3 class="text-lg font-semibold mb-4">Vos types de chambres</h3>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                  v-for="roomType in roomTypes"
                  :key="roomType.id"
                  class="bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg"
                >
                  <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                      <h4 class="text-lg font-semibold text-gray-800">{{ roomType.name }}</h4>
                      <div class="text-xl font-bold text-blue-600">{{ roomType.base_price }}€<span class="text-sm text-gray-600">/nuit</span></div>
                    </div>

                    <p class="text-gray-600 mb-4 line-clamp-2">{{ roomType.description }}</p>

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
                        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-gray-700"
                      >
                        Modifier
                      </Link>
                      <Link
                        :href="route('room-types.destroy', roomType.id)"
                        method="delete"
                        type="button"
                        class="px-3 py-1 bg-red-100 hover:bg-red-200 rounded text-red-700"
                        @click="(e) => {
                         e.preventDefault();
                         if (confirm('Êtes-vous sûr de vouloir supprimer ce type de chambre ?')) {
                            $inertia.delete(route('room-types.destroy', roomType.id));
                            }
                         }"
                      >
                        Supprimer
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedProLayout>
</template>
