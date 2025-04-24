<script setup>
import { Head, Link } from '@inertiajs/inertia-vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const rooms = [
    {
        id: 1,
        name: "Chambre Deluxe",
        hotel: "Grand Hôtel Royal",
        location: "Paris, France",
        image: "https://placehold.co/600x400/e2e8f0/1e293b?text=Chambre+Deluxe",
        price: 150,
    },
    {
        id: 2,
        name: "Suite Junior",
        hotel: "Hôtel Beau Rivage",
        location: "Nice, France",
        image: "https://placehold.co/600x400/e2e8f0/1e293b?text=Suite+Junior",
        price: 220,
    },
    {
        id: 3,
        name: "Chambre Standard",
        hotel: "Résidence du Lac",
        location: "Annecy, France",
        image: "https://placehold.co/600x400/e2e8f0/1e293b?text=Chambre+Standard",
        price: 95,
    },
    {
        id: 4,
        name: "Suite Présidentielle",
        hotel: "Palace Montmartre",
        location: "Paris, France",
        image: "https://placehold.co/600x400/e2e8f0/1e293b?text=Suite+Présidentielle",
        price: 350,
    },
];

const currentIndex = ref(0);

const likeRoom = () => {
    console.log('Like room:', rooms[currentIndex.value]);
    nextRoom();
};

const dislikeRoom = () => {
    console.log('Dislike room:', rooms[currentIndex.value]);
    nextRoom();
};

const nextRoom = () => {
    if (currentIndex.value < rooms.length - 1) {
        currentIndex.value++;
    } else {
        currentIndex.value = 0;
    }
};

const prevRoom = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--;
    } else {
        currentIndex.value = rooms.length - 1;
    }
};
</script>

<template>
    <Head title="Accueil" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Découvrir des chambres
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                    <img
                        :src="rooms[currentIndex].image"
                        :alt="rooms[currentIndex].name"
                        class="w-full h-64 object-cover"
                    >

                    <div class="p-5">
                        <h3 class="text-xl font-semibold mb-1">{{ rooms[currentIndex].name }}</h3>
                        <div class="text-gray-700 font-medium mb-1">{{ rooms[currentIndex].hotel }}</div>
                        <div class="flex items-center text-gray-500 mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ rooms[currentIndex].location }}
                        </div>
                        <div class="text-xl font-bold text-indigo-600">
                            {{ rooms[currentIndex].price }}€ <span class="text-sm text-gray-500 font-normal">/ nuit</span>
                        </div>
                    </div>

                    <div class="flex justify-center p-4 space-x-6 border-t border-gray-100">
                        <button
                            @click="prevRoom"
                            class="p-3 bg-gray-100 rounded-full hover:bg-gray-200 transition"
                            aria-label="Chambre précédente"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button
                            @click="dislikeRoom"
                            class="p-4 bg-red-100 rounded-full hover:bg-red-200 transition"
                            aria-label="Ne pas aimer cette chambre"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <button
                            @click="likeRoom"
                            class="p-4 bg-green-100 rounded-full hover:bg-green-200 transition"
                            aria-label="Aimer cette chambre"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>

                        <button
                            @click="nextRoom"
                            class="p-3 bg-gray-100 rounded-full hover:bg-gray-200 transition"
                            aria-label="Chambre suivante"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex justify-center space-x-2">
                    <div
                        v-for="(room, index) in rooms"
                        :key="room.id"
                        :class="[
                            'h-2 rounded-full transition-all',
                            index === currentIndex ? 'w-6 bg-indigo-500' : 'w-2 bg-gray-300'
                        ]"
                    ></div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
