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
        <h1 class="text-lg font-semibold text-[#101F44]">Ravie de vous revoir, <span class="text-[#EA3D69]">Aya Hanine</span> 👋</h1>
        <img src="https://placehold.co/40x40" alt="Avatar" class="rounded-full w-10 h-10 border" />
      </div>
    </template>

    <div class="flex min-h-screen font-sans bg-[#F7F5F5]">
      <!-- Sidebar -->
      <aside class="w-64 bg-[#E8E3DF] text-[#2D2C33] flex flex-col justify-between py-6 px-4 rounded-tr-2xl">
        <div>
          <div class="text-center mb-10">
            <img src="/images/logoMR.png" alt="MatchRoom Logo" class="w-28 mx-auto mb-2" />
          </div>
          <nav class="space-y-4 text-sm font-medium">
            <Link :href="route('room-types.create')" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-[#F5E9E6]">
              ➕ Add Room
            </Link>
            <a href="#" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-[#F5E9E6]">
              📅 Manage Reservations
            </a>
            <a href="#" class="flex items-center gap-2 px-4 py-2 rounded hover:bg-[#F5E9E6]">
              💬 Manage Negotiations
            </a>
          </nav>
        </div>
      </aside>

      <!-- Main content -->
      <div class="flex-1 p-6">
        <!-- Alerts -->
        <div v-if="!hotel" class="mb-6 p-4 bg-yellow-50 rounded border border-yellow-200 text-yellow-700">
          Commencez par configurer les informations de votre hôtel.
        </div>

        <div v-else-if="roomTypes && roomTypes.length === 0" class="mb-6 p-4 bg-blue-50 rounded border border-blue-200 text-blue-700">
          Votre hôtel est configuré ! Maintenant, ajoutez des types de chambres pour commencer à recevoir des réservations.
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
          <div class="bg-white border border-[#D8E0E3] rounded-lg shadow-sm p-6 text-center">
            <div class="text-[#EA3D69] text-2xl font-bold">{{ roomTypes.length }}</div>
            <div class="text-sm text-[#444151] mt-1">Rooms</div>
          </div>
          <div class="bg-white border border-[#D8E0E3] rounded-lg shadow-sm p-6 text-center">
            <div class="text-[#EA3D69] text-2xl font-bold">34</div>
            <div class="text-sm text-[#444151] mt-1">Reservations</div>
          </div>
          <div class="bg-white border border-[#D8E0E3] rounded-lg shadow-sm p-6 text-center">
            <div class="text-[#EA3D69] text-2xl font-bold">€2,500</div>
            <div class="text-sm text-[#444151] mt-1">Income</div>
          </div>
        </div>

        <!-- Room Types -->
        <div v-if="roomTypes && roomTypes.length > 0" class="mt-8">
          <h3 class="text-lg font-semibold mb-4 text-[#101F44]">🛏️ Vos types de chambres</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
                v-for="roomType in roomTypes"
                :key="roomType.id"
                class="bg-white border border-[#D8E0E3] overflow-hidden shadow-sm rounded-lg"
            >
              <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                  <h4 class="text-lg font-semibold text-[#101F44]">{{ roomType.name }}</h4>
                  <div class="text-xl font-bold text-[#EA3D69]">{{ roomType.base_price }}€<span class="text-sm text-[#444151]">/nuit</span></div>
                </div>
                <p class="text-[#444151] text-sm mb-4 line-clamp-2">{{ roomType.description }}</p>
                <div class="flex flex-wrap gap-4 text-sm text-[#444151] mb-4">
                  <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                    {{ roomType.capacity }} personne{{ roomType.capacity > 1 ? 's' : '' }}
                  </div>
                  <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                    </svg>
                    {{ roomType.quantity }} disponible{{ roomType.quantity > 1 ? 's' : '' }}
                  </div>
                </div>
                <div class="flex justify-end space-x-2">
                  <Link
                      :href="route('room-types.edit', roomType.id)"
                      class="px-3 py-1 bg-white border border-[#D8E0E3] hover:bg-[#F5E9E6] rounded text-[#2D2C33] text-sm"
                  >
                    Modifier
                  </Link>
                  <Link
                      :href="route('room-types.destroy', roomType.id)"
                      method="delete"
                      type="button"
                      class="px-3 py-1 bg-red-50 hover:bg-red-100 rounded text-red-700 text-sm"
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
  </AuthenticatedProLayout>
</template>
