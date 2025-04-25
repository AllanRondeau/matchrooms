<script setup>
import {ref, computed, watch} from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {router} from "@inertiajs/vue3";


const props = defineProps({
  roomTypes: Array
})

const currentIndex = ref(0)
const rooms = ref(props.roomTypes ?? [])
const currentRoom = computed(() => rooms.value[currentIndex.value] ?? null)
const animate = ref(false);
const heartClicked = ref(false);
const xClicked = ref(false);

const imageIndex = ref(0)

watch(currentIndex, () => {
  imageIndex.value = 0
})

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
  if (imageIndex.value < imgs.length - 1) {
    imageIndex.value++
  } else {
    imageIndex.value = 0
  }
}

function prevImage(images) {
  let imgs = getImages(images)
  if (imgs.length === 0) return
  if (imageIndex.value > 0) {
    imageIndex.value--
  } else {
    imageIndex.value = imgs.length - 1
  }
}
const proposedPrice = ref('');
const showChat = ref(false);
const negotiationSent = ref({});
const currentNegotiation = ref(null);
const countdownTime = ref(null);

const submitNegotiation = () => {
  if (proposedPrice.value <= 0) return;

  const price = proposedPrice.value;
  const hotelId = rooms[currentIndex.value].id;

  // Enregistrer la négo
  negotiationSent.value[hotelId] = {
    price,
    sentAt: new Date(),
    expiresAt: new Date(new Date().getTime() + 3 * 60 * 60 * 1000), // +3h
  };

  currentNegotiation.value = negotiationSent.value[hotelId];
  showChat.value = false;
  proposedPrice.value = '';

  alert(`💬 Votre prix de ${price}€ a été envoyé ! Il sera étudié dans un délai de 3 heures.`);

  // Passer à la chambre suivante
  nextRoom();
};

const nextRoom = () => {
  if (currentIndex.value < rooms.value.length - 1) currentIndex.value++; else currentIndex.value = 0
}
const prevRoom = () => {
  if (currentIndex.value > 0) currentIndex.value--; else currentIndex.value = rooms.value.length - 1
}

const likedRoomIds = ref(new Set(props.roomTypeLikes ?? []))

function isLiked(roomTypeId) {
  return likedRoomIds.value.has(roomTypeId)
}

function toggleLike() {
  let id = currentRoom.value.id
  let previouslyLiked = likedRoomIds.value.has(id)
  if (previouslyLiked) {
    likedRoomIds.value.delete(id)
  } else {
    likedRoomIds.value.add(id)
  }
// Affiche la popup
const openNegotiation = () => {
  const hotelId = rooms[currentIndex.value].id;
  const existing = negotiationSent.value[hotelId];

  if (existing) {
    currentNegotiation.value = existing;
  }

  showChat.value = true;
};

// Compte à rebours live
const updateCountdown = () => {
  const hotelId = rooms[currentIndex.value].id;
  const nego = negotiationSent.value[hotelId];

  if (nego) {
    const now = new Date();
    const remaining = new Date(nego.expiresAt) - now;

    if (remaining > 0) {
      const hours = Math.floor(remaining / (1000 * 60 * 60));
      const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
      countdownTime.value = `${hours}h ${minutes}m ${seconds}s`;
    } else {
      countdownTime.value = 'Réponse expirée';
    }
  }
};

  router.post('/room-type-like-toggle', { room_type_id: id }, {
    preserveScroll: true,
    onError: () => {
      if (previouslyLiked) {
        likedRoomIds.value.add(id)
      } else {
        likedRoomIds.value.delete(id)
      }
    }
  })
}

setInterval(updateCountdown, 1000);


</script>

<template>
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Découvrir des chambres</h2>
    </template>
    <div v-if="rooms.length > 0" class="max-w-md mx-auto">
      <div class="p-8 border rounded shadow mb-6 text-center flex flex-col items-center gap-4 bg-white">
        <div class="relative w-full">
          <img
            v-if="currentRoom.images && getImages(currentRoom.images).length"
            :src="getRoomImageUrl(currentRoom.images, imageIndex)"
            class="w-full rounded-md mb-4"
            alt="Photo de la chambre"
          />
          <button
            v-if="getImages(currentRoom.images).length > 1"
            class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-600 p-1 rounded-full shadow"
            @click="prevImage(currentRoom.images)"
            aria-label="Image précédente"
            style="z-index:10">
            ‹
          </button>
          <button
            v-if="getImages(currentRoom.images).length > 1"
            class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-gray-600 p-1 rounded-full shadow"
            @click="nextImage(currentRoom.images)"
            aria-label="Image suivante"
            style="z-index:10">
            ›
          </button>
          <div v-if="getImages(currentRoom.images).length > 1" class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1">
        <span v-for="(img, idx) in getImages(currentRoom.images)"
              :key="idx"
              class="w-2 h-2 rounded-full"
              :class="{'bg-blue-600': imageIndex === idx, 'bg-gray-400': imageIndex !== idx}">
        </span>
          </div>
        </div>
        <h3 class="text-lg font-bold">
          {{ currentRoom.hotel?.name }} - {{ currentRoom.name }}
        </h3>
        <span class="text-gray-500 mb-1">
                {{ currentRoom.hotel?.location }}
            </span>
        <div class="font-semibold text-green-700 text-xl mb-3">
          {{ currentRoom.price }}&nbsp;€
        </div>
        <div class="text-gray-600 mb-3">
          {{ currentRoom.description }}
        </div>
        <div class="flex space-x-4 mt-4">
          <button
            @click="toggleLike"
            :class="{'text-pink-500': isLiked(currentRoom.id), 'text-gray-300': !isLiked(currentRoom.id)}"
            class="transition hover:scale-110"
            title="Ajouter à mes coups de cœur"
          >
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" class="w-8 h-8">
              <path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.172a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
            </svg>
          </button>

          <button
            @click="nextRoom"
            class="p-4 bg-gray-200 rounded-full hover:bg-gray-300 transition"
            aria-label="Passer à la chambre suivante"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
        <div class="flex items-center justify-between w-full mt-4">
          <button
            @click="prevRoom"
            class="text-gray-400 hover:text-gray-700 transition"
            aria-label="Précédent"
          >Précédent
          </button>
        </div>
      </div>
    </div>
    <div v-else>
      <p class="text-center text-gray-500 mt-16">Aucune chambre disponible.</p>
    </div>
  </AuthenticatedLayout>
</template>
