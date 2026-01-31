<script setup>
const links = [
  { label: 'Configurações', href: '/settings', icon: 'settings' },
  { label: 'Ajuda', href: '/help', icon: 'help' },
  { label: 'Feedback', href: '/feedback', icon: 'feedback' },
  { label: 'Contato', href: '/contact', icon: 'call' },
  { label: 'Termos de uso', href: '/terms', icon: 'article' },
  { label: 'Sobre o app', href: '/about', icon: 'info' },
];
</script>
<template>
<AppHead title="Inicio"/>
    <div class="indexpage">
        <LayoutIndexPages>
            <div class="container">
              <h1>Mais opções</h1>
              <div class="list-group mt-3">
                <a
                  v-for="item in links"
                  :key="item.label"
                  class="list-group-item list-group-item-action d-flex align-items-center gap-2"
                  :href="item.href"
                >
                  <span class="material-symbols-outlined">{{ item.icon }}</span>
                  <span>{{ item.label }}</span>
                </a>
              </div>
            </div>
        </LayoutIndexPages>

    </div>
</template>
<style lang="css" scope>

</style>
