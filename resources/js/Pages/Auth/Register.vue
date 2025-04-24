<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { gsap } from 'gsap';

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone_number: '',
    password: '',
    password_confirmation: '',
    terms: false
});

const isStepOne = ref(true);
const isPasswordVisible = ref(false);
const isConfirmPasswordVisible = ref(false);
const isLoading = ref(false);
const firstNameInput = ref(null);
const emailInput = ref(null);

const passwordStrength = ref(0);
const passwordMessage = ref('');

const inputClass = computed(() => {
    return `w-full rounded-xl bg-white/80 backdrop-blur-sm border-0 px-5 py-4 text-gray-800 shadow-sm ring-1 ring-inset
           ring-gray-200 focus:ring-2 focus:ring-inset focus:ring-pink-500 transition duration-300`;
});

const passwordStrengthColor = computed(() => {
    if (passwordStrength.value < 2) return 'bg-red-500';
    if (passwordStrength.value < 3) return 'bg-yellow-500';
    return 'bg-green-500';
});

const togglePasswordVisibility = (field) => {
    if (field === 'password') {
        isPasswordVisible.value = !isPasswordVisible.value;
    } else {
        isConfirmPasswordVisible.value = !isConfirmPasswordVisible.value;
    }
};

const checkPasswordStrength = () => {
    const password = form.password;

    if (!password) {
        passwordStrength.value = 0;
        passwordMessage.value = '';
        return;
    }

    let strength = 0;

    // Length check
    if (password.length >= 8) strength += 1;

    // Contains lowercase and uppercase
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;

    // Contains numbers
    if (/[0-9]/.test(password)) strength += 1;

    // Contains special characters
    if (/[^A-Za-z0-9]/.test(password)) strength += 1;

    passwordStrength.value = strength;

    if (strength < 2) {
        passwordMessage.value = 'Faible';
    } else if (strength < 3) {
        passwordMessage.value = 'Moyen';
    } else {
        passwordMessage.value = 'Fort';
    }
};

const nextStep = () => {
    if (!form.first_name || !form.last_name || !form.email) {
        return;
    }

    gsap.to('.register-step-1', {
        x: '-100%',
        opacity: 0,
        duration: 0.5,
        onComplete: () => {
            isStepOne.value = false;
            setTimeout(() => {
                gsap.from('.register-step-2', {
                    x: '100%',
                    opacity: 0,
                    duration: 0.5,
                    stagger: 0.1
                });

                // Focus email input on next step
                setTimeout(() => {
                    if (emailInput.value) emailInput.value.focus();
                }, 100);
            }, 100);
        }
    });
};

const prevStep = () => {
    gsap.to('.register-step-2', {
        x: '100%',
        opacity: 0,
        duration: 0.5,
        onComplete: () => {
            isStepOne.value = true;
            setTimeout(() => {
                gsap.from('.register-step-1', {
                    x: '-100%',
                    opacity: 0,
                    duration: 0.5,
                    stagger: 0.1
                });

                // Focus first name input on previous step
                setTimeout(() => {
                    if (firstNameInput.value) firstNameInput.value.focus();
                }, 100);
            }, 100);
        }
    });
};

const submit = () => {
    if (!form.terms) {
        return;
    }

    isLoading.value = true;
    form.post(route('register'), {
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

onMounted(() => {
    // Animate elements on page load
    gsap.from('.register-step-1', {
        y: 20,
        opacity: 0,
        duration: 0.6,
        stagger: 0.1,
        ease: 'power2.out'
    });

    // Focus first name input
    firstNameInput.value.focus();
});
</script>

<template>
    <GuestLayout>
        <Head title="Inscription" />

        <div class="relative overflow-hidden">
            <!-- Animated background elements -->
            <div class="absolute -left-32 -bottom-20 w-64 h-64 rounded-full bg-pink-100/50 blur-3xl animate-blob"></div>
            <div class="absolute -right-32 top-20 w-64 h-64 rounded-full bg-blue-100/50 blur-3xl animate-blob" style="animation-delay: 2s"></div>
            <div class="absolute left-1/2 -translate-x-1/2 bottom-32 w-32 h-32 rounded-full bg-pink-100/30 blur-2xl animate-pulse"></div>

            <!-- Card container with glassmorphism effect -->
            <div class="relative z-10 max-w-md mx-auto bg-white/90 backdrop-blur-sm rounded-2xl shadow-xl border border-white/20 p-8 sm:p-10 overflow-hidden">
                <div class="mb-8 text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-blue-800 rounded-xl flex items-center justify-center shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Créer un compte</h2>
                    <p class="text-gray-600 mt-2">Rejoignez MatchRoom et commencez à négocier</p>
                </div>

                <!-- Progress indicator -->
                <div class="mb-8 relative">
                    <div class="h-1 w-full bg-gray-200 rounded-full">
                        <div class="h-1 bg-gradient-to-r from-pink-500 to-blue-800 rounded-full transition-all duration-500"
                             :style="{ width: isStepOne ? '50%' : '100%' }"></div>
                    </div>
                    <div class="flex justify-between mt-2">
                        <span class="text-xs font-medium" :class="{ 'text-pink-500': true, 'text-gray-500': false }">
                            Information personnelle
                        </span>
                        <span class="text-xs font-medium" :class="{ 'text-pink-500': !isStepOne, 'text-gray-500': isStepOne }">
                            Sécurité du compte
                        </span>
                    </div>
                </div>

                <form @submit.prevent="isStepOne ? nextStep() : submit()">
                    <!-- Step 1: Personal Information -->
                    <div v-if="isStepOne" class="space-y-6">
                        <div class="register-step-1">
                            <InputLabel for="first_name" value="Prénom" class="text-gray-700 mb-2 block font-medium" />
                            <TextInput
                                id="first_name"
                                ref="firstNameInput"
                                type="text"
                                :class="inputClass"
                                v-model="form.first_name"
                                required
                                autocomplete="given-name"
                                placeholder="Votre prénom"
                            />
                            <InputError class="mt-2" :message="form.errors.first_name" />
                        </div>

                        <div class="register-step-1">
                            <InputLabel for="last_name" value="Nom" class="text-gray-700 mb-2 block font-medium" />
                            <TextInput
                                id="last_name"
                                type="text"
                                :class="inputClass"
                                v-model="form.last_name"
                                required
                                autocomplete="family-name"
                                placeholder="Votre nom"
                            />
                            <InputError class="mt-2" :message="form.errors.last_name" />
                        </div>

                        <div class="register-step-1">
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

                        <div class="register-step-1">
                            <InputLabel for="phone_number" value="Téléphone (optionnel)" class="text-gray-700 mb-2 block font-medium" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                    </svg>
                                </div>
                                <TextInput
                                    id="phone_number"
                                    type="tel"
                                    :class="[inputClass, 'pl-10']"
                                    v-model="form.phone_number"
                                    autocomplete="tel"
                                    placeholder="+33 6 12 34 56 78"
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.phone_number" />
                        </div>

                        <div class="register-step-1">
                            <button
                                type="button"
                                @click="nextStep"
                                class="w-full flex justify-center items-center py-4 px-6 rounded-xl text-base font-medium text-white bg-gradient-to-r from-pink-500 to-blue-800 hover:from-pink-600 hover:to-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 shadow-lg transform transition hover:-translate-y-0.5 hover:shadow-xl"
                                :disabled="!form.first_name || !form.last_name || !form.email"
                            >
                                Suivant
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Security Information -->
                    <div v-else class="space-y-6">
                        <div class="register-step-2">
                            <InputLabel for="password" value="Mot de passe" class="text-gray-700 mb-2 block font-medium" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <TextInput
                                    id="password"
                                    ref="emailInput"
                                    :type="isPasswordVisible ? 'text' : 'password'"
                                    :class="[inputClass, 'pl-10 pr-10']"
                                    v-model="form.password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="••••••••"
                                    @input="checkPasswordStrength"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordVisibility('password')"
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

                            <!-- Password strength indicator -->
                            <div v-if="form.password" class="mt-2">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mr-2">
                                        <div class="h-1.5 rounded-full transition-all duration-300"
                                             :class="passwordStrengthColor"
                                             :style="{ width: `${passwordStrength * 25}%` }"></div>
                                    </div>
                                    <span class="text-xs whitespace-nowrap" :class="{
                                        'text-red-500': passwordStrength < 2,
                                        'text-yellow-500': passwordStrength === 2,
                                        'text-green-500': passwordStrength > 2
                                    }">{{ passwordMessage }}</span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Utilisez au moins 8 caractères avec des lettres majuscules, minuscules, des chiffres et des symboles.
                                </p>
                            </div>

                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="register-step-2">
                            <InputLabel for="password_confirmation" value="Confirmer le mot de passe" class="text-gray-700 mb-2 block font-medium" />
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <TextInput
                                    id="password_confirmation"
                                    :type="isConfirmPasswordVisible ? 'text' : 'password'"
                                    :class="[inputClass, 'pl-10 pr-10']"
                                    v-model="form.password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="••••••••"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordVisibility('confirmation')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                >
                                    <svg v-if="isConfirmPasswordVisible" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                    </svg>
                                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 hover:text-gray-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd" />
                                        <path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z" />
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password_confirmation" />
                        </div>

                        <div class="register-step-2">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="terms"
                                    v-model="form.terms"
                                    class="rounded border-gray-300 text-pink-500 shadow-sm focus:ring-pink-500"
                                    required
                                />
                                <span class="ms-2 text-sm text-gray-600">
                                    J'accepte les <a href="#" class="text-pink-500 hover:text-pink-700">Conditions d'utilisation</a> et la <a href="#" class="text-pink-500 hover:text-pink-700">Politique de confidentialité</a>
                                </span>
                            </label>
                        </div>

                        <div class="register-step-2 flex space-x-4">
                            <button
                                type="button"
                                @click="prevStep"
                                class="w-1/4 flex justify-center items-center py-4 px-6 rounded-xl text-base font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <button
                                type="submit"
                                class="w-3/4 flex justify-center items-center py-4 px-6 rounded-xl text-base font-medium text-white bg-gradient-to-r from-pink-500 to-blue-800 hover:from-pink-600 hover:to-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 shadow-lg transform transition hover:-translate-y-0.5 hover:shadow-xl disabled:opacity-50"
                                :disabled="!form.password || !form.password_confirmation || !form.terms || form.processing || isLoading"
                            >
                                <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span v-if="isLoading">Création en cours...</span>
                                <span v-else>Créer mon compte</span>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Déjà un compte?
                        <Link
                            :href="route('login')"
                            class="font-medium text-pink-500 hover:text-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 rounded-md"
                        >
                            Se connecter
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

.animate-pulse {
    animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.5;
    }
    50% {
        opacity: 0.1;
    }
}
</style>