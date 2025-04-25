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
  {
    id: 3,
    name: "Hôtel",
    location: "Paris, France",
    image: "https://placehold.co/1000x600?text=Grand+Hôtel+Royal",
    price: 45,
  },
];

const currentIndex = ref(0);
const animate = ref(false);
const heartClicked = ref(false);
const xClicked = ref(false);
const showChat = ref(false);
const proposedPrice = ref('');
const negotiationSent = ref({});
const currentNegotiation = ref(null);
const countdownTime = ref(null);

// Sidebar toggle
const sidebarVisible = ref(false);
const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value;
};

const likeRoom = () => {
  const currentRoom = rooms[currentIndex.value];
  const existing = JSON.parse(localStorage.getItem('likedRooms')) || [];

  if (!existing.some(room => room.id === currentRoom.id)) {
    existing.push(currentRoom);
    localStorage.setItem('likedRooms', JSON.stringify(existing));
  }

  heartClicked.value = true;
  triggerAnimation();
  console.log('Liked and saved:', currentRoom);
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

const submitNegotiation = () => {
  if (proposedPrice.value <= 0) return;

  const room = rooms[currentIndex.value];

  const negotiation = {
    id: room.id,
    name: room.name,
    location: room.location,
    image: room.image,
    basePrice: room.price,
    proposedPrice: proposedPrice.value,
    sentAt: new Date().toISOString(),
    expiresAt: new Date(new Date().getTime() + 3 * 60 * 60 * 1000).toISOString(), // expire dans 3h
  };

  // Sauvegarder dans localStorage
  const existingNegotiations = JSON.parse(localStorage.getItem('negotiations')) || [];
  const alreadyExists = existingNegotiations.find(n => n.id === negotiation.id);

  if (!alreadyExists) {
    existingNegotiations.push(negotiation);
    localStorage.setItem('negotiations', JSON.stringify(existingNegotiations));
  }

  // Logique actuelle
  negotiationSent.value[room.id] = {
    price: proposedPrice.value,
    sentAt: new Date(),
    expiresAt: new Date(new Date().getTime() + 3 * 60 * 60 * 1000),
  };

  currentNegotiation.value = negotiationSent.value[room.id];
  showChat.value = false;
  proposedPrice.value = '';

  alert(`💬 Votre prix de ${negotiation.proposedPrice}€ a été envoyé ! Il sera étudié dans un délai de 3 heures.`);

  nextRoom();
};



const openNegotiation = () => {
  const hotelId = rooms[currentIndex.value].id;
  const existing = negotiationSent.value[hotelId];

  if (existing) {
    currentNegotiation.value = existing;
  }

  showChat.value = true;
};

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
  <div class="relative min-h-screen bg-[#d6E1E5] flex flex-col items-center">

    <!-- SIDEBAR -->
    <transition name="slide">
      <aside
          v-if="sidebarVisible"
          class="fixed top-0 left-0 h-full w-64 bg-[#d6E1E5] shadow-md z-50 flex flex-col p-6 space-y-6 border-r border-gray-300"
      >
        <!-- Header du menu -->
        <div class="flex justify-between items-center mb-6">
          <img src="/images/logoMR.png" alt="Logo MatchRoom" class="w-24" />
          <button @click="toggleSidebar" class="text-gray-500 hover:text-pink-500 text-2xl transition">
            ✖
          </button>
        </div>

        <!-- Liens du menu -->
        <nav class="flex flex-col gap-4 text-[#2D2C33] font-semibold">
          <a href="/liked-rooms" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
             <span>Toutes les chambres likées</span>
          </a>
          <a href="/map" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
             <span>Map Finder</span>
          </a>
          <a href="/negociations" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
             <span>Négociations en cours</span>
          </a>
          <a href="#" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
             <span>Mes réservations</span>
          </a>
        </nav>
      </aside>
    </transition>


    <!-- Header -->
    <header class="w-full flex items-center justify-between px-6 py-4 bg-[#d9e2e7]">
      <button class="text-3xl" @click="toggleSidebar">☰</button>
      <div class="flex items-center space-x-2">
        <img src="/images/logoMR.png" alt="Logo MatchRoom"
             class="h-10 mb-4 transform transition-transform duration-300 hover:scale-105" />
      </div>
      <div class="w-8 h-8"></div>
    </header>

    <!-- Main Card -->
    <div
        class="relative w-[92%] max-w-5xl rounded-[30px] overflow-hidden bg-white shadow-2xl mt-20 transition-transform duration-300 ease-in-out"
        :class="{ 'scale-90 opacity-50': animate }"
    >
      <!-- Progression -->
      <div class="absolute top-0 left-0 w-full h-2 flex bg-gray-300 px-2 pt-1">
        <template v-for="(room, index) in rooms" :key="index">
          <div class="flex-1 mx-1 h-1 rounded-full" :class="{ 'bg-pink-500': index <= currentIndex, 'bg-white': index > currentIndex }"></div>
        </template>
      </div>

      <!-- Image -->
      <img :src="rooms[currentIndex].image" :alt="rooms[currentIndex].name" class="w-full h-[500px] object-cover" />

      <!-- Infos -->
      <div class="absolute bottom-5 left-1/2 -translate-x-1/2 w-[60%] bg-[#d9e2e7] rounded-[30px] px-8 py-5 flex justify-between items-center shadow-md">
        <div>
          <h2 class="text-lg font-bold text-gray-800">{{ rooms[currentIndex].name }}</h2>
          <p class="text-sm text-gray-700">{{ rooms[currentIndex].location }}</p>
        </div>
        <div class="text-lg font-semibold text-gray-900">{{ rooms[currentIndex].price }}€</div>
      </div>

      <!-- Actions -->
      <div class="absolute bottom-6 left-0 right-0 flex justify-between px-10">
        <button @click="dislikeRoom" class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition transform" :class="{ 'scale-125': xClicked }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="flex items-center gap-4">
          <button @click="likeRoom" class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition transform" :class="{ 'scale-125': heartClicked }">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="{ 'text-pink-500': heartClicked, 'text-pink-300': !heartClicked }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </button>

          <div class="flex flex-col items-center">
            <button @click="openNegotiation" class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-100 transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-black" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h6m-6 8h8a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v13l4-4z" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Popup Discussion -->
    <transition name="fade">
      <div v-if="showChat" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 bg-white rounded-xl shadow-xl p-4 z-50 border border-gray-300">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-700">
            {{ currentNegotiation ? 'Suivi de votre proposition' : 'Proposer un prix' }}
          </h3>
          <button @click="showChat = false" class="text-gray-500 hover:text-red-500 text-sm">✖</button>
        </div>

        <template v-if="!currentNegotiation">
          <form @submit.prevent="submitNegotiation">
            <div class="flex items-center border rounded-lg overflow-hidden">
              <input v-model.number="proposedPrice" type="number" min="1" placeholder="Montant" class="w-full px-4 py-2 focus:outline-none" required />
              <span class="px-3 text-gray-600 font-semibold">€</span>
            </div>
            <button type="submit" class="w-full mt-4 bg-pink-500 hover:bg-pink-600 text-white font-semibold py-2 px-4 rounded-lg transition">Envoyer</button>
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

.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}
.slide-enter-from {
  transform: translateX(-100%);
  opacity: 0;
}
.slide-leave-to {
  transform: translateX(-100%);
  opacity: 0;
}
</style>
