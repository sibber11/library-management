<script setup>

const props = defineProps({
    member: Object
})

</script>
<template>
    <article class="shadow-md rounded overflow-hidden p-3 bg-emerald-400 text-white" v-if="member">
        <div>
            <span class="font-bold tracking-wider">Member ID: </span>
            <span>{{ member.id }}</span>
        </div>
        <div>
            <span class="font-bold tracking-wider">Name: </span>
            <span>{{ member.name }}</span>
        </div>
        <div>
            <span class="font-bold tracking-wider">Email: </span>
            <span>{{ member.mail }}</span>
        </div>
    </article>
    <article class="shadow-md rounded overflow-hidden p-4 bg-emerald-400 flex gap-1 flex-col" v-else>
        <div class="flex justify-start gap-2">
            <div class="bg-emerald-200 w-1/3 h-5 rounded inline-block"></div>
            <div class="bg-emerald-200 w-10 h-5 rounded inline-block"></div>
        </div>
        <div class="flex justify-start gap-2">
            <div class="bg-emerald-200 w-1/4 h-5 rounded inline-block"></div>
            <div class="bg-emerald-200 w-2/3 h-5 rounded inline-block"></div>
        </div>
        <div class="flex justify-start gap-2">
            <div class="bg-emerald-200 w-1/3 h-5 rounded inline-block"></div>
            <div class="bg-emerald-200 w-1/6 h-5 rounded inline-block"></div>
        </div>
    </article>
</template>