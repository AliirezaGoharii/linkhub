<script setup>
/**
 * QR code share card: renders the user's public page URL as a QR code
 * client-side (qrcode npm package) with a download button.
 */
import { onMounted, ref, watch } from 'vue';
import QRCode from 'qrcode';
import { usePageStore } from '@/stores/pageStore';

const pageStore = usePageStore();
const canvasRef = ref(null);

async function render() {
    if (!canvasRef.value) return;
    await QRCode.toCanvas(canvasRef.value, pageStore.publicUrl, {
        width: 220,
        margin: 1,
        color: { dark: '#0F172A', light: '#E2E8F0' },
        errorCorrectionLevel: 'M',
    });
}

function download() {
    if (!canvasRef.value) return;
    const link = document.createElement('a');
    link.download = `linkhub-${pageStore.username}.png`;
    link.href = canvasRef.value.toDataURL('image/png');
    link.click();
}

onMounted(render);
watch(() => pageStore.publicUrl, render);
</script>

<template>
    <div class="card p-6 sm:p-8">
        <h3 class="text-lg font-semibold text-slate-300 font-rubik">Share Your Page</h3>
        <p class="mt-1 text-sm text-slate-400 font-inter">Let people scan and follow.</p>

        <div class="mt-5 flex items-center gap-6">
            <div class="shrink-0 rounded-xl bg-slate-200 p-2 shadow-lg shadow-black/30">
                <canvas ref="canvasRef" class="block rounded-md" width="220" height="220" aria-label="QR code linking to your public page"></canvas>
            </div>
            <div class="min-w-0">
                <p class="text-sm text-slate-400 break-all font-mono">{{ pageStore.publicUrl }}</p>
                <button @click="download" class="animated-button bg-slate-700 hover:bg-slate-600 text-slate-200 mt-4">
                    Download PNG
                </button>
            </div>
        </div>
    </div>
</template>
