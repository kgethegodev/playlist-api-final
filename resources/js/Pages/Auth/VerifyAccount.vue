<script setup>
import {Link, router, useForm} from "@inertiajs/vue3";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import {onMounted} from "vue";
import axios from "axios";

const props = defineProps({
    token: {
        required: true,
        type: String
    }
})

onMounted(async () => {
    if(!props.token)
        router.get('/')

    await axios.post('/api/v1/auth/verify', {token: props.token}).then(res => {
        console.log('res: ', res)
    }).catch(() => {
        router.get('/')
    })
})
</script>

<template>
    <GuestLayout>
        <h1 class="font-bold text-2xl text-center">Thank you :)</h1>
        <p class="text-center mb-6">Your account has been verified!</p>
        <Link href="/auth/login" class="w-full block p-4 text-center bg-gray-100 rounded transition-all">Login</Link>
    </GuestLayout>
</template>

<style scoped lang="scss">

</style>
