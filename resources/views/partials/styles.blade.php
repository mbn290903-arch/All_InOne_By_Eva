<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    wine: '#76243D',
                    'wine-dark': '#5D1830',
                    paper: '#F7F5F3',
                    line: '#EAE5E2',
                    muted: '#8B8588'
                },
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui']
                },
                boxShadow: {
                    soft: '0 1px 3px rgba(40, 20, 25, .04)'
                }
            }
        }
    }
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    * { box-sizing: border-box; }
    body { font-family: Inter, sans-serif; }
    .nav-link { display:flex; align-items:center; gap:12px; padding:9px 12px; border-radius:8px; color:#716b70; font-size:15px; font-weight:500; }
    .nav-link.active { background:#f6e9ed; color:#76243d; font-weight:600; }
    .nav-link svg { width:15px; height:15px; stroke-width:1.7; }
    .nav-label { color:#aaa2a5; font-size:9px; font-weight:700; letter-spacing:.12em; margin:17px 12px 7px; }
        .menu-btn { display: none; }
    .sidebar-overlay { display: none; }

    @media (max-width: 900px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform .2s ease;
            z-index: 50 !important;
        }
        .sidebar.ouverte {
            transform: translateX(0);
            box-shadow: 0 0 30px rgba(0, 0, 0, .15);
        }
        .sidebar-overlay.visible {
            display: block;
            position: fixed;
            inset: 0;
            z-index: 40;
            background: rgba(0, 0, 0, .4);
        }
        .menu-btn { display: grid; }
        .main-content { margin-left: 0 !important; }
        .desktop-search { display: none !important; }
    }
</style>