<script setup>
defineProps({
    title: String,
});
</script>
<template>
    <div class="py-4">
        <div class="py-6 text-gray-900 font-extrabold uppercase">
            {{ title }}
        </div>
        <div class="grid lg:grid-cols-4 md:grid-cols-2 gap-4">
            <slot />
        </div>
    </div>
</template>