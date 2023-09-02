<script setup>
import { Link } from '@inertiajs/vue3';
const props = defineProps({
    links: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <div class="flex justify-between">
        <div class="bg-white inline-flex flex-row rounded-lg divide-x shadow divide-gray-300 border-gray-300 my-2">
            <Link v-for="(link, index) in links" v-html="link.label"
                class="border-collapse px-2 py-1 hover:bg-gray-400 hover:text-white font-semibold" :href="link.url ?? ''"
                :key="index"
                :class="{ 'bg-gray-400 text-white': link.active, 'text-gray-400 pointer-events-none': !link.url }">
            </Link>
        </div>
        <!-- <div class="bg-white inline-flex flex-row rounded-lg divide-x shadow divide-gray-300 border-gray-300 my-2">
            {{ links }}
        </div> -->
    </div>
</template>