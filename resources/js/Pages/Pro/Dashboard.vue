<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
defineProps({
    hotel: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <Link
                v-if="hotel"
                :href="route('hotel.edit', hotel.id)"
                class="text-blue-600 hover:underline"
            >
                informations de l'hôtel
            </Link>
            <Link
                v-else
                :href="route('hotel.create')"
                class="text-blue-600 hover:underline"
            >
                informations de l'hôtel
            </Link>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        You're logged in!
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
