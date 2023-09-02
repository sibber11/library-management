<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import Pagination from '../Components/Pagination.vue';
import { ref, onMounted } from 'vue';
import debounce from 'lodash.debounce';
import qs from 'qs';

const props = defineProps({
    checkOuts: {
        type: Object,
        required: true
    }
});

function checkin(checkout) {
    const form = useForm({
        'is_checked_in': true
    });
    form.patch(route('check-outs.update', checkout.id));
}


const status = ref('');
const searchValue = ref('');

function parseQuery() {
    const query = qs.parse(window.location.search, { ignoreQueryPrefix: true });
    status.value = query.filter;
    searchValue.value = query.search;
}

onMounted(() => {
    parseQuery();
});

const search = debounce(function (event) {
    if (status.value === '' && searchValue.value === '') {
        router.get(route('check-outs.index'), {}, {
            preserveState: true,
            replace: true
        });
        return;
    }
    const form = useForm({
        ...(searchValue.value && { 'search': searchValue.value }),
        ...(status.value && { 'filter': status.value }),
    });
    form.get(route('check-outs.index'), {
        preserveState: true,
        replace: true
    });
}, 500);

function reset() {
    status.value = '';
    searchValue.value = '';
    router.get(route('check-outs.index'), {}, {
        preserveState: true,
        replace: true
    });
}
</script>
<template>
    <AuthenticatedLayout>

        <Head>
            <title>Checkouts</title>
            <meta name="description" content="Your page description">
        </Head>
        <template #header>
            <div class="flex justify-between">
                <h1 class="text-lg font-bold">Checkouts</h1>
                <Link :href="route('check-outs.create')" as="button" class="bg-gray-800 hover:bg-gray-700">New Checkout
                </Link>
            </div>
        </template>

        <section class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="my-4 flex justify-between">
                <div class="flex gap-4 flex-nowrap">
                    <input type="text" placeholder="Search by book member..." class="rounded border-transparent bg-gray-200"
                        v-model="searchValue" @input="search" autofocus>
                    <button class="bg-gray-200 text-gray-800 hover:bg-gray-300" type="button" @click="reset">reset</button>
                </div>
                <div>
                    <label>Filter: </label>
                    <select class="uppercase rounded border-transparent bg-gray-200" v-model="status" @input="search">
                        <option value="">select..</option>
                        <option value="checked_in" class="uppercase">checked in</option>
                        <option value="checked_out" class="uppercase">checked out</option>
                    </select>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <th>Member</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="checkout in checkOuts.data" :key="checkout.id">
                        <td>{{ checkout.id }}</td>
                        <td>{{ checkout.book.title }}</td>
                        <td>{{ checkout.member.user.name }}</td>
                        <td>
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full uppercase"
                                :class="{ 'bg-green-100 text-green-800': checkout.is_checked_in, 'bg-red-100 text-red-800': !checkout.is_checked_in }">{{
                                    checkout.is_checked_in ? 'checked in' : 'checked out' }}</span>
                        </td>
                        <td>
                            {{ checkout.due_date }}
                        </td>
                        <td class="">
                            <div class="flex flex-row gap-4">
                                <Link :href="route('check-outs.destroy', checkout)" method="delete" as="button"
                                    class="bg-red-600 hover:bg-red-500">
                                Delete
                                </Link>
                                <button type="button" @click="checkin(checkout)" v-if="!checkout.is_checked_in"
                                    class=" bg-blue-600 hover:bg-blue-500">
                                    Checkin
                                </button>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="checkOuts.links" />
        </section>
    </AuthenticatedLayout>
</template>