<script setup>
import {ref, computed, watch} from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {router} from "@inertiajs/vue3";

const props = defineProps({
  roomTypes: Array,
  roomTypeLikes: Array
})

const currentIndex = ref(0)
const rooms = ref(props.roomTypes ?? [])
const currentRoom = computed(() => rooms.value[currentIndex.value] ?? null)
const animate = ref(false)
const heartClicked = ref(false)
const xClicked = ref(false)
const imageIndex = ref(0)

watch(currentIndex, () => imageIndex.value = 0)

function getImages(images) {
  return Array.isArray(images) ? images : JSON.parse(images)
}

function getRoomImageUrl(images, idx) {
  let imgs = getImages(images)
  return imgs.length ? imgs[idx] : null
}

function nextImage(images) {
  let imgs = getImages(images)
  if (imgs.length === 0) return
  if (imageIndex.value < imgs.length - 1) imageIndex.value++
  else imageIndex.value = 0
}

function prevImage(images) {
  let imgs = getImages(images)
  if (imgs.length === 0) return
  if (imageIndex.value > 0) imageIndex.value--
  else imageIndex.value = imgs.length - 1
}

const proposedPrice = ref('')
const showChat = ref(false)
const negotiationSent = ref({})
const currentNegotiation = ref(null)
const countdownTime = ref(null)

const submitNegotiation = () => {
  if (proposedPrice.value <= 0) return
  const price = proposedPrice.value
  const hotelId = rooms.value[currentIndex.value].id
  negotiationSent.value[hotelId] = {
    price,
    sentAt: new Date(),
    expiresAt: new Date(new Date().getTime() + 3 * 60 * 60 * 1000)
  }
  currentNegotiation.value = negotiationSent.value[hotelId]
  showChat.value = false
  proposedPrice.value = ''
  alert(`💬 Votre prix de ${price}€ a été envoyé ! Il sera étudié dans un délai de 3 heures.`)
  nextRoom()
}

const nextRoom = () => {
  animate.value = true
  setTimeout(() => {
    animate.value = false
    if (currentIndex.value < rooms.value.length - 1) currentIndex.value++
    else currentIndex.value = 0
    heartClicked.value = false
    xClicked.value = false
    currentNegotiation.value = null
  }, 275)
}

const prevRoom = () => {
  if (currentIndex.value > 0) currentIndex.value--
  else currentIndex.value = rooms.value.length - 1
  heartClicked.value = false
  xClicked.value = false
  currentNegotiation.value = null
}

const likedRoomIds = ref(new Set(props.roomTypeLikes ?? []))

function isLiked(roomTypeId) {
  return likedRoomIds.value.has(roomTypeId)
}

function toggleLike() {
  let id = currentRoom.value.id
  let previouslyLiked = likedRoomIds.value.has(id)
  if (previouslyLiked) likedRoomIds.value.delete(id)
  else likedRoomIds.value.add(id)
  heartClicked.value = !previouslyLiked
  router.post('/room-type-like-toggle', { room_type_id: id }, {
    preserveScroll: true,
    onError: () => {
      if (previouslyLiked) likedRoomIds.value.add(id)
      else likedRoomIds.value.delete(id)
      heartClicked.value = false
    },
    onSuccess: () => {
      nextRoom()
    }
  })
}

function dislikeRoom() {
  xClicked.value = true
  setTimeout(nextRoom, 200)
}

const openNegotiation = () => {
  const hotelId = rooms.value[currentIndex.value].id
  const existing = negotiationSent.value[hotelId]
  if (existing) currentNegotiation.value = existing
  showChat.value = true
}

const updateCountdown = () => {
  const hotelId = rooms.value[currentIndex.value]?.id
  const nego = negotiationSent.value[hotelId]
  if (nego) {
    const now = new Date()
    const remaining = new Date(nego.expiresAt) - now
    if (remaining > 0) {
      const hours = Math.floor(remaining / (1000 * 60 * 60))
      const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60))
      const seconds = Math.floor((remaining % (1000 * 60)) / 1000)
      countdownTime.value = `${hours}h ${minutes}m ${seconds}s`
    } else {
      countdownTime.value = 'Réponse expirée'
    }
  }
}

setInterval(updateCountdown, 1000)
</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Découvrir des chambres</h2>
    </template>
    <div class="relative min-h-screen bg-[#d6E1E5] flex flex-col items-center pt-10 pb-24">
      <div
        v-if="rooms.length && currentRoom"
        class="relative w-[92%] max-w-5xl rounded-[30px] overflow-hidden bg-white shadow-2xl mt-10 transition-transform duration-300 ease-in-out"
        :class="{ 'scale-90 opacity-50': animate }"
      >
        <div class="absolute top-0 left-0 w-full h-2 flex bg-gray-300 px-2 pt-1 z-10">
          <template v-for="(room, index) in rooms" :key="index">
            <div
              class="flex-1 mx-1 h-1 rounded-full"
              :class="{
                'bg-pink-500': index <= currentIndex,
                'bg-white': index > currentIndex
              }"
            ></div>
          </template>
        </div>
        <div class="relative w-full bg-white h-[500px] overflow-hidden select-none">
          <img
            v-if="currentRoom.images && getImages(currentRoom.images).length"
            :src="getRoomImageUrl(currentRoom.images, imageIndex)"
            :alt="currentRoom.name"
            class="w-full h-[500px] object-cover transition-all duration-300"
          />
          <button
            v-if="getImages(currentRoom.images).length > 1"
            class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-600 p-1 rounded-full shadow"
            @click.stop="prevImage(currentRoom.images)"
            aria-label="Image précédente"
            style="z-index:10">
            ‹
          </button>
          <button
            v-if="getImages(currentRoom.images).length > 1"
            class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-600 p-1 rounded-full shadow"
            @click.stop="nextImage(currentRoom.images)"
            aria-label="Image suivante"
            style="z-index:10">
            ›
          </button>
          <div v-if="getImages(currentRoom.images).length > 1" class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1">
            <span v-for="(img, idx) in getImages(currentRoom.images)" :key="idx" class="w-2 h-2 rounded-full"
                  :class="{'bg-blue-600': imageIndex === idx, 'bg-gray-400': imageIndex !== idx}">
            </span>
          </div>
        </div>

        <div class="absolute bottom-5 left-1/2 -translate-x-1/2 w-[60%] bg-[#d9e2e7] rounded-[30px] px-8 py-5 flex justify-between items-center shadow-md">
          <div>
            <h2 class="text-lg font-bold text-gray-800">{{ currentRoom.hotel?.name }} - {{ currentRoom.name }}</h2>
            <p class="text-sm text-gray-700">{{ currentRoom.hotel?.location }}</p>
          </div>
          <div class="text-lg font-semibold text-gray-900">
            {{ currentRoom.price }}€
          </div>
        </div>

        <div class="absolute bottom-6 left-0 right-0 flex justify-between px-10 z-20">
          <button
            @click="dislikeRoom"
            class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition transform"
            :class="{ 'scale-125': xClicked }"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
          <div class="flex items-center gap-4">
            <button
              @click="toggleLike"
              class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition transform"
              :class="{ 'scale-125': heartClicked }"
              title="Ajouter à mes coups de cœur"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8"
                   :class="{ 'text-pink-500': isLiked(currentRoom.id), 'text-pink-300': !isLiked(currentRoom.id) }" fill="currentColor"
                   viewBox="0 0 20 20">
                <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.172a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
              </svg>
            </button>
            <div class="flex flex-col items-center">
              <button
                @click.stop="openNegotiation"
                class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition"
                title="Proposer un prix"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 8h10M7 12h6m-6 8h8a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v13l4-4z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
      <div v-else>
        <p class="text-center text-gray-500 mt-16">Aucune chambre disponible.</p>
      </div>

      <transition name="fade">
        <div
          v-if="showChat"
          class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 bg-white rounded-xl shadow-xl p-4 z-50 border border-gray-300"
        >
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-700">
              {{ currentNegotiation ? 'Suivi de votre proposition' : 'Proposer un prix' }}
            </h3>
            <button @click="showChat = false" class="text-gray-500 hover:text-red-500 text-sm">✖</button>
          </div>
          <template v-if="!currentNegotiation">
            <form @submit.prevent="submitNegotiation">
              <div class="flex items-center border rounded-lg overflow-hidden">
                <input
                  v-model.number="proposedPrice"
                  type="number"
                  min="1"
                  placeholder="Montant"
                  class="w-full px-4 py-2 focus:outline-none"
                  required
                />
                <span class="px-3 text-gray-600 font-semibold">€</span>
              </div>
              <button
                type="submit"
                class="w-full mt-4 bg-pink-500 hover:bg-pink-600 text-white font-semibold py-2 px-4 rounded-lg transition"
              >
                Envoyer
              </button>
            </form>
          </template>
          <template v-else>
            <div class="text-sm text-gray-500 mb-2">
              Temps de réponse estimé : <strong>{{ countdownTime }}</strong>
            </div>
            <div class="bg-gray-100 p-3 rounded-lg shadow text-sm text-gray-800">
              💬 Vous avez proposé <strong>{{ currentNegotiation.price }}€</strong><br />
              L’hôtel a jusqu’à 3h pour répondre.
            </div>
          </template>
        </div>
      </transition>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
