<script setup>
import { ref } from 'vue';


const rooms = [
  {
    id: 1,
    name: "WIT Hotel",
    location: "Paris 75009",
    image: "https://placehold.co/1000x600?text=WIT+Hotel",
    price: 80,
  },
  {
    id: 2,
    name: "Grand Hôtel Royal",
    location: "Paris, France",
    image: "https://placehold.co/1000x600?text=Grand+Hôtel+Royal",
    price: 150,
  },
];

const currentIndex = ref(0);
const animate = ref(false);
const heartClicked = ref(false);
const xClicked = ref(false);

const likeRoom = () => {
  heartClicked.value = true;
  triggerAnimation();
  console.log('Liked:', rooms[currentIndex.value]);
};

const dislikeRoom = () => {
  xClicked.value = true;
  triggerAnimation();
  console.log('Disliked:', rooms[currentIndex.value]);
};

const saveRoom = () => {
  showChat.value = !showChat.value;
};

const nextRoom = () => {
  currentIndex.value = (currentIndex.value + 1) % rooms.length;
};

const triggerAnimation = () => {
  animate.value = true;
  setTimeout(() => {
    animate.value = false;
    heartClicked.value = false;
    xClicked.value = false;
    nextRoom();
  }, 300);
};

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

setInterval(updateCountdown, 1000);


</script>

<template>
  <div class="relative min-h-screen bg-[#d9e2e7] flex flex-col items-center">

    <!-- Header -->
    <header class="w-full flex items-center justify-between px-6 py-4 bg-[#d9e2e7]">
      <button class="text-3xl">☰</button>

      <div class="flex items-center space-x-2">
        <img src="/images/logoMR.png" alt="Logo MatchRoom"
             class="h-10 mb-4 transform transition-transform duration-300 hover:scale-105" />
      </div>

      <div class="w-8 h-8"></div>
    </header>

    <!-- Main card -->
    <div
        class="relative w-[92%] max-w-5xl rounded-[30px] overflow-hidden bg-white shadow-2xl mt-20 transition-transform duration-300 ease-in-out"
        :class="{ 'scale-90 opacity-50': animate }"
    >

      <!-- Barre de progression -->
      <div class="absolute top-0 left-0 w-full h-2 flex bg-gray-300 px-2 pt-1">
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

      <!-- Image -->
      <img
          :src="rooms[currentIndex].image"
          :alt="rooms[currentIndex].name"
          class="w-full h-[500px] object-cover"
      />

      <!-- Bande infos en bas centrée -->
      <div class="absolute bottom-5 left-1/2 -translate-x-1/2 w-[60%] bg-[#d9e2e7] rounded-[30px] px-8 py-5 flex justify-between items-center shadow-md">
        <div>
          <h2 class="text-lg font-bold text-gray-800">{{ rooms[currentIndex].name }}</h2>
          <p class="text-sm text-gray-700">{{ rooms[currentIndex].location }}</p>
        </div>

        <div class="text-lg font-semibold text-gray-900">
          {{ rooms[currentIndex].price }}€
        </div>
      </div>

      <!-- Icônes action (en dehors de la bande bleue) -->
      <div class="absolute bottom-6 left-0 right-0 flex justify-between px-10">
        <!-- Dislike -->
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
          <!-- Like -->
          <button
              @click="likeRoom"
              class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition transform"
              :class="{ 'scale-125': heartClicked }"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="{ 'text-pink-500': heartClicked, 'text-pink-300': !heartClicked }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </button>

          <!-- Chat -->
          <div class="flex flex-col items-center">
            <button
                @click="openNegotiation"
                class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition"
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

    <!-- Popup discussion hors carte -->
    <!-- POPUP DE NÉGOCIATION -->
    <transition name="fade">
      <div
          v-if="showChat"
          class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 bg-white rounded-xl shadow-xl p-4 z-50 border border-gray-300"
      >
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-700">
            {{ currentNegotiation ? 'Suivi de votre proposition' : 'Proposer un prix' }}
          </h3>
          <button @click="showChat = false" class="text-gray-500 hover:text-red-500 text-sm">✖</button>
        </div>

        <!-- Si aucune négo envoyée -->
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

        <!-- Si négo déjà envoyée -->
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
