<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

interface Lead {
    id: number;
    name: string;
    email: string;
    status: string;
    value: string;
}

const props = defineProps<{
    leads: Lead[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Leads', href: '/leads' },
];

const isModalOpen = ref(false);
const editMode = ref(false);
const editingId = ref<number | null>(null);

const form = useForm({
    name: '',
    email: '',
    status: 'New',
});

const openModal = (lead: Lead | null = null) => {
    if (lead) {
        editMode.value = true;
        editingId.value = lead.id;
        form.name = lead.name;
        form.email = lead.email;
        form.status = lead.status;
    } else {
        editMode.value = false;
        editingId.value = null;
        form.reset();
    }
    isModalOpen.value = true;
    setTimeout(() => $('#lead-name').focus(), 100);
};

const closeModal = () => {
    isModalOpen.value = false;
    editMode.value = false;
    form.reset();
};

const submit = () => {
    if (editMode.value && editingId.value) {
        form.put(`/leads/${editingId.value}`, {
            onSuccess: () => {
                closeModal();
                $('#success-toast').text('✅ Lead updated successfully').slideDown().delay(3000).slideUp();
            },
        });
    } else {
        form.post('/leads', {
            onSuccess: () => {
                closeModal();
                $('#success-toast').text('✅ Lead added successfully').slideDown().delay(3000).slideUp();
            },
        });
    }
};

onMounted(() => {
    $('table tbody tr').hover(
        function() { $(this).addClass('bg-gray-50 dark:bg-zinc-800/50'); },
        function() { $(this).removeClass('bg-gray-50 dark:bg-zinc-800/50'); }
    );

    $('#table-search').on('keyup', function() {
        const value = ($(this).val() as string).toLowerCase();
        $('table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});
</script>

<template>
    <Head title="Leads" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4 space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="relative w-full md:w-96">
                    <input 
                        type="text" 
                        id="table-search"
                        placeholder="Search leads..." 
                        class="w-full p-2 rounded-lg border-gray-300 dark:bg-zinc-900 dark:border-zinc-700 text-sm focus:ring-indigo-500"
                    />
                </div>
                
                <button 
                    @click="openModal(null)"
                    class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition shadow-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Lead
                </button>
            </div>

            <div class="overflow-hidden rounded-xl border border-sidebar-border/70 bg-white dark:bg-zinc-900 shadow-sm">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-zinc-800/50 text-gray-600 dark:text-gray-400 border-b border-sidebar-border/70">
                        <tr>
                            <th class="px-6 py-4 font-semibold">Contact</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold">Value</th>
                            <th class="px-6 py-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-800">
                        <tr v-for="lead in leads" :key="lead.id" class="transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ lead.name }}</div>
                                <div class="text-xs text-gray-500">{{ lead.email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span 
                                    :class="{
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': lead.status === 'New',
                                        'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400': lead.status === 'Warm',
                                        'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': lead.status === 'Closed',
                                        'bg-gray-100 text-gray-800 dark:bg-zinc-800 dark:text-zinc-400': lead.status === 'Cold'
                                    }"
                                    class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                                >
                                    {{ lead.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-mono text-zinc-600 dark:text-zinc-400">{{ lead.value }}</td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openModal(lead)" class="text-indigo-600 hover:text-indigo-900 font-semibold transition-colors">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <h2 class="text-xl font-bold">{{ editMode ? 'Edit Lead' : 'New Lead' }}</h2>
                    <button @click="closeModal" class="text-zinc-400 hover:text-zinc-600">&times;</button>
                </div>

                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Full Name</label>
                        <input id="lead-name" v-model="form.name" type="text" required class="w-full rounded-lg border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 focus:ring-indigo-500 p-2 " />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Email</label>
                        <input v-model="form.email" type="email" required class="w-full rounded-lg border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 focus:ring-indigo-500 p-2 " />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-zinc-500 mb-1">Status</label>
                        <select v-model="form.status" class="w-full rounded-lg border-zinc-300 dark:bg-zinc-800 dark:border-zinc-700 focus:ring-indigo-500 p-2 ">
                            <option value="New">New</option>
                            <option value="Warm">Warm</option>
                            <option value="Cold">Cold</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="closeModal" class="flex-1 px-4 py-2 text-sm font-semibold bg-zinc-100 dark:bg-zinc-800 rounded-lg transition-colors">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="flex-1 px-4 py-2 text-sm font-semibold bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                            {{ form.processing ? 'Saving...' : (editMode ? 'Update Lead' : 'Save Lead') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div id="success-toast" class="hidden fixed bottom-10 right-10 bg-zinc-900 text-white px-6 py-3 rounded-xl shadow-2xl z-[100] border border-zinc-700">
            ✅ Success
        </div>
    </AppLayout>
</template>