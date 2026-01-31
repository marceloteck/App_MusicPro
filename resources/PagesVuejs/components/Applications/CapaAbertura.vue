<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import { router } from '@inertiajs/vue3'

const props = defineProps({
  type: String,
  segundos: [String, Number] 
});

const typeAmostra = props.type || "OnLoad";
const sgds = parseInt(props.segundos) || 3000;

const isLoading = ref(true);

const imgSrc = "/Assets/image/Logo/favicon.png";
const imgLoaded = ref(false); // Controla a animação da imagem

onMounted(async () => { 
 const img = new Image();
 img.src = imgSrc;

 const jaViuCapa = sessionStorage.getItem("jaViuCapa");

    if (jaViuCapa) {
            isLoading.value = false;
        } else {
        img.onload = () => {
            imgLoaded.value = true; // Ativa a transição da imagem
            
            sessionStorage.setItem("jaViuCapa", "true");

            if (typeAmostra == "time") {
            setTimeout(() => {
                isLoading.value = false;
            }, sgds);
            
            }
        }; 
    }

    if (typeAmostra == "OnLoad") {
        await nextTick(); 
        isLoading.value = false; 
    }
});
</script>

<template>
<!---->
  <div class="capa" v-if="isLoading">
    <img :src="imgSrc" alt="Logo" :class="{ 'show': imgLoaded }">
  </div>
</template>

<style lang="css" scope>
/**background: linear-gradient(45deg, #067AAC, #0BBADC, #D1422E, #CB771B, #732D15, #E27423, #008EC6); 


*/
    .capa{
        top: 0px;
        left: 0px;
        width: 100vw;
        height: 100vh;
        background: linear-gradient(135deg, rgb(179, 62, 80), rgb(158, 112, 20), rgb(3, 91, 185), rgba(13, 13, 13, 1));

        /*background: radial-gradient(circle, #FF6A3D 10%, #FFAA00 40%, #007AFF 70%, #0D0D0D 100%);
        /*background: linear-gradient(135deg, #FF6A3D, #FFAA00, #007AFF, #0D0D0D);*/
        background-size: 400% 400%;
        animation: gradientAnimation 10s ease infinite;
        position: fixed;
        z-index: 9999;
        color: aliceblue;
        align-content: center;
        justify-content: center;
        align-items: center;
        display: flex;
    }
    .capa img{
        width: 20rem;
        height: auto;
        animation: logoAnimation 3s ease infinite;
        opacity: 0;
        transform: translateY(-20px);
        transition: opacity 1s ease-out, transform 1s ease-out;
    }
    .capa img.show {
        opacity: 1;
        transform: translateY(0);
    }
    .capaNone{
        display: none;
    }
    @keyframes gradientAnimation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
    }
    @keyframes logoAnimation {
    0% { width:20rem; opacity: 1; }
    50% { width:19.7rem; opacity: 0.8; }
    100% { width:20rem; opacity: 1;}
    }
    

</style>