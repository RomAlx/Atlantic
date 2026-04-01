<script setup>
import { ref, watch } from 'vue';
import { useFeedbackStore } from '../stores/feedbackStore';

const props = defineProps({
    sourcePath: {
        type: String,
        required: true,
    },
});

const form = ref({ name: '', phone: '', email: '', message: '' });
const feedbackStore = useFeedbackStore();

watch(
    () => props.sourcePath,
    () => {
        feedbackStore.clearFormErrors();
    },
);

watch(
    () => [form.value.name, form.value.phone, form.value.email, form.value.message],
    () => {
        if (feedbackStore.errorBanner || feedbackStore.validationErrors) {
            feedbackStore.clearFormErrors();
        }
    },
);

const submit = async () => {
    const ok = await feedbackStore.submit({
        ...form.value,
        source_page: props.sourcePath,
    });

    if (ok) {
        form.value = { name: '', phone: '', email: '', message: '' };
    }
};
</script>

<template>
    <section id="feedback-form">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="h1"><p><span class="underline_bottom">Форма обратной связи</span></p></div>
                </div>
                <div class="col-xxl-6 col-xl-7 col-lg-8 col-md-10 col-sm-12">
                    <div class="at_feedback_form_wrap mx-auto">
                        <div
                            v-if="feedbackStore.errorMessages.length"
                            class="at_form_error_card mb-3"
                            role="alert"
                        >
                            <ul class="at_form_error_list mb-0">
                                <li v-for="(msg, i) in feedbackStore.errorMessages" :key="i">{{ msg }}</li>
                            </ul>
                        </div>
                        <div id="at_form_cont">
                            <div class="mb-3"><input v-model="form.name" class="form-control" placeholder="Ваше имя"></div>
                            <div class="mb-3"><input v-model="form.phone" class="form-control" placeholder="Ваш телефон"></div>
                            <div class="mb-3"><input v-model="form.email" type="email" class="form-control" placeholder="Ваш email"></div>
                            <div class="mb-3"><textarea v-model="form.message" class="form-control" rows="4" placeholder="Сообщение"></textarea></div>
                            <button :disabled="feedbackStore.submitting" type="button" class="at_but_style" @click="submit">
                                {{ feedbackStore.submitting ? 'Отправка...' : 'Отправить' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
