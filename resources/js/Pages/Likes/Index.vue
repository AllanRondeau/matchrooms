<script setup>
import {router} from '@inertiajs/vue3'
import {ref} from 'vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

const props = defineProps({
  likes: Array
})

const likesList = ref(props.likes.map(l => ({...l})))

function removeLike(like, idx) {
  if (window.confirm('Retirer cette annonce de vos likes ?')) {
    router.delete(route('likes.destroy', like.id), {
      preserveScroll: true,
      onSuccess: () => {
        likesList.value.splice(idx, 1)
      }
    })
  }
}
</script>

<template>
  <AuthenticatedLayout>
    <div class="max-w-2xl mx-auto py-10">
      <h1 class="text-2xl font-bold mb-6 text-center">Mes Likes</h1>
      <div v-if="likesList.length === 0" class="text-gray-500">
        Aucune annonce likée pour le moment.
      </div>
      <div v-else class="space-y-6">
        <div v-for="(like, idx) in likesList" :key="like.id" class="bg-white rounded-lg shadow px-4 py-3 relative flex items-center">
          <button
            class="absolute top-2 right-2 text-gray-400 hover:text-red-600 transition"
            @click="removeLike(like, idx)"
            aria-label="Retirer ce like"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <div class="flex-1">
            <div class="font-semibold text-lg">
              {{ like.room_type?.hotel?.name || 'Hôtel inconnu' }} - {{ like.room_type?.name || '' }}
            </div>
            <div class="text-gray-500">
              {{ like.room_type?.hotel?.location || '' }}
            </div>
            <div class="text-gray-600 line-clamp-2 mt-2">
              {{ like.room_type?.description || '' }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
