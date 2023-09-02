<script setup>
import { computed, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import VueSelect from 'vue-select'
import Member from './Member.vue';
import Book from './Book.vue';

const props = defineProps({
    checkout: {
        type: Object,
        required: false
    },
    books: Object,
    members: Object,
});

const form = useForm({
    book_id: '',
    member_id: '',
    due_date: ''
});

onMounted(() => {
    if (props.checkout) {
        form.book_id = props.checkout.book_id;
        form.member_id = props.checkout.member_id;
        form.due_date = props.checkout.due_date;
    }else{
        form.due_date = new Date().toISOString().substring(0, 10);
    }
});

const pageTitle = computed(() => {
    return props.checkout ? 'Edit' : 'New';
});

function save() {
    if (props.checkout) {
        form.patch(route('check-outs.update', props.checkout.id), {
            onSuccess: () => {
                console.log('success');
            },
            onError: () => {
                console.log('error');
            }
        });
    } else {
        form.post(route('check-outs.store'), {
            onSuccess: () => {
                console.log('success');
            },
            onError: () => {
                console.log('error');
            }
        });
    }
}

const book = computed(() => {
    return props.books.find(book => book.id === form.book_id);
});

const member = computed(() => {
    return props.members.find(member => member.id === form.member_id);
});

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
                                Member
                            </label>
                            <VueSelect :options="members" v-model="form.member_id" label="name"
                                :reduce="member => member.id"></VueSelect>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-last-name">
                                Book
                            </label>
                            <VueSelect :options="books" v-model="form.book_id" label="title" :reduce="member => member.id">
                            </VueSelect>
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <Member :member="member" />
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">

                            <Book :book="book" />

                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label for="published_date"
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Due Date</label>
                            <input type="date" id="published_date" v-model="form.due_date" disabled
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-6">
                        <PrimaryButton type="submit">Save</PrimaryButton>
                    </div>
                </form>
            </div>
        </section>
    </AuthenticatedLayout>
</template>