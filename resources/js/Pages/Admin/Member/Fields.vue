<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import VueSelect from 'vue-select';
import PrimaryButton from '@/Components/PrimaryButton.vue';
const props = defineProps({
    member: {
        type: Object,
        required: false
    },
    users: {
        type: Object,
        required: true
    }
})

const form = useForm({
    user_id: '',
    membership_duration: '1'
})

onMounted(()=>{
    if (props.member) {
        form.user_id = props.member.user_id
    }
})

function save(){
    if (props.member) {
        form.patch(route('members.update', props.member));
    }else{
        form.post(route('members.store'));
    }
}

</script>

<template>
    <AuthenticatedLayout>
        <Head>
            <title>Create Member</title>
        </Head>
        <template #header>
            <div>
                <h1 class="text-lg font-semibold">Create Member</h1>
            </div>
        </template>

        <section class="m-8 sm:m-6">
            <div class="max-w-7xl mx-auto mt-6 bg-white rounded-md shadow-md p-6">
                <form class="w-full" @submit.prevent="save">
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3 mb-6 md:mb-0">
                            <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                                for="grid-first-name">
                                User
                            </label>
                            <VueSelect :options="users" v-model="form.user_id" label="name"
                                :reduce="user => user.id" :disabled="member"></VueSelect>
                        </div>
                        
                    </div>
                    <div class="flex flex-wrap -mx-3 mb-6">
                        <div class="w-full px-3 mb-6 md:mb-0">
                            <label for="published_date"
                                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">Membership Duration</label>
                            <select v-model="form.membership_duration" class="w-full bg-gray-200 border-transparent rounded" required>
                                <option value="">Select period...</option>
                                <option value="1">1 Month</option>
                                <option value="3">3 Month</option>
                                <option value="6">6 Month</option>
                                <option value="12">12 Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-wrap mb-6">
                        <PrimaryButton type="submit">
                            {{ member? 'Extend': 'Save' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </section>

    </AuthenticatedLayout>
</template>