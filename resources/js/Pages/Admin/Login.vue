<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    form.post('/admin/login', {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="تسجيل الدخول - لوحة التحكم" />

    <div class="min-h-screen bg-gradient-to-br from-[#1C2833] to-[#1B4F72] flex items-center justify-center p-4" dir="rtl">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="/images/doctorato-logo.png" alt="Doctorato" class="w-56 h-auto mx-auto mb-4 logo-white" />
                <p class="text-white/60 text-sm">لوحة تحكم الموقع</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">تسجيل الدخول</h2>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">البريد الإلكتروني</label>
                        <input
                            v-model="form.email"
                            type="email"
                            dir="ltr"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#1B4F72] focus:border-transparent outline-none transition"
                            placeholder="admin@doctorato.com"
                            required
                            autofocus
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور</label>
                        <input
                            v-model="form.password"
                            type="password"
                            dir="ltr"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-[#1B4F72] focus:border-transparent outline-none transition"
                            placeholder="••••••••"
                            required
                        />
                    </div>

                    <!-- Remember -->
                    <div class="flex items-center gap-2">
                        <input v-model="form.remember" type="checkbox" id="remember" class="rounded border-gray-300 text-[#1B4F72] focus:ring-[#1B4F72]" />
                        <label for="remember" class="text-sm text-gray-600">تذكرني</label>
                    </div>

                    <!-- Error -->
                    <div v-if="form.errors.email" class="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg">
                        {{ form.errors.email }}
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full bg-[#1B4F72] text-white py-3 rounded-lg font-medium hover:bg-[#1B4F72]/90 transition disabled:opacity-50"
                    >
                        {{ form.processing ? 'جاري الدخول...' : 'دخول' }}
                    </button>
                </form>
            </div>

            <!-- Back to site -->
            <div class="text-center mt-6">
                <a href="/" class="text-white/50 hover:text-white text-sm transition">← العودة للموقع</a>
            </div>
        </div>
    </div>
</template>
