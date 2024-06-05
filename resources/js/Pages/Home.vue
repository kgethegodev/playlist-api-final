<script setup>
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {Head, useForm} from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";

defineProps({
    message: {
        type: String,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    prompt: '',
    name: '',
    contact_number: '',
})

const submit = () => {
    form.post(route('create.playlist'), {
         onFinish: () => form.reset(['prompt','name','contact_number']),
    });
};
</script>

<template>
    <Head title="Home" />

    <div class="home-container">
        <div>
            <h1 class="md:text-2xl text-xl text-center">Spotify Playlist Generator</h1>
            <input-error v-if="status === 'failed'" :message="message"  class="mb-2"/>
            <p v-if="status === 'success'" class="text-sm text-green-500">Playlist has been created you should receive the link on WhatsApp shortly.</p>
        </div>

        <form @submit.prevent="submit" class="lg:w-1/4 mx-auto w-full">
            <div class="mb-4">
                <InputLabel for="prompt" value="Instruction" />
                <TextInput
                    id="prompt"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.prompt"
                    placeholder="Make me a hip hop playlist to workout to."
                    required
                    autofocus
                />
            </div>

            <div class="mb-4">
                <InputLabel for="name" value="Full name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    placeholder="Enter your full name"
                    required
                    autofocus
                />
            </div>

            <div>
                <InputLabel for="number" value="Whatsapp number" />
                <TextInput
                    id="number"
                    type="tel"
                    class="mt-1 block w-full"
                    placeholder="Enter your Whatsapp number"
                    v-model="form.contact_number"
                    required
                    autofocus
                />
            </div>

            <div class="flex items-center justify-start mt-4">
                <PrimaryButton class="w-full text-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    <p class="text-center mb-0 w-full font-bold">Generate</p>
                </PrimaryButton>
            </div>
        </form>
    </div>
</template>

<style scoped lang="scss">
.home-container {
    height: 100dvh;
    display: flex;
    flex-direction: column;
    align-content: center;
    justify-content: center;
    gap: 40px;
    padding: 20px;

    p {
        text-align: center;
    }
}
</style>
