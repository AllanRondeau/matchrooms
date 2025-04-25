<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedProLayout from "@/Layouts/AuthenticatedProLayout.vue";

const props = defineProps({
  roomType: Object,
  hotel: Object,
  isEdit: Boolean,
})

const amenitiesInput = ref(
    props.roomType?.amenities?.join(', ') || ''
)
const form = useForm({
  hotel_id: props.hotel?.id || props.roomType?.hotel_id || '',
  name: props.roomType?.name || '',
  description: props.roomType?.description || '',
  base_price: props.roomType?.base_price || '',
  capacity: props.roomType?.capacity || 1,
  stock_quantity: props.roomType?.stock_quantity || 1,
  amenities: Array.isArray(props.roomType?.amenities)
      ? props.roomType.amenities
      : [],
  images: [],
  status: props.roomType?.status || 'active',
})

watch(amenitiesInput, (val) => {
  form.amenities = val
      .split(',')
      .map(s => s.trim())
      .filter(Boolean)
})

const submit = () => {
  if (props.isEdit) {
    form.post(route('room-types.update', props.roomType.id), {
      method: 'put',
      forceFormData: true,
    })
  } else {
    form.post(route('room-types.store'), {
      forceFormData: true,
    })
  }
}
</script>

<template>
  <Head :title="isEdit ? 'Modifier un type de chambre' : 'Ajouter un type de chambre'" />
  <AuthenticatedProLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-[#0E2748] flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          {{ isEdit ? 'Modifier un type de chambre' : 'Ajouter un type de chambre' }}
        </h2>
        <Link
            :href="route('pro.dashboard')"
            class="px-4 py-2 bg-[#E9E1DC] hover:bg-[#D6E1E5] rounded-xl text-[#2E2E38] font-medium transition-all duration-300 flex items-center"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Retour
        </Link>
      </div>
    </template>
    <div class="py-10">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-md p-8 border border-[#E9E1DC]">
          <form @submit.prevent="submit" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" v-model="form.hotel_id" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-[#2E2E38] mb-1">Nom de la chambre</label>
                <input v-model="form.name" type="text" class="w-full border border-[#E9E1DC] rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FF135F] focus:border-transparent transition-all duration-300" />
                <div v-if="form.errors.name" class="text-[#FF135F] text-sm mt-1">{{ form.errors.name }}</div>
              </div>
              <div>
                <label class="block text-sm font-medium text-[#2E2E38] mb-1">Prix (€)</label>
                <input v-model="form.base_price" type="number" step="0.01" min="0" class="w-full border border-[#E9E1DC] rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FF135F] focus:border-transparent transition-all duration-300" />
                <div v-if="form.errors.base_price" class="text-[#FF135F] text-sm mt-1">{{ form.errors.base_price }}</div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-[#2E2E38] mb-1">Description</label>
              <textarea v-model="form.description" rows="4" class="w-full border border-[#E9E1DC] rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FF135F] focus:border-transparent transition-all duration-300"></textarea>
              <div v-if="form.errors.description" class="text-[#FF135F] text-sm mt-1">{{ form.errors.description }}</div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div>
                <label class="block text-sm font-medium text-[#2E2E38] mb-1">Capacité</label>
                <input v-model="form.capacity" type="number" min="1" class="w-full border border-[#E9E1DC] rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FF135F] focus:border-transparent transition-all duration-300" />
                <div v-if="form.errors.capacity" class="text-[#FF135F] text-sm mt-1">{{ form.errors.capacity }}</div>
              </div>
              <div>
                <label class="block text-sm font-medium text-[#2E2E38] mb-1">Quantité</label>
                <input v-model="form.stock_quantity" type="number" min="1" class="w-full border border-[#E9E1DC] rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FF135F] focus:border-transparent transition-all duration-300" />
                <div v-if="form.errors.stock_quantity" class="text-[#FF135F] text-sm mt-1">{{ form.errors.stock_quantity }}</div>
              </div>
              <div>
                <label class="block text-sm font-medium text-[#2E2E38] mb-1">Statut</label>
                <select v-model="form.status" class="w-full border border-[#E9E1DC] rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FF135F] focus:border-transparent transition-all duration-300">
                  <option value="active">Actif</option>
                  <option value="inactive">Inactif</option>
                </select>
                <div v-if="form.errors.status" class="text-[#FF135F] text-sm mt-1">{{ form.errors.status }}</div>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-[#2E2E38] mb-1">Aménagements (séparés par des virgules)</label>
              <input v-model="amenitiesInput" type="text" class="w-full border border-[#E9E1DC] rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#FF135F] focus:border-transparent transition-all duration-300" placeholder="WiFi, TV, Salle de bain privée" />
              <div v-if="form.errors.amenities" class="text-[#FF135F] text-sm mt-1">{{ form.errors.amenities }}</div>
            </div>

            <div>
              <label class="block text-sm font-medium text-[#2E2E38] mb-1">Images (plusieurs possibles)</label>
              <div class="border border-dashed border-[#E9E1DC] rounded-xl p-6 text-center hover:bg-[#F4F3F3] transition-colors duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto text-[#FF135F]/50 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <input
                  type="file"
                  multiple
                  @change="e => form.images = Array.from(e.target.files)"
                  class="w-full opacity-0 absolute inset-0 cursor-pointer"
                  accept="image/*"
                />
                <p class="text-[#2E2E38]/70 text-sm">Glissez-déposez vos images ici ou <span class="text-[#FF135F] font-medium">parcourir</span></p>
                <p class="text-[#2E2E38]/50 text-xs mt-1">Formats acceptés: JPG, PNG, GIF</p>
              </div>
              <div v-if="form.errors.images" class="text-[#FF135F] text-sm mt-1">{{ form.errors.images }}</div>
            </div>

            <div class="flex justify-end">
              <button
                  type="submit"
                  class="px-6 py-3 bg-[#FF135F] text-white rounded-xl font-medium hover:bg-[#FF135F]/90 transition-all duration-300 shadow-sm flex items-center"
                  :disabled="form.processing"
              >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ isEdit ? 'Mettre à jour' : 'Créer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedProLayout>
</template>