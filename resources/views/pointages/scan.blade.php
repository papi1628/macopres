<x-app-layout>
<x-slot name="title">Scanner QR Code</x-slot>

<div class="space-y-5" x-data="scannerApp()">

    {{-- ══════════════════════════════════════
         HEADER
    ══════════════════════════════════════ --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <h2 class="text-[14px] font-bold text-slate-800">Scanner un badge QR</h2>
            <p class="text-[11px] text-slate-400 mt-0.5">Pointage automatique à l'arrivée et au départ</p>
        </div>
        <a href="{{ route('pointages.index') }}"
           class="flex items-center gap-2 h-9 px-4 rounded-xl text-sm font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
            ← Feuille de présence
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- ══════════════════════════════════════
             ZONE SCANNER
        ══════════════════════════════════════ --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-[12px] font-semibold text-slate-800">Caméra</h3>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full animate-pulse" :class="scanning ? 'bg-green-500' : 'bg-slate-300'"></div>
                    <span class="text-[10px] font-semibold" :class="scanning ? 'text-green-600' : 'text-slate-400'"
                          x-text="scanning ? 'En cours de scan...' : 'Caméra inactive'"></span>
                </div>
            </div>

            <div class="p-5">
                {{-- Vidéo scanner --}}
                <div class="relative bg-slate-900 rounded-xl overflow-hidden" style="aspect-ratio:4/3">
                    <video id="qr-video" class="w-full h-full object-cover" playsinline></video>

                    {{-- Overlay grille --}}
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="relative w-48 h-48">
                            {{-- Coins du cadre --}}
                            <div class="absolute top-0 left-0 w-8 h-8 border-t-2 border-l-2 rounded-tl-lg" style="border-color:#378ADD"></div>
                            <div class="absolute top-0 right-0 w-8 h-8 border-t-2 border-r-2 rounded-tr-lg" style="border-color:#378ADD"></div>
                            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-2 border-l-2 rounded-bl-lg" style="border-color:#378ADD"></div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-2 border-r-2 rounded-br-lg" style="border-color:#378ADD"></div>
                            {{-- Ligne de scan animée --}}
                            <div x-show="scanning" class="absolute left-2 right-2 h-0.5 scan-line" style="background:linear-gradient(90deg,transparent,#378ADD,transparent)"></div>
                        </div>
                    </div>

                    {{-- État vide --}}
                    <div x-show="!scanning" class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                        <svg class="w-12 h-12 text-slate-600 mb-3" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                        </svg>
                        <p class="text-slate-500 text-[13px] font-medium">Caméra non démarrée</p>
                        <p class="text-slate-600 text-[11px] mt-1">Cliquez sur "Démarrer" pour activer</p>
                    </div>
                </div>

                {{-- Boutons contrôle --}}
                <div class="flex gap-3 mt-4">
                    <button @click="startScanner()"
                            x-show="!scanning"
                            class="flex-1 h-10 rounded-xl text-sm font-bold text-white transition-all hover:-translate-y-px"
                            style="background:linear-gradient(135deg,#185FA5,#378ADD)">
                        Démarrer le scan
                    </button>
                    <button @click="stopScanner()"
                            x-show="scanning"
                            class="flex-1 h-10 rounded-xl text-sm font-semibold border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors">
                        Arrêter
                    </button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════
             RÉSULTAT + HISTORIQUE SCANS
        ══════════════════════════════════════ --}}
        <div class="space-y-4">

            {{-- Résultat du dernier scan --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3.5 border-b border-slate-100">
                    <h3 class="text-[12px] font-semibold text-slate-800">Dernier scan</h3>
                </div>
                <div class="p-5">
                    {{-- État vide --}}
                    <div x-show="!lastScan" class="text-center py-6">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3"
                             style="background:#f1f5f9">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-[12px] text-slate-400">Aucun scan effectué</p>
                    </div>

                    {{-- Résultat succès --}}
                    <div x-show="lastScan && lastScan.success" class="text-center">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 text-xl font-bold text-white"
                             style="background:linear-gradient(135deg,#185FA5,#378ADD)"
                             x-text="lastScan?.employe?.initiales"></div>
                        <p class="text-[15px] font-bold text-slate-800" x-text="lastScan?.employe?.prenom + ' ' + lastScan?.employe?.nom"></p>
                        <p class="text-[11px] text-slate-400 mb-3" x-text="lastScan?.employe?.matricule"></p>

                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-[12px] font-bold mb-3"
                             :style="lastScan?.action === 'arrivee'
                                ? 'background:#EAF3DE; color:#3B6D11'
                                : 'background:#E6F1FB; color:#0C447C'">
                            <span x-text="lastScan?.action === 'arrivee' ? '✓ Arrivée' : '→ Départ'"></span>
                            <span x-text="lastScan?.heure"></span>
                        </div>

                        <div x-show="lastScan?.retard"
                             class="text-[11px] font-semibold px-3 py-1 rounded-full inline-block"
                             style="background:#FAEEDA; color:#854F0B">
                            ⚠ Retard détecté
                        </div>

                        <div x-show="lastScan?.heures_travaillees"
                             class="text-[11px] text-slate-500 mt-2">
                            Durée travaillée : <strong x-text="lastScan?.heures_travaillees"></strong>
                        </div>
                    </div>

                    {{-- Résultat erreur --}}
                    <div x-show="lastScan && !lastScan.success" class="text-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3"
                             style="background:#FCEBEB">
                            <svg class="w-6 h-6" style="color:#A32D2D" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <p class="text-[13px] font-semibold" style="color:#A32D2D">Scan échoué</p>
                        <p class="text-[11px] text-slate-400 mt-1" x-text="lastScan?.message"></p>
                    </div>
                </div>
            </div>

            {{-- Historique des scans de la session --}}
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100">
                    <h3 class="text-[12px] font-semibold text-slate-800">Scans de la session</h3>
                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                          style="background:#E6F1FB; color:#0C447C"
                          x-text="scans.length + ' scan(s)'"></span>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    <template x-if="scans.length === 0">
                        <p class="text-center text-[11px] text-slate-400 py-6">Aucun scan cette session</p>
                    </template>
                    <template x-for="(scan, index) in scans.slice().reverse()" :key="index">
                        <div class="flex items-center gap-3 px-5 py-3 border-b border-slate-50 last:border-0">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[9px] font-bold text-white flex-shrink-0"
                                 style="background:linear-gradient(135deg,#185FA5,#378ADD)"
                                 x-text="scan.employe?.initiales ?? '?'"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[12px] font-semibold text-slate-800 truncate"
                                   x-text="scan.success ? (scan.employe?.prenom + ' ' + scan.employe?.nom) : 'QR invalide'"></p>
                                <p class="text-[10px] text-slate-400"
                                   x-text="scan.heure + ' · ' + (scan.action === 'arrivee' ? 'Arrivée' : scan.action === 'depart' ? 'Départ' : 'Erreur')"></p>
                            </div>
                            <div class="w-2 h-2 rounded-full flex-shrink-0"
                                 :style="scan.success ? 'background:#3B6D11' : 'background:#A32D2D'"></div>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>

</div>

<style>
@keyframes scanLine {
    0%   { top: 8px; }
    100% { top: calc(100% - 8px); }
}
.scan-line { animation: scanLine 1.5s ease-in-out infinite alternate; }
</style>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
function scannerApp() {
    return {
        scanning: false,
        lastScan: null,
        scans: [],
        videoStream: null,
        animationFrame: null,

        async startScanner() {
            try {
                this.videoStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                const video = document.getElementById('qr-video');
                video.srcObject = this.videoStream;
                await video.play();
                this.scanning = true;
                this.scanLoop();
            } catch (err) {
                alert('Impossible d\'accéder à la caméra : ' + err.message);
            }
        },

        scanLoop() {
            const video  = document.getElementById('qr-video');
            const canvas = document.createElement('canvas');
            const ctx    = canvas.getContext('2d');

            const tick = () => {
                if (!this.scanning) return;

                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    canvas.height = video.videoHeight;
                    canvas.width  = video.videoWidth;
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code) {
                        this.handleQr(code.data);
                        return; // pause 2s après chaque scan
                    }
                }

                this.animationFrame = requestAnimationFrame(tick);
            };

            this.animationFrame = requestAnimationFrame(tick);
        },

        handleQr(qrCode) {
            this.scanning = false;

            fetch('{{ route("pointages.scanner") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ qr_code: qrCode }),
            })
            .then(r => r.json())
            .then(data => {
                data.heure = data.heure ?? new Date().toTimeString().slice(0, 5);
                this.lastScan = data;
                this.scans.push(data);

                // Reprendre le scan après 2.5s
                setTimeout(() => {
                    if (this.videoStream) {
                        this.scanning = true;
                        this.scanLoop();
                    }
                }, 2500);
            })
            .catch(() => {
                this.lastScan = { success: false, message: 'Erreur réseau. Réessayez.' };
                setTimeout(() => {
                    if (this.videoStream) {
                        this.scanning = true;
                        this.scanLoop();
                    }
                }, 2500);
            });
        },

        stopScanner() {
            this.scanning = false;
            if (this.animationFrame) cancelAnimationFrame(this.animationFrame);
            if (this.videoStream) {
                this.videoStream.getTracks().forEach(t => t.stop());
                this.videoStream = null;
            }
            document.getElementById('qr-video').srcObject = null;
        },
    };
}
</script>
</x-app-layout>