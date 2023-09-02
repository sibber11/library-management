<script setup>

const props = defineProps({
    book: {
        type: Object,
        required: true
    }
})
</script>

<template>
    <article class="p-6">
        <h2 class="text-lg font-bold">{{ book.title }}</h2>
        <p>{{ book.author }}</p>
        <p>{{ book.published_at }}</p>
        <p>{{ book.quantity }}</p>
    </article>
</template>