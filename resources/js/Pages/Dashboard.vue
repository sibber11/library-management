<script setup>
import Widget from '@/Components/Widget.vue';
import Widgets from '@/Components/Widgets.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { mdiBook, mdiBookOpen } from '@mdi/js';
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js';
import { Line } from 'vue-chartjs';
import Pagination from './Admin/Components/Pagination.vue';
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)

defineProps({
    lineChart: Object,
    inventory: Object,
    members: Object,
    overdueBooks: Object
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard</h2>
        </template>

        <!-- quick links -->


        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-12">
                <div class="p-6 text-gray-900">Hello <span class="font-semibold">{{ $page.props.auth.user.name }}</span>.
                    Welcome to
                    <span>Library Management.</span>
                </div>
            </div>
            <h3 class="py-6 text-gray-900 font-extrabold uppercase">
                Quick Links
            </h3>
            <div class="flex gap-4 flex-wrap">
                <Link class="bg-orange-500" as="button" :href="route('check-outs.create')">New Checkout</Link>
                <Link class="bg-orange-500" as="button" :href="route('books.create')">New Book</Link>
                <Link class="bg-orange-500" as="button" :href="route('members.create')">New Member</Link>

            </div>
            <Widgets title="Inventory Statistics">
                <Widget title="Total Books" :value="inventory.totalBooks" :icon="mdiBook" :href="route('books.index')" />
                <Widget title="Available Books" :value="inventory.availableBooks" :icon="mdiBookOpen"
                    :href="route('books.index')" />
                <Widget title="Total Issued Books" :value="inventory.issuedBooks" :icon="mdiBook"
                    :href="route('check-outs.index')" />
                <Widget title="Total Reservations" :value="inventory.reservations" :icon="mdiBookOpen"
                    :href="route('reservations.index')" />
            </Widgets>
            <Widgets title="Member Statistics">
                <Widget title="Total Members" :value="members.totalMembers" :icon="mdiBook"
                    :href="route('members.index')" />
                <Widget title="Active Members" :value="members.activeMembers" :icon="mdiBookOpen"
                    :href="route('members.index', { filter: 'active' })" />
                <Widget title="Expired Members" :value="members.expiredMembers" :icon="mdiBook"
                    :href="route('members.index', { filter: 'expired' })" />
            </Widgets>
            <div class="py-4">
                <h3 class="py-6 text-gray-900 font-extrabold uppercase">
                    Usage Reports
                </h3>
                <Line :data=" lineChart " :options=" { scales: { y: { min: 0, suggestedMax: 10, ticks: { stepSize: 1 } } } } " />
            </div>

            <div class="overflow-hidden shadow-sm sm:rounded-lg pb-4">
                <h3 class="py-6 text-gray-900 font-extrabold uppercase">
                    Overdue Books.
                </h3>
                <div class="overflow-auto">
                    <table>
                        <thead>
                            <tr>
                                <th>id</th>
                                <th>book</th>
                                <th>member</th>
                                <th>Expired</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for=" checkout  in  overdueBooks.data ">
                                <td>{{ checkout.id }}</td>
                                <td>{{ checkout.book.title }}</td>
                                <td>{{ checkout.member.user.name }}</td>
                                <td>{{ checkout.check_out_date }}</td>
                            </tr>
                            <tr v-if=" overdueBooks.data.length == 0 ">
                                <td colspan="4" class="font-semibold text-center text-lg">No Overdue Books.</td>
                            </tr>
                        </tbody>
                    </table>
                    <Pagination :links=" overdueBooks.links " />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
