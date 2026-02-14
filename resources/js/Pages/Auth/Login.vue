<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div class="space-y-2">
                <label for="email" class="block text-sm font-medium text-zinc-300">Email</label>
                <Input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="you@example.com"
                    class="bg-zinc-950 border-zinc-700 text-zinc-100 placeholder:text-zinc-500"
                />
                <p v-if="form.errors.email" class="text-sm text-red-400">{{ form.errors.email }}</p>
            </div>

            <div class="space-y-2">
                <label for="password" class="block text-sm font-medium text-zinc-300">Password</label>
                <Input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="bg-zinc-950 border-zinc-700 text-zinc-100 placeholder:text-zinc-500"
                />
                <p v-if="form.errors.password" class="text-sm text-red-400">{{ form.errors.password }}</p>
            </div>

            <div class="flex items-center">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input
                        type="checkbox"
                        v-model="form.remember"
                        class="h-4 w-4 rounded border-zinc-600 bg-zinc-950 text-emerald-600 focus:ring-emerald-500/20 focus:ring-offset-0"
                    />
                    <span class="text-sm text-zinc-400">Remember me</span>
                </label>
            </div>

            <Button
                type="submit"
                class="w-full bg-emerald-300 hover:bg-emerald-400 text-white font-medium"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Log in
            </Button>
        </form>
    </GuestLayout>
</template>
