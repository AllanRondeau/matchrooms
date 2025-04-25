<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedProLayout from "@/Layouts/AuthenticatedProLayout.vue";

const props = defineProps({
    hotel: {
        type: Object,
        default: null
    }
});

const form = useForm({
    name: props.hotel?.name || '',
    description: props.hotel?.description || '',
    address: props.hotel?.address || '',
    city: props.hotel?.city || '',
    country: props.hotel?.country || '',
    postal_code: props.hotel?.postal_code || '',
    star_rating: props.hotel?.star_rating || '',
    amenities: props.hotel ? JSON.parse(props.hotel.amenities) : [],
    photo: null, // Ce champ contiendra le File envoyé depuis l'input
    status: props.hotel?.status || 'active',
});

const onPhotoChange = (e) => {
    form.photo = e.target.files[0];
};

const amenitiesInput = ref('');
watch(
    () => form.amenities,
    (newVal) => {
        amenitiesInput.value = Array.isArray(newVal) ? newVal.join(", ") : '';
    },
    { immediate: true }
);

watch(amenitiesInput, (val) => {
    const arr = val.split(",").map(s => s.trim()).filter(Boolean);
    if (JSON.stringify(arr) !== JSON.stringify(form.amenities)) {
        form.amenities = arr;
    }
});

const isEdit = computed(() => !!props.hotel);
const submit = () => {
    if (isEdit.value) {
        form.post(route('hotel.update', props.hotel.id), {
            method: 'put',
            forceFormData: true,
            onSuccess: () => form.reset('photo')
        });
    } else {
        form.post(route('hotel.store'), {
            forceFormData: true,
            onSuccess: () => form.reset()
        });
    }
};

</script>

<template>
    <Head title="Informations de l'hôtel" />
    <AuthenticatedProLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ isEdit ? 'Modifier l\'hôtel' : 'Créer un hôtel' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <form
                        @submit.prevent="submit"
                        enctype="multipart/form-data"
                        class="space-y-6 p-6"
                    >
                        <div>
                            <label class="block mb-1">Nom</label>
                            <input v-model="form.name" type="text" class="w-full border rounded" />
                            <div v-if="form.errors.name" class="text-sm text-red-600 mt-1">
                                {{ form.errors.name }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1">Description</label>
                            <textarea v-model="form.description" class="w-full border rounded"></textarea>
                            <div v-if="form.errors.description" class="text-sm text-red-600 mt-1">
                                {{ form.errors.description }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1">Adresse</label>
                            <input v-model="form.address" type="text" class="w-full border rounded" />
                            <div v-if="form.errors.address" class="text-sm text-red-600 mt-1">
                                {{ form.errors.address }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1">Ville</label>
                            <input v-model="form.city" type="text" class="w-full border rounded" />
                            <div v-if="form.errors.city" class="text-sm text-red-600 mt-1">
                                {{ form.errors.city }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1">Pays</label>
                            <input v-model="form.country" type="text" class="w-full border rounded" />
                            <div v-if="form.errors.country" class="text-sm text-red-600 mt-1">
                                {{ form.errors.country }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1">Code postal</label>
                            <input v-model="form.postal_code" type="text" class="w-full border rounded" />
                            <div v-if="form.errors.postal_code" class="text-sm text-red-600 mt-1">
                                {{ form.errors.postal_code }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1">Nombre d'étoiles</label>
                            <input v-model="form.star_rating" type="number" min="1" max="5" class="w-full border rounded" />
                            <div v-if="form.errors.star_rating" class="text-sm text-red-600 mt-1">
                                {{ form.errors.star_rating }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1">Équipements (séparés par une virgule)</label>
                            <input v-model="amenitiesInput" type="text" class="w-full border rounded"
                                   placeholder="Piscine,WiFi,Parking" />
                            <div v-if="form.errors.amenities" class="text-sm text-red-600 mt-1">
                                {{ form.errors.amenities }}
                            </div>
                        </div>
                        <div>
                            <label class="block mb-1">Photo de présentation</label>
                            <input type="file" @change="onPhotoChange" accept="image/*" class="w-full border rounded" />
                            <div v-if="form.errors.photo" class="text-sm text-red-600 mt-1">
                                {{ form.errors.photo }}
                            </div>
                            <div v-if="isEdit && props.hotel && props.hotel.images && JSON.parse(props.hotel.images)[0]" class="mt-2">
                                <span class="block text-sm text-gray-400">Photo actuelle:</span>
                                <img
                                    :src="`/storage/${JSON.parse(props.hotel.images)[0]}`"
                                    alt="photo" class="w-32 h-20 object-cover rounded"
                                >
                            </div>
                        </div>
                        <button
                            type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                        >
                            {{ isEdit ? 'Mettre à jour' : 'Créer' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedProLayout>
</template>
