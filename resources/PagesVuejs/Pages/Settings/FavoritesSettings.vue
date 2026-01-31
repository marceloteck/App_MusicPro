<script setup>
import { ref } from 'vue';
import { user, avatarUrl } from "@resources/plugins/DadosUser.js";

const users = ref({
  name: user.value.name || '',
  email: user.value.email || '',
  photo: avatarUrl || '' 
});

const Title = "Favoritos";
</script>
<template>
<AppHead :title="Title"/>
    <div class="profilePage">
        <LayoutSubPages :title="Title">
           <div class="container-fluid">
                <h2>{{Title}}</h2>
           </div>
        </LayoutSubPages>

    </div>
</template>
<style lang="css" scope>

</style>