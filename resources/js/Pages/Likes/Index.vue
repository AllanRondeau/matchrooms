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
      <div class="text-center mb-8">
        <div class="inline-flex items-center px-3 py-1.5 mb-2 rounded-full bg-[#F7E6E2] border border-[#FF135F]/20 text-[#FF135F] text-sm font-medium">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
          </svg>
          Vos favoris
        </div>
        <h1 class="text-3xl font-bold text-[#0E2748]">Mes Likes</h1>
      </div>
      
      <div v-if="likesList.length === 0" class="text-center py-12">
        <div class="bg-white rounded-xl shadow-md p-8 max-w-md mx-auto border border-[#E9E1DC]">
          <div class="text-[#FF135F] mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </div>
          <h2 class="text-xl font-semibold text-[#2E2E38] mb-2">Aucune annonce likée</h2>
          <p class="text-[#2E2E38]/70 mb-6">Explorez des chambres et ajoutez-les à vos favoris pour les retrouver ici.</p>
          <a href="/" class="inline-block bg-[#FF135F] text-white px-6 py-3 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:shadow-[#FF135F]/20 hover:-translate-y-0.5">
            Découvrir des chambres
          </a>
        </div>
      </div>
      
      <div v-else class="space-y-4">
        <div v-for="(like, idx) in likesList" :key="like.id" class="bg-white rounded-xl shadow-md overflow-hidden border border-[#E9E1DC] hover:shadow-lg transition-all duration-300">
          <div class="flex flex-col md:flex-row">
            <div class="md:w-1/3">
              <div class="h-full w-full bg-[#F4F3F3] flex items-center justify-center">
                <img 
                  :src="like.room_type?.image_url || 'https://placehold.co/300x200?text=Hotel+Room'" 
                  :alt="like.room_type?.name" 
                  class="w-full h-48 md:h-full object-cover"
                />
              </div>
            </div>
            <div class="p-6 md:w-2/3 relative">
              <button
                class="absolute top-4 right-4 text-[#2E2E38]/40 hover:text-[#FF135F] transition-colors duration-300"
                @click="removeLike(like, idx)"
                aria-label="Retirer ce like"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
              
              <div class="mb-4">
                <h2 class="text-xl font-semibold text-[#0E2748] mb-1">
                  {{ like.room_type?.hotel?.name || 'Hôtel inconnu' }} - {{ like.room_type?.name || '' }}
                </h2>
                <p class="text-[#2E2E38]/70 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  {{ like.room_type?.hotel?.location || '' }}
                </p>
              </div>
              
              <p class="text-[#2E2E38]/70 text-sm mb-4 line-clamp-2">
                {{ like.room_type?.description || '' }}
              </p>
              
              <div class="flex justify-between items-center">
                <div class="flex text-amber-400">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </div>
                <div class="text-xl font-bold text-[#FF135F]">
                  {{ like.room_type?.price || '?' }}€
                  <span class="text-sm text-[#2E2E38]/70">/nuit</span>
                </div>
              </div>
              
              <div class="mt-4 flex justify-end">
                <button class="px-4 py-2 bg-[#FF135F] text-white rounded-xl font-medium hover:bg-[#FF135F]/90 transition-all duration-300 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                  </svg>
                  Négocier
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>