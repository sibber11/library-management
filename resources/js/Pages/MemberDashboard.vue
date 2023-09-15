<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Badge from './Admin/Components/Badge.vue';
import { statusClass } from '@/helper';
import Pagination from './Admin/Components/Pagination.vue';

const props = defineProps({
    checkouts: Object,
    reservations: Object,
    user: Object,
});

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 flex flex-col gap-4">
                    <div class="text-gray-900">
                        <span class="font-bold">Name:</span> {{ user.name }}
                    </div>
                    <div class="text-gray-900">
                        <span class="font-bold">Email:</span> {{ user.email }}
                    </div>
                    <div class="text-gray-900">
                        <span class="font-bold">Membership Due Date:</span>
                        {{ user.member.membership_due_date ?? 'N/A' }}
                    </div>
                    <div class="text-gray-900">
                        <span class="font-bold">Membership Status:</span>
                        <Badge
                            :class="{ 'text-red-800 bg-red-100': !user.member.membership_status, 'text-emerald-800 bg-emerald-100': user.member.membership_status }">
                            {{ user.member.membership_status ? 'active' : 'expired' }}
                        </Badge>
                    </div>
                </div>
            </div>
        </div>

        <section class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight py-4">Checkouts</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <!-- <th>Member</th> -->
                        <th>Status</th>
                        <th>Due Date</th>
                        <!-- <th>Action</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="checkout in checkouts.data" :key="checkout.id">
                        <td>{{ checkout.id }}</td>
                        <td>{{ checkout.book.title }}</td>
                        <!-- <td>{{ checkout.member.user.name }}</td> -->
                        <td>
                            <span class="px-2 py-0.5 inline-flex text-xs font-semibold rounded-full uppercase"
                                :class="{ 'bg-green-100 text-green-800': checkout.is_checked_in, 'bg-red-100 text-red-800': !checkout.is_checked_in }">{{
                                    checkout.is_checked_in ? 'checked in' : 'checked out' }}</span>
                        </td>
                        <td>
                            {{ checkout.due_date }}
                        </td>
                    </tr>
                    <tr v-if="!checkouts.data.length">
                        <td colspan="6">
                            No checkouts found.
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="checkouts.links" :only="['checkouts']" />
        </section>

        <section class="mx-auto max-w-7xl sm:px-6 lg:px-8 mt-8">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight py-4">Reservations</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book</th>
                        <!-- <th>Member</th> -->
                        <th>Status</th>
                        <!-- <th>Action</th> -->
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="reservation in reservations.data" :key="reservation.id">
                        <td>{{ reservation.id }}</td>
                        <td>{{ reservation.book.title }}</td>
                        <!-- <td>{{ reservation.member.user.name }}</td> -->
                        <td>
                            <Badge :class="statusClass(reservation.status)">
                                {{ reservation.status }}
                            </Badge>
                        </td>
                    </tr>
                    <tr v-if="!reservations.data.length">
                        <td colspan="6">
                            No reservations found.
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="reservations.links" :only="['reservations']" />
        </section>

    </AuthenticatedLayout>
</template>
