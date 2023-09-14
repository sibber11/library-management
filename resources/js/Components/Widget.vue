<script setup>
import BaseIcon from '@/Components/BaseIcon.vue';
const props = defineProps({
    title: String,
    value: Number,
    icon: String,
});
</script>
<template>
    <div class= "p-2 rounded shadow bg-gradient-to-r from-orange-400 to-pink-500 overflow-clip relative text-white">
        <div class="w-20 h-20 rounded-full bg-white opacity-30 absolute -right-8 -top-2"></div>
        <div class="w-24 h-24 rounded-full bg-white opacity-30 absolute right-0 -bottom-8"></div>
        <div class="flex justify-between">
            <div class="px-2">
                <div class="uppercase text-sm tracking-wider font-semibold">{{ title }}</div>
                <div class="text-xl font-bold">{{ value }}</div>
            </div>
            <BaseIcon :path="icon" class="text-white w-12 h-12" size="40" v-if="icon"/>
        </div>
        <div class="flex px-2 pt-4">
            <div class="text-sm">Last 30 days</div>
            <div class="text-sm">+ 5.2%</div>
        </div>
    </div>
</template>