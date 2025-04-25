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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          {{ isEdit ? 'Modifier un type de chambre' : 'Ajouter un type de chambre' }}
        </h2>
        <Link
          :href="route('pro.dashboard')"
          class="px-4 py-2 bg-gray-300 hover:bg-gray-400 rounded text-gray-800"
        >
          Retour
        </Link>
      </div>
    </template>
    <div class="py-12">
      <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <form @submit.prevent="submit" enctype="multipart/form-data" class="p-6 space-y-6">
            <input type="hidden" v-model="form.hotel_id" />
            <div>
              <label class="block text-sm font-medium text-gray-700">Nom</label>
              <input v-model="form.name" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
              <div v-if="form.errors.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Description</label>
              <textarea v-model="form.description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
              <div v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Prix de base (€)</label>
              <input v-model="form.base_price" type="number" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
              <div v-if="form.errors.base_price" class="text-red-600 text-sm mt-1">{{ form.errors.base_price }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Capacité (nombre de personnes)</label>
              <input v-model="form.capacity" type="number" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
              <div v-if="form.errors.capacity" class="text-red-600 text-sm mt-1">{{ form.errors.capacity }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Quantité en stock</label>
              <input v-model="form.stock_quantity" type="number" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
              <div v-if="form.errors.stock_quantity" class="text-red-600 text-sm mt-1">{{ form.errors.stock_quantity }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Aménagements (séparés par des virgules)</label>
              <input v-model="amenitiesInput" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="WiFi, TV, Salle de bain privée" />
              <div v-if="form.errors.amenities" class="text-red-600 text-sm mt-1">{{ form.errors.amenities }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Images (plusieurs possibles)</label>
              <input
                type="file"
                multiple
                @change="e => form.images = Array.from(e.target.files)"
                class="mt-1 block w-full"
                accept="image/*"
              />
              <div v-if="form.errors.images" class="text-red-600 text-sm mt-1">{{ form.errors.images }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Statut</label>
              <select v-model="form.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="active">Actif</option>
                <option value="inactive">Inactif</option>
              </select>
              <div v-if="form.errors.status" class="text-red-600 text-sm mt-1">{{ form.errors.status }}</div>
            </div>
            <div class="flex justify-end">
              <button
                type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                :disabled="form.processing"
              >
                {{ isEdit ? 'Mettre à jour' : 'Créer' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedProLayout>
</template>
