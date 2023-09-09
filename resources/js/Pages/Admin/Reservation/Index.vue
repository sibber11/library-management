<script setup>
import qs from 'qs';
import { ref, onMounted } from 'vue';
import debounce from 'lodash.debounce';
import Pagination from '../Components/Pagination.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Badge from '../Components/Badge.vue';
import { statusClass } from '@/helper';

const props = defineProps({
    reservations: {
        type: Object,
        required: true
    }
});

function checkin(reservation) {
    const form = useForm({
        'is_checked_in': true
    });
    form.patch(route('reservations.update', reservation.id));
}


const status = ref('');
const searchValue = ref('');

onMounted(() => {
    const query = qs.parse(window.location.search, { ignoreQueryPrefix: true });
    status.value = query.filter;
    searchValue.value = query.search;
});

const search = debounce(() => {
    if (status.value === '' && searchValue.value === '') {
        router.get(route('reservations.index'), {}, {
            preserveState: true,
            replace: true
        });
        return;
    }
    router.get(route('reservations.index'), {
        ...(searchValue.value && { 'search': searchValue.value }),
        ...(status.value && { 'filter': status.value }),
    }, { preserveState: true, replace: true });
}, 500);

function reset() {
    status.value = '';
    searchValue.value = '';
    router.get(route('reservations.index'), {}, {
        preserveState: true,
        replace: true
    });
}
</script>
<template>
    <AuthenticatedLayout>

        <Head>
            <title>Reservations</title>
            <meta name="description" content="Your page description">
        </Head>
        <template #header>
            <div class="flex justify-between">
                <h1 class="text-lg font-bold">Reservations</h1>
                <Link :href="route('reservations.create')" as="button" class="bg-gray-800 hover:bg-gray-700">New Reservation
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
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="reservation in reservations.data" :key="reservation.id">
                        <td>{{ reservation.id }}</td>
                        <td>{{ reservation.book.title }}</td>
                        <td>{{ reservation.member.user.name }}</td>
                        <td>
                            <Badge :class="statusClass(reservation.status)">
                                {{ reservation.status }}
                            </Badge>
                        </td>
                        <td class="">
                            <div class="flex flex-row gap-2">
                                <Link :href="route('reservations.destroy', reservation)" method="delete" as="button"
                                    v-if="reservation.status === 'pending'" class="bg-red-600 hover:bg-red-500">
                                Delete
                                </Link>
                                <Link :href="route('reservations.update', reservation)" method="patch" as="button"
                                    v-if="reservation.status !== 'completed' && reservation.status !== 'canceled'"
                                    :data="{ status: 'canceled' }" class="bg-emerald-600 hover:bg-emerald-500">
                                Cancel
                                </Link>
                                <Link :href="route('reservations.update', reservation)" method="patch" as="button"
                                    :data="{ status: 'completed' }" v-if="reservation.status === 'reserved'"
                                    class="bg-purple-600 hover:bg-purple-500">
                                Fullfill
                                </Link>
                            </div>

                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="reservations.links" :only="['reservations']" />
        </section>
    </AuthenticatedLayout>
</template>