<script setup>
import qs from 'qs';
import { ref, onMounted } from 'vue';
import debounce from 'lodash.debounce';
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '../Components/Pagination.vue';
import Badge from '@/Pages/Admin/Components/Badge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    members: {
        type: Object,
        required: true
    }
});

const status = ref('');
const searchValue = ref('');

onMounted(() => {
    const query = qs.parse(window.location.search, { ignoreQueryPrefix: true });
    status.value = query.filter;
    searchValue.value = query.search;
});

const search = debounce(() => {
    if (status.value === '' && searchValue.value === '') {
        router.get(route('check-outs.index'), {}, {
            preserveState: true,
            replace: true
        });
        return;
    }
    router.get(route('members.index'), {
        ...(searchValue.value && { 'search': searchValue.value }),
        ...(status.value && { 'filter': status.value }),
    }, { preserveState: true, replace: true });
}, 500);

function reset() {
    status.value = '';
    searchValue.value = '';
    router.get(route('members.index'), {}, {
        preserveState: true,
        replace: true
    });
}
</script>
<template>
    <AuthenticatedLayout>

        <Head>
            <title>Members</title>
            <meta name="description" content="Your page description">
        </Head>
        <template #header>
            <div class="flex justify-between">
                <h1 class="text-lg font-bold">Members</h1>
                <Link :href="route('members.create')" as="button" class="bg-gray-800 hover:bg-gray-700">Add Member</Link>
            </div>
        </template>

        <section class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="my-4 flex justify-between">
                <div class="flex gap-4 flex-nowrap">
                    <input type="text" placeholder="Search by member..." class="rounded border-transparent bg-gray-200"
                        v-model="searchValue" @input="search" autofocus>
                    <button class="bg-gray-200 text-gray-800 hover:bg-gray-300" type="button" @click="reset">reset</button>
                </div>
                <div>
                    <label>Filter: </label>
                    <select class="uppercase rounded border-transparent bg-gray-200" v-model="status" @input="search">
                        <option value="">select..</option>
                        <option value="active" class="uppercase">Active</option>
                        <option value="expired" class="uppercase">Expired</option>
                    </select>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Membership</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    <tr v-for="member in members.data" :key="member.id">
                        <td>{{ member.id }}</td>
                        <td>
                            <Link :href="route('members.show', member.id)" class="text-sm font-semibold text-blue-700">
                            {{ member.user.name }}
                            </Link>
                        </td>
                        <td>
                            {{ member.membership_due_date }}
                        </td>
                        <td>{{ member.type }}</td>
                        <td>
                            <Badge
                                :class="{ 'text-red-800 bg-red-100': !member.membership_status, 'text-emerald-800 bg-emerald-100': member.membership_status }">
                                {{ member.membership_status ? 'active' : 'expired' }}
                            </Badge>
                        </td>
                        <td>
                            <div class="flex flex-row gap-2 justify-end">
                                <Link :href="route('members.edit', member.id)" as="button"
                                    class="bg-gray-800 hover:bg-gray-700">
                                Edit
                                </Link>
                                <Link :href="route('members.destroy', member.id)" method="delete" as="button"
                                    class="bg-red-500 hover:bg-red-400">
                                Delete
                                </Link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="members.data.length == 0">
                        <td colspan="6" class="font-semibold text-center text-lg">No members available.</td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="members.links" :only="['members']"/>
        </section>
    </AuthenticatedLayout>
</template>