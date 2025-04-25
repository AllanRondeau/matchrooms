<script setup>
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { gsap } from 'gsap';

onMounted(() => {
    // Animate logo and layout elements on mount
    gsap.from('.logo-animation', {
        scale: 0.8,
        opacity: 0,
        duration: 0.8,
        ease: 'back.out(1.7)'
    });
    
    gsap.from('.layout-content', {
        opacity: 0,
        y: 20,
        duration: 0.8,
        delay: 0.2,
        ease: 'power3.out'
    });
});
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-white to-gray-100 py-12 sm:py-6">
        <!-- Background patterns -->
        <div class="absolute inset-0 overflow-hidden -z-10">
            <div class="absolute top-0 left-0 w-full h-64 bg-gradient-to-b from-pink-50 to-transparent"></div>
            <div class="absolute bottom-0 left-0 w-full h-64 bg-gradient-to-t from-blue-50 to-transparent"></div>
            
            <!-- Top-right pattern -->
            <div class="absolute top-0 right-0 -mt-24 -mr-24 w-96 h-96 opacity-20">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="text-pink-500">
                    <path fill="currentColor" d="M47.5,-73.3C59.2,-64.8,65.2,-47.6,70.5,-30.9C75.8,-14.2,80.5,2,79.1,17.8C77.8,33.7,70.3,49.2,58.3,59.5C46.2,69.8,29.6,74.8,13.3,76.8C-3.1,78.8,-19.1,77.8,-34.3,71.9C-49.5,66,-63.8,55.2,-71.1,41C-78.3,26.7,-78.3,8.9,-76.1,-8.2C-73.9,-25.3,-69.5,-41.6,-59.6,-52.6C-49.7,-63.5,-34.4,-69.2,-19.1,-74.1C-3.8,-79.1,11.4,-83.5,27.7,-81.9C44,-80.3,61.3,-72.8,47.5,-73.3Z" transform="translate(100 100)" />
                </svg>
            </div>
            
            <!-- Bottom-left pattern -->
            <div class="absolute bottom-0 left-0 -mb-24 -ml-24 w-96 h-96 opacity-20">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" class="text-blue-800">
                    <path fill="currentColor" d="M31.9,-53.5C39.1,-45.9,41.4,-32.9,47.8,-21.1C54.1,-9.3,64.4,1.3,66.6,14.1C68.7,26.9,62.6,42,51.8,50.9C41,59.8,25.4,62.6,10.9,64.4C-3.6,66.1,-16.9,66.7,-28.6,62.1C-40.2,57.4,-50.1,47.4,-56.2,35.5C-62.3,23.6,-64.7,9.9,-64,-3.5C-63.3,-17,-59.6,-30.1,-51.2,-38.1C-42.8,-46.1,-29.7,-49.1,-18.4,-55.2C-7.1,-61.3,2.2,-70.7,10.8,-68.2C19.4,-65.8,27.2,-51.7,31.9,-53.5Z" transform="translate(100 100)" />
                </svg>
            </div>
        </div>
        
        <div class="logo-animation">
            <Link href="/" class="transform transition duration-500 hover:scale-110">
                <img src="/images/logoMR.png" alt="Logo MatchRoom" class="h-12 mb-4" />
            </Link>
        </div>
        
        <div class="layout-content w-full max-w-md px-6 sm:px-0">
            <slot />
            
            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-500">
                    &copy; 2025 MatchRoom. Tous droits réservés.
                </p>
                <div class="mt-2 flex justify-center space-x-4">
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-700 transition">Conditions d'utilisation</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-700 transition">Politique de confidentialité</a>
                    <span class="text-gray-300">|</span>
                    <a href="#" class="text-xs text-gray-500 hover:text-gray-700 transition">Aide</a>
                </div>
            </div>
        </div>
    </div>
</template>