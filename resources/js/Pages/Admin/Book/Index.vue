<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Pagination from '../Components/Pagination.vue';
import Badge from '@/Pages/Admin/Components/Badge.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import qs from 'qs';
import debounce from 'lodash.debounce';

const props = defineProps({
    books: {
        type: Object,
        required: true
    }
});

const searchValue = ref('');

onMounted(() => {
    const query = qs.parse(window.location.search, { ignoreQueryPrefix: true });
    searchValue.value = query.search;
});

const search = debounce(() => {
    if (searchValue.value === '') {
        router.get(route('books.index'), {}, { preserveState: true, replace: true });
        return;
    }
    router.get(route('books.index'),
        { search: searchValue.value },
        { preserveState: true, replace: true });
}, 500);

function reset() {
    searchValue.value = '';
    router.get(route('books.index'), {}, {
        preserveState: true,
        replace: true
    });
}

</script>

<template>
    <AuthenticatedLayout>

        <Head>
            <title>Books</title>
            <meta name="description" content="Your page description">
        </Head>
        <template #header>
            <div class="flex justify-between">
                <h1 class="text-lg font-bold">Books</h1>
                <Link :href="route('books.create')" as="button" class="bg-gray-800 hover:bg-gray-700">Add Book</Link>
            </div>
        </template>

        <section class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex gap-4 flex-nowrap my-4">
                    <input type="text" placeholder="Search by book name..." class="rounded border-transparent bg-gray-200"
                        v-model="searchValue" @input="search" autofocus>
                    <button class="bg-gray-200 text-gray-800 hover:bg-gray-300" type="button" @click="reset">reset</button>
                </div>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Author</th>
                        <th>Publish</th>
                        <th>Unit</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    <tr v-for="book in books.data" :key="book.id">
                        <td>
                            <Link :href="route('books.show', book.id)" class="font-semibold hover:text-blue-500">
                            {{ book.title }}
                            </Link>
                            <Badge v-if="book.is_new">New</Badge>
                        </td>
                        <td class="min-w-max">{{ book.author }}</td>
                        <td>{{ book.publish_year }}</td>
                        <td>{{ book.available }}</td>
                        <td>
                            <div class="flex flex-row gap-2 justify-end">
                                <Link :href="route('books.edit', book.id)" as="button"
                                    class="bg-gray-800 hover:bg-gray-700">
                                Edit
                                </Link>
                                <Link :href="route('books.destroy', book.id)" method="delete" as="button"
                                    class="bg-red-500 hover:bg-red-400">
                                Delete
                                </Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <Pagination :links="books.links" />
        </section>
    </AuthenticatedLayout>
</template>