<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  musica: {
    type: Object,
    required: true,
  },
});

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
  return props.musica?.texto ?? '';
});

const songTitle = computed(() => props.musica?.title ?? props.musica?.titulo ?? 'Cifra');
const songArtist = computed(() => props.musica?.artist ?? props.musica?.artista ?? '');

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

const linesPerPage = computed(() => {
  const headerHeight = 120;
  const footerHeight = 36;
  const pageHeight = 1123;
  const lineHeight = state.value.fontSize * 1.8;
  return Math.max(1, Math.floor((pageHeight - headerHeight - footerHeight - 80) / lineHeight));
});

const paginatedLines = computed(() => {
  const chunkSize = linesPerPage.value;
  const pages = [];
  let current = [];
  lineModels.value.forEach((line) => {
    current.push(line);
    if (current.length >= chunkSize) {
      pages.push(current);
      current = [];
    }
  });
  if (current.length) pages.push(current);
  return pages;
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
  state.value.fontSize = Math.max(10, state.value.fontSize - 2);
};

const transposeUp = () => {
  state.value.transpose = Math.min(24, state.value.transpose + 1);
};

const transposeDown = () => {
  state.value.transpose = Math.max(-24, state.value.transpose - 1);
};
</script>

<template>
  <div
    class="cifra-viewer"
    :style="{
      '--chordColor': state.chordColor,
      '--chordWeight': state.boldChord ? 800 : 600,
      '--lyricWeight': state.boldLyric ? 700 : 400,
    }"
  >
    <div class="topbar">
      <button class="btn" @click="decreaseFont">A-</button>
      <button class="btn" @click="increaseFont">A+</button>

      <div class="pill">
        <span class="label">Fonte</span>
        <b>{{ state.fontSize }}px</b>
      </div>

      <div class="pill">
        <span class="label">Tom</span>
        <button class="btn" @click="transposeDown">-</button>
        <b>{{ state.transpose }}</b>
        <button class="btn" @click="transposeUp">+</button>
        <span class="helper">(semitons)</span>
      </div>

      <div class="pill">
        <span class="label">Capotraste</span>
        <select v-model="state.capoEnabled">
          <option :value="false">Não</option>
          <option :value="true">Sim</option>
        </select>
        <span class="label">Traste</span>
        <input v-model.number="state.capoFret" type="number" min="0" max="12" />
      </div>

      <div class="pill">
        <span class="label">Cor da cifra</span>
        <input v-model="state.chordColor" type="color" />
      </div>

      <div class="pill">
        <span class="label">Negrito</span>
        <label>
          <input v-model="state.boldChord" type="checkbox" /> Cifra
        </label>
        <label>
          <input v-model="state.boldLyric" type="checkbox" /> Letra
        </label>
      </div>

      <button class="btn" @click="() => window.print()">Imprimir</button>
    </div>

    <div class="pages-wrap" :style="{ '--fs': `${state.fontSize}px` }">
      <div v-for="(page, pageIndex) in paginatedLines" :key="pageIndex" class="page">
        <div class="header">
          <p class="title">{{ songTitle }}</p>
          <p class="subtitle">
            <span class="badge">{{ songArtist }}</span>
            <span class="badge">Transposição: {{ state.transpose }}</span>
            <span class="badge">Capotraste: {{ state.capoEnabled ? state.capoFret : 'Não' }}</span>
          </p>
        </div>

        <div class="content">
          <div v-for="(line, index) in page" :key="index" class="line-wrapper">
            <div v-if="line.type === 'divider'" class="divider"></div>
            <div v-else class="music-line">
              <span
                v-for="(cell, cellIndex) in lineCells(line.model)"
                :key="cellIndex"
                class="cell"
              >
                <span class="chord">{{ cell.chord }}</span>
                <span class="lyric">{{ cell.char }}</span>
              </span>
            </div>
          </div>
        </div>

        <div class="footer">Página {{ pageIndex + 1 }} / {{ paginatedLines.length }}</div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cifra-viewer {
  font-family: "Inter", sans-serif;
}

.topbar {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  align-items: center;
  padding: 12px;
  border-bottom: 1px solid #e0e0e0;
  background: #fff;
  position: sticky;
  top: 0;
  z-index: 5;
}

.btn {
  padding: 6px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  background: #f7f7f7;
  cursor: pointer;
}

.pill {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  background: #f0f4f8;
  border-radius: 999px;
  font-size: 13px;
}

.pill label {
  display: flex;
  align-items: center;
  gap: 4px;
}

.helper {
  font-size: 12px;
  opacity: 0.7;
}

.pages-wrap {
  padding: 24px;
  background: #f5f6f8;
}

.page {
  background: #fff;
  padding: 24px;
  margin-bottom: 24px;
  border-radius: 12px;
  min-height: 1123px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
}

.header {
  margin-bottom: 16px;
}

.title {
  font-size: 24px;
  margin: 0;
}

.subtitle {
  margin: 6px 0 0;
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.badge {
  background: #eef2f7;
  border-radius: 6px;
  padding: 4px 8px;
  font-size: 12px;
}

.content {
  flex: 1;
}

.music-line {
  display: flex;
  flex-wrap: wrap;
  font-family: "Courier New", monospace;
  font-size: var(--fs);
}

.cell {
  display: inline-flex;
  flex-direction: column;
  min-width: 0.6em;
  align-items: center;
}

.chord {
  color: var(--chordColor);
  font-weight: var(--chordWeight);
  font-size: 0.85em;
  line-height: 1;
}

.lyric {
  font-weight: var(--lyricWeight);
  line-height: 1.4;
}

.divider {
  height: 16px;
}

.footer {
  text-align: right;
  font-size: 12px;
  color: #666;
  margin-top: 16px;
}

@media print {
  .topbar {
    display: none;
  }

  .pages-wrap {
    padding: 0;
    background: #fff;
  }

  .page {
    box-shadow: none;
    margin: 0;
    border-radius: 0;
    page-break-after: always;
  }
}
</style>
