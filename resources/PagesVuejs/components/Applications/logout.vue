<script setup>
import { router } from '@inertiajs/vue3';

const logout = () => {
    router.post('/logout'); // Faz a requisição para deslogar
};
</script>

<template>
    <button @click="logout" class="btn btn-logout">
        Sair
    </button>
</template>

<style>
.btn-logout {
    color: rgb(255, 255, 255);
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: 0.3s;
    margin-left: 20px;
}

.btn-logout:hover {
    background-color: #1b1b1b;
    color: rgb(255, 193, 24);
}
</style>
