<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  corrections: {
    type: Array,
    default: () => [],
  },
});

const Title = 'Correções de cifras';

const approve = (id) => {
  useForm({}).post(route('admin.corrections.approve', id), {
    preserveScroll: true,
  });
};

const reject = (id) => {
  useForm({}).post(route('admin.corrections.reject', id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <AppHead :title="Title" />
  <LayoutSubPages :title="Title">
    <div class="container-fluid">
      <div class="card">
        <div class="card-header">Sugestões pendentes</div>
        <div class="card-body p-0">
          <table class="table mb-0">
            <thead>
              <tr>
                <th>Música</th>
                <th>Usuário</th>
                <th>Status</th>
                <th>Correção</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in props.corrections" :key="item.id">
                <td class="text-break">{{ item.song?.title || 'N/D' }}</td>
                <td>{{ item.user?.name || 'N/D' }}</td>
                <td>
                  <span
                    class="badge"
                    :class="{
                      'bg-warning text-dark': item.status === 'pending',
                      'bg-success': item.status === 'approved',
                      'bg-danger': item.status === 'rejected',
                    }"
                  >
                    {{ item.status }}
                  </span>
                </td>
                <td>
                  <pre class="correction-preview">{{ item.content }}</pre>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <button
                      class="btn btn-sm btn-success"
                      @click="approve(item.id)"
                      :disabled="item.status !== 'pending'"
                    >
                      Aprovar
                    </button>
                    <button
                      class="btn btn-sm btn-outline-danger"
                      @click="reject(item.id)"
                      :disabled="item.status !== 'pending'"
                    >
                      Rejeitar
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="props.corrections.length === 0">
                <td colspan="5" class="text-center text-muted">Nenhuma correção encontrada.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </LayoutSubPages>
</template>

<style scoped>
.correction-preview {
  max-height: 160px;
  overflow: auto;
  background: #f7f7f7;
  padding: 8px;
  border-radius: 8px;
  white-space: pre-wrap;
}
</style>
