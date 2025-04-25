<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { gsap } from 'gsap';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const isPasswordVisible = ref(false);
const isLoading = ref(false);
const emailInput = ref(null);

const inputClass = computed(() => {
    return `w-full rounded-xl bg-white/80 backdrop-blur-sm border-0 px-5 py-4 text-gray-800 shadow-sm ring-1 ring-inset 
           ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-pink-500 transition duration-300`;
});

const togglePasswordVisibility = () => {
    isPasswordVisible.value = !isPasswordVisible.value;
};

const submit = () => {
    isLoading.value = true;
    form.post(route('login'), {
        onFinish: () => {
            isLoading.value = false;
            form.reset('password');
        },
    });
};

onMounted(() => {
    // Animate elements on page load
    gsap.from('.login-form-element', {
        y: 20,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        ease: 'power2.out'
    });
    
    // Focus email input
    emailInput.value.focus();
});
</script>

<template>
    <GuestLayout>
        <Head title="Connexion" />

        <div class="relative overflow-hidden">
            <!-- Animated background elements -->
            <div class="absolute -left-32 top-20 w-64 h-64 rounded-full bg-pink-100/50 blur-3xl animate-blob"></div>
            <div class="absolute -right-32 bottom-20 w-64 h-64 rounded-full bg-blue-100/50 blur-3xl animate-blob" style="animation-delay: 2s"></div>
            
            <!-- Card container with glassmorphism effect -->
            <div class="relative z-10 max-w-md mx-auto bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-8 sm:p-10">
                <div class="mb-8 text-center login-form-element">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-blue-800 rounded-xl flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Ravi de vous revoir</h2>
                    <p class="text-gray-600 mt-2">Connectez-vous pour accéder à votre compte</p>
                </div>

                <div v-if="status" class="login-form-element mb-6 p-4 bg-green-50 rounded-xl text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div class="space-y-6">
                        <div class="login-form-element">
                            <InputLabel for="email" value="Email" class="text-gray-700 mb-2 block font-medium" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                    </svg>
                                </div>
                                <TextInput
                                    id="email"
                                    ref="emailInput"
                                    type="email"
                                    :class="[inputClass, 'pl-10']"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                    placeholder="votre@email.com"
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="login-form-element">
                            <div class="flex items-center justify-between mb-2">
                                <InputLabel for="password" value="Mot de passe" class="text-gray-700 font-medium" />
                                <Link
                                    v-if="canResetPassword"
                                    :href="route('password.request')"
                                    class="text-sm text-pink-500 hover:text-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 rounded-md"
                                >
                                    Mot de passe oublié?
                                </Link>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <TextInput
                                    id="password"
                                    :type="isPasswordVisible ? 'text' : 'password'"
                                    :class="[inputClass, 'pl-10 pr-10']"
                                    v-model="form.password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                                <button 
                                    type="button" 
                                    @click="togglePasswordVisibility" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                >
                                    <svg v-if="isPasswordVisible" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="login-form-element flex items-center">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    v-model="form.remember"
                                    class="rounded border-gray-300 text-pink-500 shadow-sm focus:ring-pink-500"
                                />
                                <span class="ms-2 text-sm text-gray-600">Se souvenir de moi</span>
                            </label>
                        </div>

                        <div class="login-form-element">
                            <button
                                type="submit"
                                class="w-full flex justify-center items-center py-4 px-6 rounded-xl text-base font-medium text-white bg-gradient-to-r from-pink-500 to-blue-800 hover:from-pink-600 hover:to-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 shadow-lg transform transition hover:-translate-y-0.5 hover:shadow-xl disabled:opacity-50"
                                :disabled="form.processing || isLoading"
                            >
                                <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span v-if="isLoading">Connexion en cours...</span>
                                <span v-else>Se connecter</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 text-center login-form-element">
                    <p class="text-sm text-gray-600">
                        Pas encore de compte?
                        <Link
                            :href="route('register')"
                            class="font-medium text-pink-500 hover:text-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 rounded-md"
                        >
                            Créer un compte
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

<style>
@keyframes blob {
    0% {
        transform: scale(1) translate(0, 0);
    }
    33% {
        transform: scale(1.1) translate(10px, 10px);
    }
    66% {
        transform: scale(0.9) translate(-10px, 10px);
    }
    100% {
        transform: scale(1) translate(0, 0);
    }
}

.animate-blob {
    animation: blob 10s infinite alternate;
}
</style>