<script setup>
import { ref, onMounted } from 'vue';
import NavLink from "@/Components/NavLink.vue";

const negotiations = ref([]);
const sidebarVisible = ref(false);

const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value;
};

const loadNegotiations = () => {
  const stored = JSON.parse(localStorage.getItem('negotiations')) || [];
  negotiations.value = stored;
};

const getTimeRemaining = (expiresAt) => {
  const remaining = new Date(expiresAt) - new Date();
  if (remaining <= 0) return 'Expiré';

  const hours = Math.floor(remaining / (1000 * 60 * 60));
  const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

  return `${hours}h ${minutes}m ${seconds}s`;
};

onMounted(() => {
  loadNegotiations();
  setInterval(loadNegotiations, 60000); // Refresh every 60s
});
</script>

<template>
  <div class="min-h-screen bg-[#d6E1E5] flex flex-col">

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
          <NavLink
            :href="route('logout')"
            method="post"
          >
            Se déconnecter
          </NavLink>
        </nav>
      </aside>
    </transition>

    <!-- HEADER -->
    <header class="w-full flex items-center justify-between px-6 py-4 bg-[#d9e2e7]">
      <button @click="toggleSidebar" class="text-3xl">☰</button>

      <div class="flex items-center space-x-2">
        <img src="/images/logoMR.png" alt="Logo MatchRoom"
             class="h-10 mb-4 transform transition-transform duration-300 hover:scale-105" />
      </div>

      <div class="w-8 h-8"></div>
    </header>

    <!-- CONTENU -->
    <main class="p-8 flex-1">
      <h1 class="text-2xl font-bold mb-8 text-center text-[#101F44]"> Négociations en cours</h1>

      <div v-if="negotiations.length === 0" class="text-center text-gray-600">
        Aucune négociation en cours.
      </div>

      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
            v-for="nego in negotiations"
            :key="nego.id"
            class="bg-white rounded-xl shadow-md p-6 flex flex-col justify-between"
        >
          <img :src="nego.image" alt="Hôtel" class="w-full h-40 object-cover rounded-md mb-4" />
          <h2 class="text-lg font-semibold text-[#101F44] mb-2">{{ nego.name }}</h2>
          <p class="text-sm text-gray-600 mb-2">{{ nego.location }}</p>

          <div class="text-sm text-gray-700 mb-2">
            <strong>Prix de base :</strong> {{ nego.basePrice }}€
          </div>
          <div class="text-sm text-gray-700 mb-2">
            <strong>Prix proposé :</strong> {{ nego.proposedPrice }}€
          </div>
          <div class="text-sm text-gray-700">
            <strong>Temps restant :</strong> {{ getTimeRemaining(nego.expiresAt) }}
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
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
