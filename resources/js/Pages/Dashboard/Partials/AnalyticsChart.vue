<script setup>
import { computed, ref } from 'vue';

/**
 * A dependency-free 14-day views/clicks area chart (pure SVG).
 * The backend guarantees a fixed-length, zero-filled series, so the
 * component only has to worry about geometry and interaction.
 */
const props = defineProps({
    series: { type: Array, required: true },
});

const W = 600;
const H = 220;
const PAD_X = 12;
const PAD_TOP = 16;
const PAD_BOTTOM = 28;

const uid = `chart-${Math.random().toString(36).slice(2, 9)}`;

const totals = computed(() => ({
    views: props.series.reduce((sum, day) => sum + Number(day.views ?? 0), 0),
    clicks: props.series.reduce((sum, day) => sum + Number(day.clicks ?? 0), 0),
}));

const maxVal = computed(() => Math.max(
    1,
    ...props.series.map((day) => Math.max(Number(day.views ?? 0), Number(day.clicks ?? 0))),
));

const step = computed(() => (props.series.length > 1
    ? (W - 2 * PAD_X) / (props.series.length - 1)
    : 0));

function yFor(value) {
    const usable = H - PAD_TOP - PAD_BOTTOM;
    return H - PAD_BOTTOM - (value / maxVal.value) * usable;
}

const points = computed(() => props.series.map((day, index) => ({
    x: PAD_X + index * step.value,
    yv: yFor(Number(day.views ?? 0)),
    yc: yFor(Number(day.clicks ?? 0)),
})));

/** Catmull-Rom interpolated path for a smooth-but-not-overshooting curve. */
function smoothPath(key) {
    const pts = points.value;
    if (pts.length === 0) return '';
    if (pts.length === 1) return `M ${pts[0].x.toFixed(1)} ${pts[0][key].toFixed(1)}`;

    let d = `M ${pts[0].x.toFixed(1)} ${pts[0][key].toFixed(1)}`;
    for (let i = 0; i < pts.length - 1; i++) {
        const p0 = pts[i - 1] || pts[i];
        const p1 = pts[i];
        const p2 = pts[i + 1];
        const p3 = pts[i + 2] || p2;
        const c1x = p1.x + (p2.x - p0.x) / 6;
        const c1y = p1[key] + (p2[key] - p0[key]) / 6;
        const c2x = p2.x - (p3.x - p1.x) / 6;
        const c2y = p2[key] - (p3[key] - p1[key]) / 6;
        d += ` C ${c1x.toFixed(1)} ${c1y.toFixed(1)}, ${c2x.toFixed(1)} ${c2y.toFixed(1)}, ${p2.x.toFixed(1)} ${p2[key].toFixed(1)}`;
    }
    return d;
}

function areaPath(key) {
    const line = smoothPath(key);
    if (!line) return '';
    const base = H - PAD_BOTTOM;
    return `${line} L ${(W - PAD_X).toFixed(1)} ${base} L ${PAD_X} ${base} Z`;
}

const viewsLine = computed(() => smoothPath('yv'));
const clicksLine = computed(() => smoothPath('yc'));
const viewsArea = computed(() => areaPath('yv'));
const clicksArea = computed(() => areaPath('yc'));

const yTicks = computed(() => {
    const nice = (v) => (Math.round(v * 10) / 10).toString();
    return [0, maxVal.value / 2, maxVal.value].map((value) => ({
        value,
        label: nice(value),
        y: yFor(value),
    }));
});

const xLabels = computed(() => {
    const n = props.series.length;
    if (n === 0) return [];
    const indices = n > 1 ? [0, Math.floor((n - 1) / 2), n - 1] : [0];
    return indices.map((index) => ({
        x: points.value[index].x,
        label: new Date(`${props.series[index].date}T00:00:00`)
            .toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
    }));
});

const hoverIndex = ref(null);

function onMove(event) {
    const rect = event.currentTarget.getBoundingClientRect();
    const ratio = (event.clientX - rect.left) / rect.width;
    const x = ratio * W;
    if (step.value === 0) {
        hoverIndex.value = props.series.length ? 0 : null;
        return;
    }
    const index = Math.round((x - PAD_X) / step.value);
    hoverIndex.value = Math.min(props.series.length - 1, Math.max(0, index));
}

function onLeave() {
    hoverIndex.value = null;
}

const hoveredDay = computed(() => (hoverIndex.value === null ? null : props.series[hoverIndex.value]));
const hoveredPoints = computed(() => (hoverIndex.value === null ? null : points.value[hoverIndex.value]));

function formatNumber(value) {
    return new Intl.NumberFormat().format(Number(value ?? 0));
}
</script>

<template>
    <div v-motion-pop class="card">
        <div class="p-6 sm:p-8">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="card-title">Audience</h2>
                    <p class="card-subtitle">Views and link clicks over the last 14 days.</p>
                </div>
                <div class="flex items-center gap-5 text-xs font-semibold">
                    <span class="inline-flex items-center gap-2 text-slate-300">
                        <span class="h-2.5 w-2.5 rounded-full bg-indigo-400"></span>
                        {{ formatNumber(totals.views) }} views
                    </span>
                    <span class="inline-flex items-center gap-2 text-slate-300">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                        {{ formatNumber(totals.clicks) }} clicks
                    </span>
                </div>
            </div>

            <div class="relative mt-6 select-none" @mousemove="onMove" @mouseleave="onLeave">
                <svg :viewBox="`0 0 ${W} ${H}`" class="w-full h-auto block" role="img"
                     aria-label="Daily page views and link clicks chart">
                    <defs>
                        <linearGradient :id="`${uid}-views`" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#818cf8" stop-opacity="0.35" />
                            <stop offset="100%" stop-color="#818cf8" stop-opacity="0.02" />
                        </linearGradient>
                        <linearGradient :id="`${uid}-clicks`" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#34d399" stop-opacity="0.28" />
                            <stop offset="100%" stop-color="#34d399" stop-opacity="0.02" />
                        </linearGradient>
                    </defs>

                    <!-- grid -->
                    <g v-for="tick in yTicks" :key="tick.label + tick.y">
                        <line :x1="PAD_X" :x2="W - PAD_X" :y1="tick.y" :y2="tick.y"
                              stroke="rgba(148, 163, 184, 0.12)" stroke-width="1" />
                        <text :x="PAD_X + 4" :y="tick.y - 4" class="fill-slate-500" font-size="9">{{ tick.label }}</text>
                    </g>

                    <!-- series -->
                    <path :d="viewsArea" :fill="`url(#${uid}-views)`" />
                    <path :d="clicksArea" :fill="`url(#${uid}-clicks)`" />
                    <path :d="viewsLine" fill="none" stroke="#818cf8" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round" />
                    <path :d="clicksLine" fill="none" stroke="#34d399" stroke-width="2"
                          stroke-linecap="round" stroke-linejoin="round" />

                    <!-- x labels -->
                    <g v-for="label in xLabels" :key="label.x">
                        <text :x="label.x" :y="H - 8" text-anchor="middle" class="fill-slate-500" font-size="9">
                            {{ label.label }}
                        </text>
                    </g>

                    <!-- hover crosshair -->
                    <g v-if="hoveredPoints">
                        <line :x1="hoveredPoints.x" :x2="hoveredPoints.x" :y1="PAD_TOP" :y2="H - PAD_BOTTOM"
                              stroke="rgba(148, 163, 184, 0.35)" stroke-width="1" stroke-dasharray="3 3" />
                        <circle :cx="hoveredPoints.x" :cy="hoveredPoints.yv" r="4" fill="#818cf8"
                                stroke="#0f172a" stroke-width="2" />
                        <circle :cx="hoveredPoints.x" :cy="hoveredPoints.yc" r="4" fill="#34d399"
                                stroke="#0f172a" stroke-width="2" />
                    </g>
                </svg>

                <!-- tooltip -->
                <div v-if="hoveredDay"
                     class="pointer-events-none absolute top-0 z-10 -translate-x-1/2 rounded-lg bg-slate-950/95 border border-slate-700/60 px-3 py-2 text-xs shadow-xl"
                     :style="{ left: `${(hoveredPoints.x / W) * 100}%` }">
                    <p class="font-semibold text-slate-200 mb-1">
                        {{ new Date(`${hoveredDay.date}T00:00:00`).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }) }}
                    </p>
                    <p class="text-indigo-300 flex items-center justify-between gap-4">
                        <span>Views</span><span class="font-semibold">{{ formatNumber(hoveredDay.views) }}</span>
                    </p>
                    <p class="text-emerald-300 flex items-center justify-between gap-4">
                        <span>Clicks</span><span class="font-semibold">{{ formatNumber(hoveredDay.clicks) }}</span>
                    </p>
                </div>

                <div v-if="totals.views === 0 && totals.clicks === 0"
                     class="absolute inset-0 flex items-center justify-center">
                    <p class="text-slate-500 text-sm bg-slate-950/60 rounded-full px-4 py-1.5 border border-slate-800">
                        No traffic yet — share your page to collect analytics.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
