<template>
    <div>
        <h2>{{ message }}</h2>
        <button @click="changeMessage">Changer le message</button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            message: 'Bonjour, Vue.js!',
        };
    },
    methods: {
        changeMessage() {
            this.message = 'Le message a été changé!';
        },
    },
};
</script>
