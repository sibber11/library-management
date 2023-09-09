<script setup>

const props = defineProps({
    book: [Object, String]
})

</script>
<template>
    <article class="shadow-md rounded overflow-hidden p-3 bg-rose-400 text-white" v-if="book">
        <div>
            <span class="font-bold tracking-wider">Book ID: </span>
            <span>{{ book.id }}</span>
        </div>
        <div>
            <span class="font-bold tracking-wider">Title: </span>
            <span>{{ book.title }}</span>
        </div>
        <div>
            <span class="font-bold tracking-wider">Publish Year: </span>
            <span>{{ book.publish_year }}</span>
        </div>
    </article>
    <article class="shadow-md rounded overflow-hidden p-3 bg-rose-400 flex flex-col justify-between h-24" v-else>
        <div class="flex justify-start gap-2">
            <div class="bg-rose-200 w-1/3 h-5 rounded inline-block"></div>
            <div class="bg-rose-200 w-10 h-5 rounded inline-block"></div>
        </div>
        <div class="flex justify-start gap-2">
            <div class="bg-rose-200 w-1/4 h-5 rounded inline-block"></div>
            <div class="bg-rose-200 w-2/3 h-5 rounded inline-block"></div>
        </div>
        <div class="flex justify-start gap-2">
            <div class="bg-rose-200 w-1/3 h-5 rounded inline-block"></div>
            <div class="bg-rose-200 w-1/6 h-5 rounded inline-block"></div>
        </div>
    </article>
</template>