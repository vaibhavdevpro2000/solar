<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';

// 1. Modal State
const isModalOpen = ref(false);

const form = useForm({
    name: '',
    email: '',
    status: 'New',
});

const submit = () => {
    form.post('/leads', {
        onSuccess: () => {
            closeModal();
            // Use jQuery to show a success notification
            $('#toast').slideDown().delay(3000).slideUp();
        },
    });
};

const openModal = () => {
    isModalOpen.ref = true;
    // jQuery: Auto-focus the name input after Vue renders the modal
    setTimeout(() => $('#lead-name').focus(), 100);
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};
</script>

<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Leads Dashboard</h1>
            <button @click="openModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-sm">
                + Add New Lead
            </button>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <h2 class="text-xl font-semibold">New Lead Details</h2>
                    <button @click="closeModal" class="text-zinc-400 hover:text-zinc-600 text-2xl">&times;</button>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div>
                        <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Full Name</label>
                        <input id="lead-name" v-model="form.name" type="text" required
                            class="mt-1 w-full rounded-lg py-2 border-zinc-300 dark:bg-zinc-800 focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Email Address</label>
                        <input v-model="form.email" type="email" required
                            class="mt-1 w-full rounded-lg py-2 border-zinc-300 dark:bg-zinc-800 focus:ring-2 focus:ring-indigo-500" />
                    </div>

                    <div class="flex gap-3 mt-6">
                        <button type="button" @click="closeModal" 
                            class="flex-1 px-4 py-2 bg-zinc-100 dark:bg-zinc-800 rounded-lg font-medium">Cancel</button>
                        <button type="submit" :disabled="form.processing"
                            class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Create Lead' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="toast" class="hidden fixed top-5 right-5 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-[100]">
            ✅ New lead added to the system!
        </div>
    </div>
</template>