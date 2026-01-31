<template>
  <div>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <!-- Botão para abrir o modal -->
    <button @click="openModal">Abrir Menu</button>

    
<div class="BottomBlack" v-if="BottomOpen()" @click.self="closeModal"></div>
    <!-- Modal Bottom Sheet -->
    <div class="bottom-sheet" :class="{ open: isModalOpen }" >
      <!-- Área de arrasto para fechar -->
      <div class="drag-area" @mousedown="startDrag" @touchstart="startDrag" 
      @click.self="closeModal">
        <span class="material-symbols-outlined">vertical_align_bottom</span>
      </div>

      <div class="modal-content">
        <template v-if="user">
          <a href="/profile">Editar Perfil</a>
          <a href="#" @click="logout">Sair</a>
        </template>
        <template v-else>
          <a href="/login">Login</a>
          <a href="/register">Cadastro</a>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const isModalOpen = ref(false);
const user = ref(true); // Exemplo, altere conforme necessário

const openModal = () => {
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
};
const BottomOpen = () => isModalOpen.value;

const logout = () => {
  console.log("Usuário deslogado");
  closeModal();
};



// Função para arrastar e fechar
let startY = 0;
let isDragging = false;

const startDrag = (event) => {
  isDragging = true;
  startY = event.touches ? event.touches[0].clientY : event.clientY;

  window.addEventListener("mousemove", onDrag);
  window.addEventListener("touchmove", onDrag);
  window.addEventListener("mouseup", stopDrag);
  window.addEventListener("touchend", stopDrag);
};

const onDrag = (event) => {
  if (!isDragging) return;
  const currentY = event.touches ? event.touches[0].clientY : event.clientY;
  const deltaY = currentY - startY;

  if (deltaY > 100) { // Se arrastar para baixo 100px ou mais, fecha o modal
    closeModal();
  }
};

const stopDrag = () => {
  isDragging = false;
  window.removeEventListener("mousemove", onDrag);
  window.removeEventListener("touchmove", onDrag);
  window.removeEventListener("mouseup", stopDrag);
  window.removeEventListener("touchend", stopDrag);
};
</script>

<style scoped>
/* Estilizando o modal */
.bottom-sheet {
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  height: 0; /* Começa fechado */
  background-color: #fff;
  border-top: 1px solid #e5e3e3;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
  box-shadow: 6px 10px 20px 9px rgba(0, 0, 0, 0.2);
  transform: translateY(100%);
  transition: transform 0.3s ease-in-out, height 0s 0.3s;
  z-index: 105;
  overflow-y: auto;
}
.BottomBlack{
  position: absolute;
  top: 0px;
  bottom: 0px;
  width: 100vw;
  height: 100vh;
  background: rgba(27, 27, 27, 0.7);
  z-index: 100;
  transform: translateY(0);
  transition: transform 0.3s ease-in-out, height 0s 0.3s;
}

/* Quando o modal estiver aberto */
.bottom-sheet.open {
  min-height: 45vh;
  transform: translateY(0);
  transition: transform 0.3s ease-in-out;
}

/* Estilizando a área de arrasto */
.drag-area {
  padding: 3px;
  background-color: #f7f4f4;
  
  cursor: pointer;
  display: flex;
  text-align: center;
  justify-content: center;
  align-items: center;
}

/* Conteúdo do modal */
.modal-content {
  padding: 20px;
  max-height: 80vh;
  overflow-y: auto;
}

/* Botão de abrir */
button {
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
}
</style>
