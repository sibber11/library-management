<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { computed } from 'vue';

const props = defineProps({
    book: {
        type: Object,
        required: false
    }
});

const form = useForm({
    title: '',
    author: '',
    description: '',
    published_at: '',
    price: '',
});

onMounted(() => {
    if (props.book) {
        form.title = props.book.title;
        form.author = props.book.author;
        form.description = props.book.description;
        form.published_at = props.book.published_at;
        form.price = props.book.price;
    }
});

const pageTitle = computed(() => {
    return props.book ? 'Edit' : 'Create';
});

function save() {
    if (props.book) {
        form.patch(route('books.update', props.book.id), {
            onSuccess: () => {
                console.log('success');
            },
            onError: () => {
                console.log('error');
            }
        });
    } else {
        form.post(route('books.store'), {
            onSuccess: () => {
                console.log('success');
            },
            onError: () => {
                console.log('error');
            }
        });
    }
}

</script>

<template>
    <AuthenticatedLayout>

        <Head>
            <title>{{ pageTitle }}</title>
            <meta name="description" content="Your page description">
        </Head>
        <template #header>
            <h1>Book {{ pageTitle }}</h1>
        </template>

        <section class="m-8 sm:m-6">
            <div class="max-w-7xl mx-auto mt-6 bg-white rounded-md shadow-md p-6">
            <form class="w-full" @submit.prevent="save">
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-first-name">
                            Book Title*
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                            id="grid-first-name" type="text" placeholder="Jane" v-model="form.title">
                    </div>
                    <div class="w-full md:w-1/2 px-3">
                        <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                            for="grid-last-name">
                            Author Name
                        </label>
                        <input
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                            id="grid-last-name" type="text" placeholder="Doe" v-model="form.author">
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full px-3 mb-6 md:mb-0">
                        <label for="description"
                            class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Description</label>
                        <textarea name="" cols="30" rows="10" v-model="form.description" id="description"
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"></textarea>
                    </div>
                </div>
                <div class="flex flex-wrap -mx-3 mb-6">
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label for="published_date"
                            class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Publish Date</label>
                        <input type="date" name="date" id="published_date" v-model="form.published_at"
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    </div>
                    <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                        <label for="price"
                            class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Price</label>
                        <input type="number" name="price" id="price" v-model="form.price"
                            class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                    </div>
                </div>
                <div class="flex flex-wrap mb-6 gap-4">
                    <PrimaryButton type="submit">Save</PrimaryButton>
                    <Link :href="route('books.index')" as="button" class="bg-blue-500">Cancel</Link>
                </div>
            </form>
        </div>
        </section>
</AuthenticatedLayout></template>