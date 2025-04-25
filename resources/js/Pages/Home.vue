<script setup>
import { ref } from 'vue';
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";

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
  <div class="relative min-h-screen bg-[#F4F3F3] flex flex-col items-center">
    <!-- SIDEBAR -->
    <transition name="slide">
      <aside
        v-if="sidebarVisible"
        class="fixed top-0 left-0 h-full w-72 bg-white shadow-xl z-50 flex flex-col p-6 space-y-6 border-r border-[#E9E1DC]"
      >
        <!-- Header du menu -->
        <div class="flex justify-between items-center mb-6">
          <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/logoMR-TakbHOAaljFXg5P560GFVsP1M2q0Tx.png" alt="Logo MatchRoom" class="h-10" />
          <button @click="toggleSidebar" class="text-[#2E2E38] hover:text-[#FF135F] transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Liens du menu -->
        <nav class="flex flex-col gap-2 text-[#2E2E38] font-medium">
          <a href="/liked-rooms" class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
            <span>Chambres likées</span>
          </a>
          <a href="/map" class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            <span>Map Finder</span>
          </a>
          <a href="/negociations" class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
            </svg>
            <span>Négociations en cours</span>
          </a>
          <a href="#" class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Mes réservations</span>
          </a>
          <div class="pt-4 mt-4 border-t border-[#E9E1DC]">
            <NavLink
              :href="route('logout')"
              method="post"
              class="flex items-center gap-3 hover:bg-[#F7E6E2] px-4 py-3 rounded-xl transition-all duration-300 text-[#2E2E38]"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              Se déconnecter
            </NavLink>
          </div>
        </nav>
      </aside>
    </transition>

    <!-- Header -->
    <header class="w-full flex items-center justify-between px-6 py-4 bg-white shadow-sm border-b border-[#E9E1DC]">
      <button @click="toggleSidebar" class="text-[#2E2E38] hover:text-[#FF135F] transition-colors duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <div class="flex items-center">
        <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/logoMR-TakbHOAaljFXg5P560GFVsP1M2q0Tx.png" alt="Logo MatchRoom" class="h-10 transform transition-transform duration-300 hover:scale-105" />
      </div>

      <div class="w-6"></div>
    </header>

    <!-- Main Card -->
    <div
      class="relative w-[92%] max-w-5xl rounded-2xl overflow-hidden bg-white shadow-xl mt-12 transition-transform duration-300 ease-in-out border border-[#E9E1DC]"
      :class="{ 'scale-90 opacity-50': animate }"
    >
      <!-- Progression -->
      <div class="absolute top-0 left-0 w-full h-1 flex bg-[#E9E1DC] z-10">
        <template v-for="(room, index) in rooms" :key="index">
          <div class="flex-1 mx-0.5" :class="{ 'bg-[#FF135F]': index <= currentIndex, 'bg-[#E9E1DC]': index > currentIndex }"></div>
        </template>
      </div>

      <!-- Image -->
      <img :src="rooms[currentIndex].image" :alt="rooms[currentIndex].name" class="w-full h-[500px] object-cover" />

      <!-- Infos -->
      <div class="absolute bottom-6 left-1/2 -translate-x-1/2 w-[70%] bg-white/90 backdrop-blur-sm rounded-2xl px-8 py-5 flex justify-between items-center shadow-lg border border-[#E9E1DC]">
        <div>
          <h2 class="text-xl font-bold text-[#0E2748]">{{ rooms[currentIndex].name }}</h2>
          <p class="text-[#2E2E38]/70 flex items-center mt-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            {{ rooms[currentIndex].location }}
          </p>
        </div>
        <div class="text-xl font-semibold text-[#FF135F]">{{ rooms[currentIndex].price }}€</div>
      </div>

      <!-- Actions -->
      <div class="absolute bottom-6 left-0 right-0 flex justify-between px-10">
        <button @click="dislikeRoom" class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-[#F7E6E2] transition-all duration-300 transform border border-[#E9E1DC]" :class="{ 'scale-125': xClicked }">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#2E2E38]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>

        <div class="flex items-center gap-4">
          <button @click="likeRoom" class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-[#F7E6E2] transition-all duration-300 transform border border-[#E9E1DC]" :class="{ 'scale-125': heartClicked }">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" :class="{ 'text-[#FF135F] fill-[#FF135F]': heartClicked, 'text-[#FF135F]': !heartClicked }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
          </button>

          <div class="flex flex-col items-center">
            <button @click="openNegotiation" class="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-[#F7E6E2] transition-all duration-300 border border-[#E9E1DC]">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Popup Discussion -->
    <transition name="fade">
      <div v-if="showChat" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 bg-white rounded-xl shadow-xl p-6 z-50 border border-[#E9E1DC]">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-semibold text-[#0E2748]">
            {{ currentNegotiation ? 'Suivi de votre proposition' : 'Proposer un prix' }}
          </h3>
          <button @click="showChat = false" class="text-[#2E2E38] hover:text-[#FF135F] transition-colors duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <template v-if="!currentNegotiation">
          <div class="mb-6">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 rounded-full bg-[#F7E6E2] flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div>
                <div class="text-sm text-[#2E2E38]/70">Prix affiché</div>
                <div class="text-xl font-semibold text-[#2E2E38]">{{ rooms[currentIndex].price }}€</div>
              </div>
            </div>
            
            <form @submit.prevent="submitNegotiation" class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-[#2E2E38] mb-1">Votre proposition</label>
                <div class="flex items-center border border-[#E9E1DC] rounded-xl overflow-hidden focus-within:ring-2 focus-within:ring-[#FF135F] focus-within:border-transparent">
                  <input 
                    v-model.number="proposedPrice" 
                    type="number" 
                    min="1" 
                    placeholder="Montant" 
                    class="w-full px-4 py-3 focus:outline-none" 
                    required 
                  />
                  <span class="px-4 text-[#2E2E38] font-semibold">€</span>
                </div>
              </div>
              
              <button 
                type="submit" 
                class="w-full bg-[#FF135F] hover:bg-[#FF135F]/90 text-white font-medium py-3 px-4 rounded-xl transition-all duration-300 flex items-center justify-center"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
                Envoyer ma proposition
              </button>
            </form>
          </div>
          
          <div class="text-xs text-[#2E2E38]/60 text-center">
            L'hôtelier a jusqu'à 3 heures pour répondre à votre proposition
          </div>
        </template>

        <template v-else>
          <div class="bg-[#F7E6E2] p-4 rounded-xl mb-4">
            <div class="text-sm text-[#2E2E38]/70 mb-2 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Temps de réponse estimé : <strong>{{ countdownTime }}</strong>
            </div>
            <div class="flex items-center">
              <div class="w-10 h-10 rounded-full bg-white flex items-center justify-center mr-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#FF135F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
              </div>
              <div>
                <div class="text-sm font-medium text-[#0E2748]">Vous avez proposé</div>
                <div class="text-lg font-semibold text-[#FF135F]">{{ currentNegotiation.price }}€</div>
              </div>
            </div>
          </div>
          
          <div class="text-sm text-[#2E2E38]/70 mb-4">
            L'hôtel a jusqu'à 3h pour répondre à votre proposition. Vous recevrez une notification dès qu'une réponse sera disponible.
          </div>
          
          <button @click="showChat = false" class="w-full bg-[#E9E1DC] hover:bg-[#D6E1E5] text-[#2E2E38] font-medium py-3 px-4 rounded-xl transition-all duration-300">
            Fermer
          </button>
        </template>
      </div>
    </transition>
    
    <!-- Overlay pour le popup -->
    <div v-if="showChat" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40" @click="showChat = false"></div>
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