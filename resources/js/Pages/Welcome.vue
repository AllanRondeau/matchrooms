<template>
    <Head>
        <title>Home</title>
    </Head>

    <div class="p-6">
        <h1 class="text-2xl font-bold">Hello Inertia + Vue3 🎉</h1>
        <p class="mt-4">{{ message }}</p>
<!--        <Link href="/about" class="text-blue-600 underline">Go to About</Link>-->
    </div>
</template>

<script setup>
import {Head, Link} from '@inertiajs/inertia-vue3'

const props = defineProps({
    message: {
        type: String,
        required: true,
    },
})
</script>

