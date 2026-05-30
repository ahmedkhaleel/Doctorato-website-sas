<script setup>
/**
 * Dependency-free SVG sparkline.
 *
 * Renders a smooth line through `points` (array of numbers), with
 * an optional filled area underneath. No d3, no charting library —
 * the project's been disciplined about not adding deps for things
 * we can do in 50 lines of SVG.
 *
 * Props:
 *   points     — number[]; missing/null values are skipped
 *   width      — render width in px (the SVG scales to its container)
 *   height     — render height in px
 *   stroke     — line colour
 *   fill       — area fill under the line (set to 'none' to disable)
 *   labels     — optional string[] same length as points; if supplied,
 *                hovering a data point shows the label + value tooltip
 */
import { computed, ref } from 'vue';

const props = defineProps({
    points: { type: Array, default: () => [] },
    width: { type: Number, default: 260 },
    height: { type: Number, default: 60 },
    stroke: { type: String, default: '#0A1628' },
    fill: { type: String, default: 'rgba(196, 162, 101, 0.15)' },
    labels: { type: Array, default: null },
    format: { type: Function, default: (v) => v },
});

const padding = 4;
const cleanPoints = computed(() => props.points.map((p) => Number.isFinite(p) ? p : null));

const range = computed(() => {
    const vals = cleanPoints.value.filter((p) => p !== null);
    if (!vals.length) return { min: 0, max: 1 };
    const min = Math.min(...vals);
    const max = Math.max(...vals);
    return { min, max: max === min ? max + 1 : max };
});

const pixelPoints = computed(() => {
    const n = cleanPoints.value.length;
    if (n < 2) return [];
    const { min, max } = range.value;
    const w = props.width - padding * 2;
    const h = props.height - padding * 2;
    return cleanPoints.value.map((v, i) => {
        if (v === null) return null;
        const x = padding + (i / (n - 1)) * w;
        const y = padding + h - ((v - min) / (max - min)) * h;
        return { x, y, v, i };
    });
});

// Build the line path (skipping gaps from null values cleanly).
const linePath = computed(() => {
    let d = '';
    let pen = 'M';
    for (const p of pixelPoints.value) {
        if (p === null) { pen = 'M'; continue; }
        d += `${pen}${p.x.toFixed(1)},${p.y.toFixed(1)} `;
        pen = 'L';
    }
    return d.trim();
});

const areaPath = computed(() => {
    const valid = pixelPoints.value.filter(Boolean);
    if (valid.length < 2) return '';
    const first = valid[0];
    const last = valid[valid.length - 1];
    const baseY = props.height - padding;
    let d = `M${first.x.toFixed(1)},${baseY.toFixed(1)} `;
    for (const p of valid) {
        d += `L${p.x.toFixed(1)},${p.y.toFixed(1)} `;
    }
    d += `L${last.x.toFixed(1)},${baseY.toFixed(1)} Z`;
    return d;
});

const hovered = ref(null);
function onHover(p) { hovered.value = p; }
function onLeave() { hovered.value = null; }
</script>

<template>
    <div class="relative inline-block w-full">
        <svg :viewBox="`0 0 ${width} ${height}`" :width="width" :height="height"
             class="block w-full h-auto" preserveAspectRatio="none">
            <path v-if="fill !== 'none' && areaPath" :d="areaPath" :fill="fill" stroke="none" />
            <path v-if="linePath" :d="linePath" :stroke="stroke" stroke-width="1.5"
                  fill="none" stroke-linecap="round" stroke-linejoin="round" />
            <circle v-for="p in pixelPoints.filter(Boolean)" :key="p.i"
                    :cx="p.x" :cy="p.y" r="6" fill="transparent"
                    @mouseenter="onHover(p)" @mouseleave="onLeave" />
            <circle v-if="hovered" :cx="hovered.x" :cy="hovered.y" r="3" :fill="stroke" />
        </svg>
        <div v-if="hovered" class="absolute -translate-x-1/2 -translate-y-full bg-[#0A1628] text-white text-[10px] px-2 py-1 rounded shadow whitespace-nowrap pointer-events-none"
             :style="{ left: `${(hovered.x / width) * 100}%`, top: `${(hovered.y / height) * 100}%` }">
            <span v-if="labels && labels[hovered.i]">{{ labels[hovered.i] }}: </span>
            <strong>{{ format(hovered.v) }}</strong>
        </div>
    </div>
</template>
