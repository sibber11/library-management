<script setup>
import qs from 'qs';
import { computed, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import VueSelect from 'vue-select'
import Member from './Member.vue';
import Book from './Book.vue';
import Badge from '../Components/Badge.vue';

const props = defineProps({
    checkout: Object,
    books: Object,
    members: Object,
    due_date: String
});

const form = useForm({
    book: null,
    member: null,
    due_date: props.due_date
});

onMounted(() => {
    // if (props.checkout) {
    //     form.book_id = props.checkout.book_id;
    //     form.member_id = props.checkout.member_id;
    //     form.due_date = props.checkout.due_date;
    //     return;
    // }
    const query = qs.parse(window.location.search, { ignoreQueryPrefix: true });
    if (query.book && query.member) {
        console.log('set');
        form.book = props.books.find(item => item.id == query.book);
        form.member = props.members.find(item => item.id == query.member);
    }
});

const pageTitle = computed(() => {
    return props.checkout ? 'Edit' : 'Create';
});

function save() {
    const available = form.book?.available;
    form.transform(data => ({
        book_id: data.book.id,
        member_id: data.member.id,
        due_date: data.due_date
    }))
    if (props.checkout) {
        form.patch(route('check-outs.update', props.checkout.id));
    } else if (!available) {
        form.post(route('reservations.store'));
    } else {
        form.post(route('check-outs.store'));
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
            <h1 class="text-lg font-bold">Checkout {{ pageTitle }}</h1>
        </template>

        <section class="m-8 sm:m-6">
            <div class="max-w-7xl mx-auto mt-6 bg-white rounded-md shadow-md p-6"
                :class="{ '!bg-yellow-100': form.book == null ? false : form.book?.available ? false : true }">
                <form class="w-full" @submit.prevent="save">
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-first-name">
                                Member
                            </label>
                            <VueSelect :options="members" v-model="form.member" label="name">
                                <template #search="{ attributes, events }">
                                    <input class="vs__search" :required="!form.member" v-bind="attributes" v-on="events" />
                                </template>
                            </VueSelect>
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-last-name">
                                Book
                            </label>
                            <VueSelect :options="books" v-model="form.book" label="title">
                                <template v-slot:option="option">
                                    <div class="flex justify-between"
                                        :class="{ 'text-red-800 font-semibold': !option.available }">
                                        <span>{{ option.title }}</span>
                                        <Badge
                                            :class="{ 'bg-red-100 text-red-800': !option.available, 'bg-green-100 text-green-800': option.available }">
                                            {{ form.member?.checkouts.includes(option.id)?'checked out':
                                            form.member?.reservations.includes(option.id)?'reserved':option.available ? option.available : 'out' }}
                                        </Badge>
                                    </div>
                                </template>
                                <template #search="{ attributes, events }">
                                    <input class="vs__search" :required="!form.book" v-bind="attributes" v-on="events" />
                                </template>
                            </VueSelect>
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <Member :member="form.member" />
                        </div>
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <Book :book="form.book" />
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6" v-if="form.due_date">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label for="published_date"
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Due Date</label>
                            <input type="date" id="published_date" v-model="form.due_date" disabled
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                        </div>
                    </div>
                    <div>
                        <PrimaryButton type="submit" :disabled="form.processing || form.member?.checkouts.includes(form.book?.id)"
                            :class="{ 'bg-yellow-600': form.book == null ? false : form.book?.available ? false : true }">
                            {{ form.book == null ? 'checkout' : form.book?.available ? 'checkout' : 'reserve' }}
                        </PrimaryButton>
                        <div class="text-sm text-red-700 py-2" v-show="form.member?.checkouts.includes(form.book?.id)">
                            This book cannot be reserved by the current selected member.
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </AuthenticatedLayout>
</template>