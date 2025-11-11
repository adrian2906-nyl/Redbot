{{-- resources/views/dashboard.blade.php --}}
<x-app-layout>
    {{-- SIN barra de Jetstream --}}
    <x-slot name="header"></x-slot>

    <section class="cb-hero">
        <header class="cb-topnav">
            <div class="cb-brand">
                <div class="cb-logo">CB</div>
                <span>CiscoBot</span>
            </div>
            <nav class="cb-links">
                <a href="{{ url('/') }}">Inicio</a>
                <a href="{{ url('/docs') }}">Documentación</a>
                <a href="{{ url('/ayuda') }}">Ayuda</a>
            </nav>
        </header>

        <div class="cb-headline">
            <h1>Asistente de Configuración Cisco</h1>
            <p>Genera comandos para switches, routers y más</p>
        </div>

        <div class="cb-cards">
            <a class="cb-card" href="{{ url('/chat') }}">
                <div class="cb-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 10h8M8 14h5M4 6h16v9a3 3 0 01-3 3H8l-4 4V6z"/>
                    </svg>
                </div>
                <h3>Chat Principal</h3>
                <p>Conversación directa con el asistente</p>
                <div class="cb-btn">Comenzar Chat <span>→</span></div>
            </a>

            <a class="cb-card" href="{{ url('/configurador') }}">
                <div class="cb-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M11 11l-8 8 2 2 8-8m1-5a4 4 0 105.66-5.66l-3.18 3.18a2 2 0 11-2.83 2.83L12 6z"/>
                    </svg>
                </div>
                <h3>Configurador Cisco</h3>
                <p>Generador especializado de comandos</p>
                <div class="cb-btn">Abrir Configurador <span>→</span></div>
            </a>
        </div>

        <footer class="cb-foot">© {{ now()->year }} CiscoBot</footer>

        <!-- blobs decorativos -->
        <div class="cb-blob cb-blob-a"></div>
        <div class="cb-blob cb-blob-b"></div>
    </section>

    {{-- CSS PURO, con prefijo cb- para evitar colisiones --}}
    <style>
        .cb-hero{
            position:relative; min-height:calc(100vh - 0px); padding:24px; overflow:hidden;
            color:#fff; 
            background:
              radial-gradient(1200px 800px at 10% 5%, rgba(255,255,255,.09), transparent 60%),
              radial-gradient(900px 600px at 85% 0%, rgba(255,255,255,.07), transparent 55%),
              linear-gradient(135deg,#0ea5e9,#4f46e5 45%,#8b5cf6);
        }
        .cb-topnav{max-width:1120px;margin:0 auto 12px;display:flex;align-items:center;justify-content:space-between}
        .cb-brand{display:flex;align-items:center;gap:10px;font-weight:800;letter-spacing:.2px}
        .cb-logo{width:30px;height:30px;border-radius:9px;background:linear-gradient(135deg,#fff,#e5e9f2);
                 color:#0b1220;display:grid;place-items:center;font-size:13px;box-shadow:0 6px 14px rgba(0,0,0,.25)}
        .cb-links a{color:#fff;opacity:.9;text-decoration:none;margin-left:18px;font-size:14px}
        .cb-links a:hover{opacity:1;text-decoration:underline}

        .cb-headline{text-align:center;margin:30px auto 18px;max-width:1120px}
        .cb-headline h1{margin:0;font-size:clamp(28px,5vw,48px);font-weight:700}
        .cb-headline p{margin:10px 0 0;color:rgba(255,255,255,.9)}

        .cb-cards{max-width:1120px;margin:26px auto 0;display:grid;grid-template-columns:1fr;gap:22px}
        @media (min-width:740px){ .cb-cards{ grid-template-columns:1fr 1fr; } }

        .cb-card{
            position:relative; display:block; padding:28px; border-radius:22px; text-align:center;
            color:#fff; text-decoration:none;
            background:rgba(255,255,255,.10); backdrop-filter: blur(12px);
            border:1px solid rgba(255,255,255,.25);
            box-shadow:0 12px 28px rgba(3,7,18,.35), inset 0 0 0 9999px rgba(255,255,255,.02);
            transition:transform .25s ease, box-shadow .25s ease, background .25s ease;
        }
        .cb-card:hover{ transform: translateY(-3px); box-shadow:0 18px 30px rgba(3,7,18,.45); }
        .cb-card h3{margin:6px 0 4px; font-size:22px; font-weight:700}
        .cb-card p{margin:0; color:rgba(255,255,255,.85); font-size:14px}
        .cb-icon{width:46px;height:46px;margin:0 auto 8px;border-radius:14px;background:rgba(255,255,255,.15);
                 display:grid;place-items:center;box-shadow:inset 0 0 0 1px rgba(255,255,255,.15)}
        .cb-icon svg{width:24px;height:24px;color:#fff}

        .cb-btn{
            display:inline-flex;align-items:center;gap:8px;
            margin-top:16px;padding:10px 18px;border-radius:999px;font-weight:800;color:#0b1220;
            background:linear-gradient(135deg,#c9e7ff,#d9d0ff);
            box-shadow:0 8px 18px rgba(2,6,23,.35);
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .cb-btn:hover{ transform:translateY(-1px); box-shadow:0 12px 22px rgba(2,6,23,.45); }
        .cb-btn span{opacity:.7}

        .cb-foot{text-align:center;margin:28px auto 6px;color:rgba(255,255,255,.8);font-size:12.5px}

        .cb-blob{position:absolute; border-radius:9999px; filter:blur(60px); opacity:.18; pointer-events:none}
        .cb-blob-a{width:520px;height:520px; left:-160px; top:-160px; background:#ffffff}
        .cb-blob-b{width:460px;height:460px; right:-120px; bottom:-160px; background:#ffffff}
    </style>
</x-app-layout>
