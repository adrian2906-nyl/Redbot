<x-app-layout>
    <div class="min-h-screen flex flex-col bg-gradient-to-br from-indigo-200 via-purple-200 to-violet-200 text-gray-800">

        <!-- Header -->
        <header class="px-6 py-6">
            <div class="max-w-4xl mx-auto">
                <div class="rounded-2xl bg-white/60 backdrop-blur-md shadow-lg ring-1 ring-black/10 p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6h4m-8 8h8m-6 4h6M6 6h.01M6 10h.01M6 14h.01" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-700">Configurador Cisco</h1>
                                <p class="text-sm text-gray-500">Generador de comandos para equipos Cisco</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button id="clearBtn" class="px-3 py-2 rounded-lg bg-white/50 hover:bg-white/70 transition text-sm text-gray-700 shadow-sm">Limpiar</button>
                            <button id="exportBtn" class="px-3 py-2 rounded-lg bg-white/50 hover:bg-white/70 transition text-sm text-gray-700 shadow-sm">Exportar</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Chat -->
        <main class="flex-1">
            <div class="max-w-4xl mx-auto px-6">
                <div class="rounded-2xl bg-white/70 backdrop-blur-lg shadow-xl ring-1 ring-black/10 overflow-hidden">

                    <!-- Zona de mensajes -->
                    <div id="messages"
                         class="h-[58vh] md:h-[65vh] overflow-y-auto p-4 space-y-4 scroll-smooth bg-gradient-to-b from-white/70 to-white/40">
                        <div class="flex items-start gap-3">
                            <div class="shrink-0 h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700">🤖</div>
                            <div class="max-w-[85%] md:max-w-[70%] rounded-2xl bg-white text-gray-800 px-4 py-3 shadow">
                                <p class="font-semibold text-indigo-700">CiscoBot</p>
                                <p class="leading-relaxed text-gray-700">
                                    ¡Hola! Soy tu asistente de configuración Cisco.<br>
                                    Dime qué necesitas. Ej.: <span class="font-mono">crear vlan 20 en gi0/1-2</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Composer -->
                    <div class="border-t border-gray-200 bg-white/70 p-3">
                        <div class="flex flex-wrap gap-2 px-1 pb-2">
                            <button class="chip" data-fill="crear vlan 10 en puertos gi0/1-2">VLAN 10</button>
                            <button class="chip" data-fill="asignar ip 192.168.10.1/24 a interfaz vlan 10">IP Vlan</button>
                            <button class="chip" data-fill="habilitar trunk en gi0/1 allowed 10,20 nativo 10">Trunk</button>
                            <button class="chip" data-fill="configurar dhcp pool ventas 192.168.20.0/24 gateway 192.168.20.1">DHCP</button>
                        </div>

                        <form id="composer" class="flex gap-3">
                            <input id="prompt" type="text"
                                   placeholder="Escribe tu consulta… Ej.: crear vlan 20 en puertos fa0/1-2"
                                   class="flex-1 rounded-full border border-gray-300 px-5 py-3 text-gray-700 focus:ring-2 focus:ring-indigo-300 bg-white/80" />
                            <button type="submit"
                                    class="px-6 py-3 rounded-xl bg-gradient-to-r from-indigo-400 to-purple-400 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold shadow-md">
                                Enviar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <button id="toBottom"
                class="hidden fixed bottom-28 right-6 md:right-10 rounded-full p-3 bg-indigo-300/60 hover:bg-indigo-400/70 shadow-md backdrop-blur text-white">
            ↓
        </button>
    </div>

    @push('scripts')
    <script>
        const messages = document.getElementById('messages');
        const composer = document.getElementById('composer');
        const prompt   = document.getElementById('prompt');
        const clearBtn = document.getElementById('clearBtn');
        const exportBtn= document.getElementById('exportBtn');
        const toBottom = document.getElementById('toBottom');

        const scrollToBottom = (smooth=true) => {
            messages.scrollTo({ top: messages.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });
        };

        const bubble = (side, html) => {
            const wrap = document.createElement('div');
            wrap.className = `flex items-start gap-3 ${side === 'me' ? 'justify-end' : ''}`;
            const avatar = side === 'me'
                ? '<div class="shrink-0 h-9 w-9 rounded-full bg-indigo-300 flex items-center justify-center">👤</div>'
                : '<div class="shrink-0 h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700">🤖</div>';
            const base = side === 'me' ? 'bg-indigo-100 text-gray-800' : 'bg-white text-gray-800';
            wrap.innerHTML = `
                ${side === 'me' ? '' : avatar}
                <div class="max-w-[85%] md:max-w-[70%] rounded-2xl ${base} px-4 py-3 shadow-sm group">
                    ${html}
                    <div class="mt-2 flex gap-2 opacity-0 group-hover:opacity-100 transition">
                        <button class="copy px-2 py-1 text-xs rounded bg-black/5">Copiar</button>
                    </div>
                </div>
                ${side === 'me' ? avatar : ''}`;
            messages.appendChild(wrap);
            scrollToBottom();
        };

        messages.addEventListener('click', (e) => {
            if (e.target.classList.contains('copy')) {
                const text = e.target.closest('.group').innerText.trim();
                navigator.clipboard.writeText(text);
                e.target.textContent = 'Copiado';
                setTimeout(()=> e.target.textContent = 'Copiar', 1200);
            }
        });

        messages.addEventListener('scroll', () => {
            const nearBottom = messages.scrollHeight - messages.scrollTop - messages.clientHeight < 60;
            toBottom.classList.toggle('hidden', nearBottom);
        });
        toBottom.addEventListener('click', () => scrollToBottom());

        document.querySelectorAll('.chip').forEach(ch => {
            ch.className = "px-3 py-1 rounded-full bg-indigo-100 hover:bg-indigo-200 text-sm text-gray-700 transition";
            ch.addEventListener('click', () => prompt.value = ch.dataset.fill);
        });

        clearBtn.addEventListener('click', () => { messages.innerHTML = ''; });
        exportBtn.addEventListener('click', () => {
            const text = messages.innerText.replace(/\n{3,}/g, '\n\n');
            const blob = new Blob([text], {type: 'text/plain'});
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'conversacion-ciscobot.txt';
            a.click();
        });

        composer.addEventListener('submit', (e) => {
            e.preventDefault();
            const q = prompt.value.trim();
            if (!q) return;
            bubble('me', `<p class="whitespace-pre-wrap">${q}</p>`);
            prompt.value = '';
            // Aquí conectas tu backend/API
        });
    </script>
    @endpush

    <style>
        pre { white-space: pre-wrap; }
        code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    </style>
</x-app-layout>
