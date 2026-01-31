<template>
    <div>
        
    </div>
</template>
<script setup>
import { watchEffect, onMounted } from 'vue';

const props = defineProps(['color']);

// Função para alterar a cor do navegador
const setThemeColor = (color) => {
  const metaTag = document.querySelector('meta[name="theme-color"]');
  if (metaTag) {
    metaTag.setAttribute("content", color);
  } else {
    console.warn("Meta tag 'theme-color' não encontrada.");
  }
};

// Aplica a cor inicial quando o componente for montado
onMounted(() => {
  setThemeColor(props.color || '#3498db');
});

// Observa mudanças na `prop` color e aplica a cor automaticamente
watchEffect(() => {
  setThemeColor(props.color || '#3498db');
});
</script>

