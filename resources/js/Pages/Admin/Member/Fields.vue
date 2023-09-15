<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, computed } from 'vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
const props = defineProps({
    member: {
        type: Object,
        required: false
    }
})

const form = useForm({
    name: '',
    email: '',
    membership_duration: '0'
})

onMounted(() => {
    if (props.member) {
        form.name = props.member.user.name;
        form.email = props.member.user.email;
    }
})

function save() {
    if (props.member) {
        form.patch(route('members.update', props.member));
    } else {
        form.post(route('members.store'));
    }
}

const pageTitle = computed(() => {
    return props.member ? 'Edit' : 'Create';
});

</script>

<template>
    <AuthenticatedLayout>

        <Head>
            <title>{{ pageTitle }} Member</title>
        </Head>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold">{{}} Member</h1>
            </div>
        </template>

        {{ form.errors }}
        <section class="m-8 sm:m-6">
            <div class="max-w-7xl mx-auto mt-6 bg-white rounded-md shadow-md p-6">
                <form class="w-full" @submit.prevent="save">
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-first-name">
                                Name
                            </label>
                            <input
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                                id="grid-first-name" type="text" placeholder="Name of user..." v-model="form.name">
                        </div>
                        <div class="w-full md:w-1/2 px-3">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-last-name">
                                Email
                            </label>
                            <input
                                class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500"
                                id="grid-last-name" type="email" placeholder="Email of user..." v-model="form.email">
                        </div>
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3 mb-6 md:mb-0">
                            <label for="published_date"
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Membership
                                Duration</label>
                            <select v-model="form.membership_duration" class="w-full bg-gray-200 border-transparent rounded"
                                required>
                                <option value="0">Select period...</option>
                                <option value="1">1 Month</option>
                                <option value="3">3 Month</option>
                                <option value="6">6 Month</option>
                                <option value="12">12 Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-6">
                        <PrimaryButton type="submit">
                            {{ member ? 'Update' : 'Save' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </section>

    </AuthenticatedLayout>
</template>