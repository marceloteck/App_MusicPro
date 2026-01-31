<script setup>
const highlights = [
  {
    title: 'Pesquisar músicas',
    description: 'Busque por título ou trechos da letra.',
    href: '/search',
  },
  {
    title: 'Playlists',
    description: 'Monte repertórios por evento ou banda.',
    href: '/player',
  },
  {
    title: 'Offline',
    description: 'Baixe tudo e use o app sem internet.',
    href: '/settings',
  },
];
</script>

<template>
  <AppHead title="Inicio" />
  <div class="indexpage">
    <LayoutIndexPages>
      <div class="container">
        <div class="hero card shadow-sm">
          <div class="card-body">
            <h1 class="card-title">Bem-vindo ao Tocare</h1>
            <p class="card-text text-muted">
              Organize cifras, letras e vídeos em um só lugar. Prepare repertórios e mantenha tudo offline.
            </p>
            <a href="/search" class="btn btn-primary">Começar pesquisa</a>
          </div>
        </div>

        <div class="row mt-4 g-3">
          <div v-for="item in highlights" :key="item.title" class="col-md-4">
            <div class="card h-100">
              <div class="card-body">
                <h5 class="card-title">{{ item.title }}</h5>
                <p class="card-text text-muted">{{ item.description }}</p>
                <a :href="item.href" class="btn btn-outline-secondary">Abrir</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </LayoutIndexPages>
  </div>
</template>

<style lang="css" scope>
.hero {
  border-radius: 16px;
}
</style>
