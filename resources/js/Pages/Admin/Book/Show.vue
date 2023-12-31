<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '../Components/Pagination.vue';
import { statusClass } from '@/helper';
import Badge from '../Components/Badge.vue';

const props = defineProps({
    book: {
        type: Object,
        required: true
    },
    checkouts: {
        type: Object,
        required: false
    },
    reservations: {
        type: Object,
        required: false
    }
});

</script>

<template>
    <AuthenticatedLayout>

        <Head>
            <title>{{ book.title }}</title>
            <meta name="description" content="Your page description">
        </Head>
        <template #header>
            <div class="flex justify-between">
                <h1 class="text-lg font-bold">{{ book.title }}</h1>
            </div>
        </template>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md my-4 flex md:flex-nowrap flex-wrap">
                <div class="md:w-1/2 w-full p-6 rounded overflow-auto">
                    <!-- <div class="w-full h-full bg-gray-200">&nbsp;</div> -->
                    <img src="https://picsum.photos/450?grayscale" alt="none">
                </div>
                <div class="m-auto md:w-1/2 w-full p-6">
                    <h2 class="text-lg font-bold">Book Details</h2>
                    <ul class="">
                        <li v-for="(value, index) in book" class="leading-8">
                            <span class="uppercase font-bold">{{ index }}: </span>
                            <span>{{ value }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" v-if="reservations.data">
            <h3 class="py-6 text-gray-900 font-extrabold uppercase">
                Reservations
            </h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="reservation in reservations.data" :key="reservation.id">
                        <td>{{ reservation.id }}</td>
                        <td>{{ reservation.member.user.name }}</td>
                        <td>
                            <Badge :class="statusClass(reservation.status)">
                                {{ reservation.status }}
                            </Badge>
                        </td>
                    </tr>
                    <tr v-if="reservations.data.length == 0">
                        <td colspan="3" class="font-semibold text-center text-lg">No Reservations for this book.</td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="checkouts.links" />
        </div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" v-if="checkouts.data">
            <h3 class="py-6 text-gray-900 font-extrabold uppercase">
                Checkout History
            </h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Member</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Check in Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="checkout in checkouts.data" :key="checkout.id">
                        <td>{{ checkout.id }}</td>
                        <td>{{ checkout.member.user.name }}</td>
                        <td>
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full uppercase"
                                :class="{ 'bg-green-100 text-green-800': checkout.is_checked_in, 'bg-red-100 text-red-800': !checkout.is_checked_in }">{{
                                    checkout.is_checked_in ? 'checked in' : 'checked out' }}</span>
                        </td>
                        <td>
                            {{ checkout.due_date }}
                        </td>
                        <td>
                            {{ checkout.check_in_date }}
                        </td>
                    </tr>
                    <tr v-if="checkouts.data.length == 0">
                        <td colspan="5" class="font-semibold text-center text-lg">No Checkout history for this book.</td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="checkouts.links" />
        </div>
    </AuthenticatedLayout>
</template>