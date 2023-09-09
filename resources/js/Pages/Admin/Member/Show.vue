<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '../Components/Pagination.vue';
import { statusClass } from '@/helper';
import Badge from '../Components/Badge.vue';

const props = defineProps({
    member: Object,
    checkouts: Object,
    reservations: Object
});

</script>

<template>
    <AuthenticatedLayout>

        <Head>
            <title>{{ member.user.name }}</title>
            <meta name="description" content="Your page description">
        </Head>
        <template #header>
            <div class="flex justify-between">
                <h1 class="text-lg font-bold">{{ member.user.name }}</h1>
            </div>
        </template>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white my-6 p-4 rounded shadow">
                <ul>
                    <li>
                        ID: {{ member.id }}
                    </li>
                    <li>
                        Name: {{ member.user.name }}
                    </li>
                    <li>
                        Email: {{ member.user.email }}
                    </li>
                    <li>
                        Membership Status: <Badge
                            :class="{ 'text-red-800 bg-red-100': !member.membership_status, 'text-emerald-800 bg-emerald-100': member.membership_status }">
                            {{ member.membership_status ? 'active' : 'expired' }}
                        </Badge>
                    </li>
                    <li>Membership Due Date: {{ member.membership_due_date }}</li>
                </ul>
            </div>
        </div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" v-if="reservations.data">
            <h2 class="text-lg font-semibold p-2 my-2 bg-white rounded shadow">Reservations</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="reservation in reservations.data" :key="reservation.id">
                        <td>{{ reservation.id }}</td>
                        <td>{{ reservation.book.title }}</td>
                        <td>
                            <Badge :class="statusClass(reservation.status)">
                                {{ reservation.status }}
                            </Badge>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="checkouts.links" />
        </div>
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8" v-if="checkouts.data">
            <h2 class="text-lg font-semibold p-2 my-2 bg-white rounded shadow">Checkout History</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Check in Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="checkout in checkouts.data" :key="checkout.id">
                        <td>{{ checkout.id }}</td>
                        <td>{{ checkout.book.title }}</td>
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
                </tbody>
            </table>
            <Pagination :links="checkouts.links" />
        </div>

    </AuthenticatedLayout>
</template>