<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  imports: {
    type: Array,
    default: () => [],
  },
});

const Title = 'Importação de músicas';
const form = useForm({
  urls: '',
});

const submit = () => {
  form.post(route('admin.imports.store'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppHead :title="Title" />
  <LayoutSubPages :title="Title">
    <div class="container-fluid">
      <p class="text-muted">
        Cole vários links (um por linha) para importar automaticamente músicas de sites externos.
      </p>

      <form @submit.prevent="submit" class="mb-4">
        <textarea
          v-model="form.urls"
          class="form-control"
          rows="6"
          placeholder="https://www.cifraclub.com.br/..."
          required
        ></textarea>
        <button class="btn btn-primary mt-3" type="submit" :disabled="form.processing">
          Importar músicas
        </button>
      </form>

      <div class="card">
        <div class="card-header">Últimas importações</div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>Link</th>
                <th>Status</th>
                <th>Mensagem</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in props.imports" :key="item.id">
                <td class="text-break">{{ item.url }}</td>
                <td>
                  <span
                    class="badge"
                    :class="{
                      'bg-success': item.status === 'success',
                      'bg-warning text-dark': item.status === 'pending',
                      'bg-danger': item.status === 'failed',
                    }"
                  >
                    {{ item.status }}
                  </span>
                </td>
                <td>{{ item.message }}</td>
              </tr>
              <tr v-if="props.imports.length === 0">
                <td colspan="3" class="text-center text-muted">
                  Nenhuma importação registrada ainda.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </LayoutSubPages>
</template>
