<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3';


const likedRooms = ref([])
const sidebarVisible = ref(false)

onMounted(() => {
  try {
    const saved = localStorage.getItem('likedRooms')
    if (saved) {
      likedRooms.value = JSON.parse(saved)
    }
  } catch (error) {
    console.error('Erreur lecture localStorage :', error)
  }
})

const toggleSidebar = () => {
  sidebarVisible.value = !sidebarVisible.value
}
</script>

<template>
  <div class="min-h-screen bg-[#d9e2e7] flex flex-col relative">

    <!-- SIDEBAR -->
    <transition name="slide">
      <aside
          v-if="sidebarVisible"
          class="fixed top-0 left-0 h-full w-64 bg-[#d9e2e7] shadow-md z-50 flex flex-col p-6 space-y-6 border-r border-gray-300"
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
          <a href="/home" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
            <span>swipe page</span>
          </a>
          <a href="/map" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
            <span>map finder</span>
          </a>
          <Link href="/negociations" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
            <span>Négociations en cours</span>
          </Link>
          <a href="/reservations" class="flex items-center gap-3 hover:bg-[#F5E9E6] px-4 py-3 rounded-xl transition">
            <span>Mes réservations</span>
          </a>
        </nav>
      </aside>
    </transition>

    <!-- HEADER -->
    <header class="bg-[#d9e2e7] w-full px-6 py-6 flex items-center justify-between ">
      <button class="text-3xl" @click="toggleSidebar">☰</button>
      <img src="/images/logoMR.png" alt="Logo MatchRoom" class="h-12" />
      <div class="w-8"></div>
    </header>

    <!-- TITRE -->
    <div class="flex flex-col items-center mt-8">
      <h1 class="text-3xl font-bold bg-gradient-to-r from-[#101F44] via-[#EA3D69] to-[#101F44] text-transparent bg-clip-text text-center">
        Your liked rooms
      </h1>

    </div>

    <!-- CONTENT -->
    <main class="flex-1 p-8">
      <div v-if="likedRooms.length === 0" class="text-center text-gray-500 mt-20 text-lg">
        Aucune chambre likée pour l’instant...
      </div>

      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mt-10">
        <div
            v-for="room in likedRooms"
            :key="room.id"
            class="bg-white rounded-2xl shadow-lg overflow-hidden flex flex-col hover:shadow-xl transition"
        >
          <img :src="room.image" :alt="room.name" class="w-full h-48 object-cover" />
          <div class="p-5 flex flex-col flex-1 justify-between">
            <div>
              <h2 class="text-lg font-bold text-[#101F44]">{{ room.name }}</h2>
              <p class="text-sm text-gray-600 mt-1">{{ room.location }}</p>
            </div>
            <div class="mt-4 text-[#EA3D69] text-lg font-semibold">
              {{ room.price }}€ / nuit
            </div>
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
