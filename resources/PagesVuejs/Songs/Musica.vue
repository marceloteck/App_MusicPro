<script setup>
import { computed, ref, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
  musica: {
    type: Object,
    required: true,
  },
  versions: {
    type: Array,
    default: () => [],
  },
});

const page = usePage();
const tabs = [
  { key: 'letras', label: 'Letras' },
  { key: 'cifras', label: 'Cifras' },
  { key: 'videos', label: 'Vídeos' },
  { key: 'historico', label: 'Histórico' },
];

const activeTab = ref('cifras');
const stageMode = ref(false);

const state = ref({
  fontSize: 18,
  transpose: 0,
  capoEnabled: false,
  capoFret: 0,
  chordColor: '#3fb6ff',
  boldChord: true,
  boldLyric: false,
});

const rawText = computed(() => {
  if (Array.isArray(props.musica?.lyrics)) {
    return props.musica.lyrics.join('\n');
  }
  if (typeof props.musica?.lyrics === 'string') {
    return props.musica.lyrics;
  }
  if (Array.isArray(props.musica?.letra)) {
    return props.musica.letra.join('\n');
  }
  return props.musica?.texto ?? props.musica?.chords ?? '';
});

const songTitle = computed(() => props.musica?.title ?? props.musica?.titulo ?? 'Cifra');
const songArtist = computed(() => props.musica?.artist ?? props.musica?.artista ?? '');
const songComposer = computed(() => props.musica?.composer ?? '');
const songSinger = computed(() => props.musica?.singer ?? '');
const songDescription = computed(() => props.musica?.description ?? '');

const NOTES_SHARP = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
const NOTES_FLAT = ['C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'];

const mod = (n, m) => ((n % m) + m) % m;

const preferFlatsFromChord = (text) => /[A-G]b/.test(text);

const noteToIndex = (note) => {
  const sharp = NOTES_SHARP.indexOf(note);
  if (sharp !== -1) return sharp;
  return NOTES_FLAT.indexOf(note);
};

const indexToNote = (idx, preferFlats) => (preferFlats ? NOTES_FLAT[idx] : NOTES_SHARP[idx]);

const transposeChord = (chord, semitones, preferFlats) => {
  const re = /^([A-G])([#b]?)(.*?)(?:\/([A-G])([#b]?)(.*))?$/;
  const match = chord.match(re);
  if (!match) return chord;

  const root = match[1] + (match[2] || '');
  const tail = match[3] || '';
  const bassRoot = match[4] ? match[4] + (match[5] || '') : null;
  const bassTail = match[6] || '';

  const rootIndex = noteToIndex(root);
  if (rootIndex === null) return chord;
  const newRoot = indexToNote(mod(rootIndex + semitones, 12), preferFlats);

  if (bassRoot) {
    const bassIndex = noteToIndex(bassRoot);
    if (bassIndex === null) return `${newRoot}${tail}/${bassRoot}${bassTail}`;
    const newBass = indexToNote(mod(bassIndex + semitones, 12), preferFlats);
    return `${newRoot}${tail}/${newBass}${bassTail}`;
  }

  return `${newRoot}${tail}`;
};

const looksLikeChordToken = (token) =>
  /^[A-G](#|b)?([a-zA-Z]{0,5})?\d*(\([^)]+\))?(\/[A-G](#|b)?)?$/.test(token);

const isChordLine = (line) => {
  const trimmed = line.trim();
  if (!trimmed) return false;
  if (trimmed.includes('[') && trimmed.includes(']')) return false;
  const tokens = trimmed.split(/\s+/).filter(Boolean);
  if (!tokens.length) return false;
  const chordish = tokens.filter(looksLikeChordToken).length;
  return chordish >= Math.max(1, Math.ceil(tokens.length * 0.6));
};

const extractChordsWithPositions = (line) => {
  const chords = [];
  const re = /\S+/g;
  let match;
  while ((match = re.exec(line)) !== null) {
    const chord = match[0];
    if (looksLikeChordToken(chord)) {
      chords.push({ idx: match.index, chord });
    }
  }
  return chords;
};

const parseChordProLine = (line) => {
  let lyric = '';
  const chords = [];
  let i = 0;
  while (i < line.length) {
    if (line[i] === '[') {
      const end = line.indexOf(']', i + 1);
      if (end !== -1) {
        const chord = line.slice(i + 1, end).trim();
        if (chord) chords.push({ idx: lyric.length, chord });
        i = end + 1;
        continue;
      }
    }
    lyric += line[i];
    i += 1;
  }
  return { lyric, chords };
};

const makeLineModel = (lyricLine, chords) => {
  const maxChordIdx = chords.length ? Math.max(...chords.map((c) => c.idx)) : 0;
  const len = Math.max(lyricLine.length, maxChordIdx + 1);
  const chordAt = new Map();
  chords.forEach((c) => chordAt.set(c.idx, c.chord));
  return { lyric: lyricLine.padEnd(len, ' '), chordAt, len };
};

const lineModels = computed(() => {
  const lines = rawText.value.replace(/\r\n/g, '\n').split('\n');
  const models = [];
  for (let i = 0; i < lines.length; i += 1) {
    const line = lines[i];
    if (isChordLine(line) && i + 1 < lines.length) {
      const chords = extractChordsWithPositions(line);
      const lyricLine = lines[i + 1];
      models.push({ type: 'music', model: makeLineModel(lyricLine, chords) });
      i += 1;
      continue;
    }
    if (line.includes('[') && line.includes(']')) {
      const { lyric, chords } = parseChordProLine(line);
      models.push({ type: 'music', model: makeLineModel(lyric, chords) });
      continue;
    }
    if (!line.trim()) {
      models.push({ type: 'divider' });
      continue;
    }
    models.push({ type: 'music', model: makeLineModel(line, []) });
  }
  return models;
});

const semitonesForChords = computed(() =>
  state.value.capoEnabled ? state.value.transpose - state.value.capoFret : state.value.transpose
);

const preferFlats = computed(() => preferFlatsFromChord(rawText.value));

const lineCells = (lineModel) => {
  const cells = [];
  for (let i = 0; i < lineModel.len; i += 1) {
    const chord = lineModel.chordAt.get(i) || '';
    cells.push({
      char: lineModel.lyric[i] ?? ' ',
      chord: chord ? transposeChord(chord, semitonesForChords.value, preferFlats.value) : '',
    });
  }
  return cells;
};

const increaseFont = () => {
  state.value.fontSize = Math.min(46, state.value.fontSize + 2);
};

const decreaseFont = () => {
  state.value.fontSize = Math.max(12, state.value.fontSize - 2);
};

const transposeUp = () => {
  state.value.transpose = Math.min(24, state.value.transpose + 1);
};

const transposeDown = () => {
  state.value.transpose = Math.max(-24, state.value.transpose - 1);
};

const videoUrl = computed(() => props.musica?.video_url || props.musica?.video || '');

const videoEmbedUrl = computed(() => {
  if (!videoUrl.value) return '';
  if (videoUrl.value.includes('youtube.com') || videoUrl.value.includes('youtu.be')) {
    const base = typeof window === 'undefined' ? 'http://localhost' : window.location.origin;
    const url = new URL(videoUrl.value, base);
    const id = url.searchParams.get('v') || url.pathname.split('/').pop();
    return `https://www.youtube.com/embed/${id}`;
  }
  return '';
});

const canSuggest = computed(() => !!page.props.auth?.user);
const correctionForm = useForm({
  content: '',
});

watch(
  rawText,
  (value) => {
    if (!correctionForm.content) {
      correctionForm.content = value;
    }
  },
  { immediate: true }
);

const submitCorrection = () => {
  if (!props.musica?.id) return;
  correctionForm.post(route('songs.corrections.store', props.musica.id), {
    preserveScroll: true,
  });
};
</script>

<template>
  <div
    class="music-viewer"
    :class="{ 'stage-mode': stageMode }"
    :style="{
      '--chordColor': state.chordColor,
      '--chordWeight': state.boldChord ? 800 : 600,
      '--lyricWeight': state.boldLyric ? 700 : 400,
      '--fontSize': `${state.fontSize}px`,
    }"
  >
    <header class="music-header">
      <div class="music-title">
        <h1>{{ songTitle }}</h1>
        <p v-if="songArtist" class="text-muted">{{ songArtist }}</p>
        <div class="meta" v-if="songSinger || songComposer || songDescription">
          <span v-if="songSinger">Cantor: {{ songSinger }}</span>
          <span v-if="songComposer">Compositor: {{ songComposer }}</span>
          <span v-if="songDescription">{{ songDescription }}</span>
        </div>
      </div>
      <div class="music-actions">
        <button class="btn btn-outline-secondary" @click="stageMode = !stageMode">
          {{ stageMode ? 'Modo normal' : 'Modo palco' }}
        </button>
        <button class="btn btn-outline-secondary" @click="decreaseFont">A-</button>
        <button class="btn btn-outline-secondary" @click="increaseFont">A+</button>
      </div>
    </header>

    <div class="tabs">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        :class="['tab', { active: activeTab === tab.key }]"
        @click="activeTab = tab.key"
      >
        {{ tab.label }}
      </button>
    </div>

    <section v-if="activeTab === 'letras'" class="panel">
      <pre class="lyrics-block">{{ rawText }}</pre>
    </section>

    <section v-else-if="activeTab === 'cifras'" class="panel">
      <div class="toolbar">
        <div class="pill">
          <span class="label">Tom</span>
          <button class="btn btn-sm" @click="transposeDown">-</button>
          <b>{{ state.transpose }}</b>
          <button class="btn btn-sm" @click="transposeUp">+</button>
        </div>
        <div class="pill">
          <span class="label">Capotraste</span>
          <select v-model="state.capoEnabled" class="form-select form-select-sm">
            <option :value="false">Não</option>
            <option :value="true">Sim</option>
          </select>
          <input v-model.number="state.capoFret" type="number" min="0" max="12" class="form-control form-control-sm" />
        </div>
        <div class="pill">
          <span class="label">Cor cifra</span>
          <input v-model="state.chordColor" type="color" class="form-control form-control-color" />
        </div>
        <div class="pill">
          <label class="form-check-label">
            <input v-model="state.boldChord" type="checkbox" class="form-check-input" /> Cifra
          </label>
          <label class="form-check-label">
            <input v-model="state.boldLyric" type="checkbox" class="form-check-input" /> Letra
          </label>
        </div>
      </div>

      <div class="music-sheet" :style="{ '--fs': `${state.fontSize}px` }">
        <div v-for="(line, index) in lineModels" :key="index" class="line-wrapper">
          <div v-if="line.type === 'divider'" class="divider"></div>
          <div v-else class="music-line">
            <span v-for="(cell, cellIndex) in lineCells(line.model)" :key="cellIndex" class="cell">
              <span class="chord">{{ cell.chord }}</span>
              <span class="lyric">{{ cell.char }}</span>
            </span>
          </div>
        </div>
      </div>

      <div v-if="canSuggest" class="correction-box">
        <h5>Sugerir correção</h5>
        <textarea
          v-model="correctionForm.content"
          class="form-control"
          rows="6"
          placeholder="Atualize a cifra/letre como deveria ficar"
        ></textarea>
        <button
          class="btn btn-primary mt-3"
          type="button"
          :disabled="correctionForm.processing"
          @click="submitCorrection"
        >
          Enviar correção
        </button>
      </div>
    </section>

    <section v-else-if="activeTab === 'videos'" class="panel">
      <div v-if="videoEmbedUrl" class="video-frame">
        <iframe
          :src="videoEmbedUrl"
          title="Vídeo"
          frameborder="0"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
          allowfullscreen
        ></iframe>
      </div>
      <div v-else>
        <p class="text-muted">Nenhum vídeo encontrado para esta música.</p>
        <a v-if="videoUrl" :href="videoUrl" target="_blank" rel="noreferrer">Abrir link</a>
      </div>
    </section>

    <section v-else-if="activeTab === 'historico'" class="panel">
      <div v-if="versions.length" class="history-list">
        <div v-for="item in versions" :key="item.id" class="history-item">
          <div class="history-meta">
            <strong>{{ item.source }}</strong>
            <span class="text-muted">· {{ new Date(item.created_at).toLocaleString('pt-BR') }}</span>
          </div>
          <pre class="history-content">{{ item.content }}</pre>
        </div>
      </div>
      <p v-else class="text-muted">Nenhuma versão registrada ainda.</p>
    </section>

    <section v-else class="panel">
      <p class="text-muted">Selecione uma aba para visualizar o conteúdo.</p>
    </section>
  </div>
</template>

<style scoped>
.music-viewer {
  background: #f7f8fa;
  border-radius: 16px;
  padding: 24px;
  color: #1d1f23;
}

.music-viewer.stage-mode {
  background: #0d0f14;
  color: #f1f3f7;
}

.music-viewer.stage-mode .text-muted {
  color: #a6b0bf !important;
}

.music-header {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 20px;
}

.music-title h1 {
  margin: 0;
  font-size: 28px;
  font-weight: 700;
}

.meta {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  font-size: 13px;
  color: #6c757d;
}

.tabs {
  display: flex;
  gap: 8px;
  background: #e9ecef;
  padding: 6px;
  border-radius: 12px;
  margin-bottom: 20px;
}

.music-viewer.stage-mode .tabs {
  background: #1c212b;
}

.tab {
  border: none;
  background: transparent;
  padding: 10px 16px;
  border-radius: 10px;
  font-weight: 600;
  color: #6c757d;
}

.tab.active {
  background: #ffffff;
  color: #111827;
}

.music-viewer.stage-mode .tab.active {
  background: #2b3240;
  color: #ffffff;
}

.panel {
  background: #ffffff;
  border-radius: 16px;
  padding: 20px;
}

.music-viewer.stage-mode .panel {
  background: #151a22;
}

.lyrics-block {
  white-space: pre-wrap;
  font-size: 16px;
  line-height: 1.7;
  font-family: 'Inter', sans-serif;
}

.toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 16px;
}

.pill {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #f1f3f7;
  padding: 8px 12px;
  border-radius: 999px;
  font-size: 13px;
}

.music-viewer.stage-mode .pill {
  background: #1e2531;
}

.music-sheet {
  background: #f8f9fb;
  border-radius: 12px;
  padding: 16px;
  font-family: 'Courier New', monospace;
  font-size: var(--fs);
}

.music-viewer.stage-mode .music-sheet {
  background: #0f141c;
}

.music-line {
  display: flex;
  flex-wrap: wrap;
  align-items: flex-start;
}

.cell {
  width: 1ch;
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  flex: 0 0 auto;
}

.chord {
  height: 1.1em;
  font-size: calc(var(--fontSize) * 0.8);
  line-height: 1.1em;
  color: var(--chordColor);
  font-weight: var(--chordWeight);
}

.lyric {
  height: 1.2em;
  line-height: 1.2em;
  font-weight: var(--lyricWeight);
  white-space: pre;
}

.divider {
  height: 1px;
  background: rgba(0, 0, 0, 0.08);
  margin: 12px 0;
}

.music-viewer.stage-mode .divider {
  background: rgba(255, 255, 255, 0.12);
}

.video-frame iframe {
  width: 100%;
  min-height: 360px;
  border-radius: 12px;
}

.correction-box {
  margin-top: 24px;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}

.music-viewer.stage-mode .correction-box {
  border-top-color: #2a3140;
}

.history-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.history-item {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 12px;
  background: #f9fafb;
}

.music-viewer.stage-mode .history-item {
  border-color: #2a3140;
  background: #111827;
}

.history-meta {
  display: flex;
  gap: 8px;
  align-items: center;
  margin-bottom: 8px;
}

.history-content {
  white-space: pre-wrap;
  max-height: 240px;
  overflow: auto;
  background: rgba(0, 0, 0, 0.04);
  padding: 10px;
  border-radius: 8px;
}

.music-viewer.stage-mode .history-content {
  background: rgba(255, 255, 255, 0.08);
}

@media (max-width: 768px) {
  .music-viewer {
    padding: 16px;
  }

  .music-title h1 {
    font-size: 22px;
  }

  .panel {
    padding: 16px;
  }
}
</style>
