<template>
    <transition name="fade">
      <div v-if="show" class="splash">
        <img src="https://upload.wikimedia.org/wikipedia/commons/0/09/Logotipo_da_JBS_%282023%29.png" alt="Logo do App">
      </div>
    </transition>
  </template>
  
  <script>
  export default {
    data() {
      return {
        show: true
      };
    },
    mounted() {
      setTimeout(() => {
        this.show = false;
      }, 2000); // Tempo da Splash Screen antes de desaparecer
    }
  };
  </script>
  
  <style scoped>
  .splash {
    position: fixed;
    width: 100%;
    height: 100vh;
    background-color: #1e1e1e;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
  }
  
  /* Ajuste do logo */
  .splash img {
    width: 150px;
    animation: fadeIn 1.5s ease-in-out;
  }
  
  /* Animação do logo */
  @keyframes fadeIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
  }
  
  /* Transição Vue */
  .fade-enter-active, .fade-leave-active {
    transition: opacity 1s ease-out;
  }
  .fade-enter, .fade-leave-to {
    opacity: 0;
  }
  </style>
<!-- COMO USAR
<template>
  <div>
    <SplashScreen v-if="showSplash" @animationend="showSplash = false"/>
    <div v-if="!showSplash" class="content">
      <h1>Bem-vindo ao Meu App!</h1>
    </div>
  </div>
</template>

<script>
import SplashScreen from "./components/SplashScreen.vue";

export default {
  components: {
    SplashScreen
  },
  data() {
    return {
      showSplash: true
    };
  },
  mounted() {
    setTimeout(() => {
      this.showSplash = false;
    }, 3000); // Tempo da Splash antes de sumir
  }
};
</script>

<style>
body {
  margin: 0;
  padding: 0;
}

.content {
  text-align: center;
  padding: 20px;
}
</style>

-->