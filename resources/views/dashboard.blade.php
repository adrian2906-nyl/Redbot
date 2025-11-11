{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    {{-- Fondo + blobs animados --}}
    <div class="relative min-h-[calc(100vh-0px)] overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-gradient-to-br from-sky-500 via-indigo-600 to-violet-700"></div>
        <div class="pointer-events-none absolute -top-24 -left-24 h-[32rem] w-[32rem] rounded-full bg-white/10 blur-3xl -z-10 animate-float"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-24 h-[28rem] w-[28rem] rounded-full bg-white/10 blur-3xl -z-10 animate-float-delayed"></div>

        {{-- NAV superior --}}
        <header class="max-w-7xl mx-auto px-6 pt-6 flex items-center justify-between">
            <div class="flex items-center gap-2 text-white font-extrabold tracking-tight">
                <div class="w-8 h-8 rounded-lg bg-white text-slate-900 grid place-items-center text-xs shadow">CB</div>
                <span class="text-xl">CiscoBot</span>
            </div>
            <nav class="hidden sm:flex items-center gap-6 text-white/90 text-sm">
                <a class="hover:underline" href="{{ url('/') }}">Inicio</a>
                <a class="hover:underline" href="{{ url('/docs') }}">Documentación</a>
                <a class="hover:underline" href="{{ url('/ayuda') }}">Ayuda</a>
            </nav>
        </header>

        {{-- HERO --}}
        <div class="max-w-7xl mx-auto px-6 text-center text-white mt-12">
            <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight drop-shadow-md">
                Asistente de Configuración Cisco
            </h1>
            <p class="mt-3 text-white/85">
                Genera comandos para <span class="font-medium">switches</span>, <span class="font-medium">routers</span> y más
            </p>
        </div>

        {{-- TARJETAS bonitos --}}
        <main class="max-w-7xl mx-auto px-6 py-14">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 place-items-center">
                {{-- Card: Chat --}}
                <a href="{{ url('/chat') }}"
                   class="group w-full max-w-md p-[1px] rounded-3xl bg-gradient-to-r from-white/40 to-white/10 hover:from-white/60 hover:to-white/20 transition">
                    <div class="rounded-3xl bg-white/10 backdrop-blur-xl border border-white/25 p-8 text-center shadow-2xl relative overflow-hidden">
                        <div class="absolute -top-16 -right-16 h-40 w-40 bg-white/10 rounded-full blur-2xl"></div>

                        <div class="mx-auto mb-5 w-12 h-12 grid place-items-center rounded-2xl bg-white/15 text-white shadow-md group-hover:scale-105 transition transform-gpu">
                            <svg viewBox="0 0 24 24" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 10h8M8 14h5M4 6h16v9a3 3 0 01-3 3H8l-4 4V6z"/>
                            </svg>
                        </div>

                        <h3 class="text-2xl font-semibold text-white drop-shadow">Chat Principal</h3>
                        <p class="mt-2 text-white/80 text-sm">Conversación directa con el asistente</p>

                        <div class="mt-7 inline-flex items-center justify-center px-6 py-2.5 rounded-full font-semibold text-slate-900
                                    bg-gradient-to-r from-sky-300 to-violet-300 shadow-lg group-hover:shadow-xl transition
                                    group-hover:translate-y-[-1px]">
                            Comenzar Chat
                            <span class="ml-2 opacity-70 group-hover:opacity-100 transition">→</span>
                        </div>

                        {{-- Shine --}}
                        <span class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition
                                     bg-[radial-gradient(1200px_200px_at_20%_-20%,rgba(255,255,255,.35),transparent)]"></span>
                    </div>
                </a>

                {{-- Card: Configurador --}}
                <a href="{{ url('/configurador') }}"
                   class="group w-full max-w-md p-[1px] rounded-3xl bg-gradient-to-r from-white/40 to-white/10 hover:from-white/60 hover:to-white/20 transition">
                    <div class="rounded-3xl bg-white/10 backdrop-blur-xl border border-white/25 p-8 text-center shadow-2xl relative overflow-hidden">
                        <div class="absolute -top-16 -left-16 h-40 w-40 bg-white/10 rounded-full blur-2xl"></div>

                        <div class="mx-auto mb-5 w-12 h-12 grid place-items-center rounded-2xl bg-white/15 text-white shadow-md group-hover:scale-105 transition transform-gpu">
                            <svg viewBox="0 0 24 24" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11 11l-8 8 2 2 8-8m1-5a4 4 0 105.66-5.66l-3.18 3.18a2 2 0 11-2.83 2.83L12 6z"/>
                            </svg>
                        </div>

                        <h3 class="text-2xl font-semibold text-white drop-shadow">Configurador Cisco</h3>
                        <p class="mt-2 text-white/80 text-sm">Generador especializado de comandos</p>

                        <div class="mt-7 inline-flex items-center justify-center px-6 py-2.5 rounded-full font-semibold text-slate-900
                                    bg-gradient-to-r from-sky-300 to-violet-300 shadow-lg group-hover:shadow-xl transition
                                    group-hover:translate-y-[-1px]">
                            Abrir Configurador
                            <span class="ml-2 opacity-70 group-hover:opacity-100 transition">→</span>
                        </div>

                        <span class="pointer-events-none absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 transition
                                     bg-[radial-gradient(1200px_200px_at_80%_-20%,rgba(255,255,255,.35),transparent)]"></span>
                    </div>
                </a>
            </div>

            {{-- Footer mini --}}
            <p class="mt-14 text-center text-white/70 text-xs">© {{ now()->year }} CiscoBot</p>
        </main>
    </div>

    {{-- extras CSS --}}
    <style>
        @keyframes float { from { transform: translateY(0) } 50% { transform: translateY(-12px) } to { transform: translateY(0) } }
        .animate-float { animation: float 12s ease-in-out infinite; }
        .animate-float-delayed { animation: float 14s 1s ease-in-out infinite; }
    </style>
</x-app-layout>
