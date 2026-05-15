<?php
$totalUsers   = Database::fetchOne("SELECT COUNT(*) c FROM users WHERE role='user' AND status='active'")['c'] ?? 0;
$totalMatches = Database::fetchOne("SELECT COUNT(*) c FROM interests WHERE status='accepted'")['c'] ?? 0;
$plans        = Database::fetchAll("SELECT * FROM subscription_plans WHERE is_active=1 ORDER BY sort_order");
$stories      = Database::fetchAll("SELECT * FROM success_stories WHERE is_published=1 AND is_featured=1 ORDER BY married_on DESC LIMIT 3");
$featured     = Database::fetchAll("SELECT u.id,u.profile_id,u.name,u.gender,p.age,p.city,p.caste,p.occupation,p.is_highlighted,(SELECT ph.file_path FROM photos ph WHERE ph.user_id=u.id AND ph.is_primary=1 AND ph.is_approved='approved' LIMIT 1) AS photo FROM users u JOIN profiles p ON p.user_id=u.id WHERE u.status='active' AND p.admin_approved='approved' ORDER BY p.is_highlighted DESC, u.created_at DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars(APP_NAME) ?> – Find Your Perfect Match</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{
      --pink:#E91E8C;--pink-light:#FF6EBB;--pink-pale:#FDE8F4;
      --green:#1B8C5E;--green-light:#27C47F;--green-pale:#E5F7F0;
      --dark:#1A1028;--text:#3D3046;--muted:#8B7E97;
      --shadow-pink:0 8px 32px rgba(233,30,140,.18);
      --shadow-green:0 8px 32px rgba(27,140,94,.15);
      --radius:16px;--radius-lg:28px;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html{scroll-behavior:smooth}
    body{font-family:'DM Sans',sans-serif;color:var(--text);background:#fff;overflow-x:hidden}
    h1,h2,h3,h4,h5{font-family:'Playfair Display',serif}
    a{text-decoration:none;color:inherit}

    /* ════ BUTTONS ════ */
    .btn-pink{background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;border:none;border-radius:50px;padding:.65rem 1.8rem;font-weight:600;transition:transform .2s,box-shadow .2s;box-shadow:var(--shadow-pink)}
    .btn-pink:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(233,30,140,.28);color:#fff}
    .btn-green{background:linear-gradient(135deg,var(--green),var(--green-light));color:#fff;border:none;border-radius:50px;padding:.65rem 1.8rem;font-weight:600;transition:transform .2s,box-shadow .2s;box-shadow:var(--shadow-green)}
    .btn-green:hover{transform:translateY(-2px);color:#fff}
    .btn-outline-pink{border:2px solid var(--pink);color:var(--pink);border-radius:50px;padding:.6rem 1.6rem;font-weight:600;background:transparent;transition:all .2s}
    .btn-outline-pink:hover{background:var(--pink);color:#fff}
    .section-tag{display:inline-block;background:var(--pink-pale);color:var(--pink);font-size:.78rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:.35rem 1rem;border-radius:50px;margin-bottom:1rem}
    .section-tag.green{background:var(--green-pale);color:var(--green)}

    /* ════ DESKTOP NAVBAR ════ */
    .site-nav{
      background:rgba(255,255,255,.97);backdrop-filter:blur(14px);
      border-bottom:2px solid transparent;padding:.5rem 0;
      position:sticky;top:0;z-index:1000;
      transition:border-color .35s,box-shadow .35s;
    }
    .site-nav.scrolled{border-bottom-color:var(--pink);box-shadow:0 4px 22px rgba(233,30,140,.18)}
    .nav-brand{font-family:'Playfair Display',serif;font-size:1.4rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;white-space:nowrap}
    .nav-menu{display:flex;align-items:center;gap:.05rem;list-style:none;margin:0;padding:0}
    .nav-menu a{font-size:.84rem;font-weight:500;color:var(--text);padding:.3rem .72rem;border-radius:8px;transition:background .18s,color .18s;white-space:nowrap}
    .nav-menu a:hover,.nav-menu a.active{background:var(--pink-pale);color:var(--pink)}
    .lang-btn{font-size:.78rem;font-weight:700;color:var(--muted);cursor:pointer;border:1.5px solid #EDD7EA;border-radius:7px;padding:.26rem .6rem;transition:all .18s;background:transparent}
    .lang-btn:hover,.lang-btn.active{border-color:var(--pink);color:var(--pink);background:var(--pink-pale)}
    .nav-login-btn{border:1.5px solid var(--pink);color:var(--pink);border-radius:50px;padding:.28rem .95rem;font-weight:600;font-size:.8rem;background:transparent;cursor:pointer;transition:all .18s;white-space:nowrap}
    .nav-login-btn:hover{background:var(--pink);color:#fff}
    .nav-reg-btn{background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;border:none;border-radius:50px;padding:.3rem .95rem;font-weight:600;font-size:.8rem;cursor:pointer;transition:transform .18s,box-shadow .18s;box-shadow:0 3px 12px rgba(233,30,140,.25);white-space:nowrap}
    .nav-reg-btn:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(233,30,140,.32)}
    .nav-notif-btn{position:relative;background:var(--pink-pale);border:none;width:30px;height:30px;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--pink);font-size:.9rem;transition:background .18s;flex-shrink:0}
    .nav-notif-btn:hover{background:var(--pink);color:#fff}
    .notif-dot{position:absolute;top:3px;right:3px;width:7px;height:7px;border-radius:50%;background:#FF3B3B;border:1.5px solid #fff}

    /* Mobile nav: sticky logo only */
    .mob-nav-sticky{display:none;position:sticky;top:0;z-index:1000;background:rgba(255,255,255,.97);backdrop-filter:blur(14px);border-bottom:2px solid transparent;padding:.45rem 0;text-align:center;transition:border-color .35s,box-shadow .35s}
    .mob-nav-sticky.scrolled{border-bottom-color:var(--pink);box-shadow:0 3px 16px rgba(233,30,140,.15)}
    .mob-lang-bar{display:none;justify-content:center;gap:.5rem;padding:.35rem 0;background:#fff;border-bottom:1px solid #F5EDF9}
    @media(max-width:991.98px){.site-nav{display:none!important}.mob-nav-sticky{display:block}.mob-lang-bar{display:flex}}
    @media(min-width:992px){.mob-nav-sticky,.mob-lang-bar{display:none!important}}

    /* ════ HERO ════ */
    .hero{min-height:100vh;background:linear-gradient(135deg,#fff 0%,var(--pink-pale) 45%,var(--green-pale) 100%);position:relative;overflow:hidden;display:flex;align-items:center;padding:80px 0 60px}
    .hero::before{content:'';position:absolute;top:-120px;right:-120px;width:520px;height:520px;border-radius:50%;background:radial-gradient(circle,rgba(233,30,140,.12),transparent 70%);pointer-events:none}
    .hero::after{content:'';position:absolute;bottom:-100px;left:-80px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(27,140,94,.1),transparent 70%);pointer-events:none}
    .hero-title{font-size:clamp(2.4rem,5.5vw,4rem);font-weight:900;line-height:1.15;color:var(--dark)}
    .hero-title .accent-pink{color:var(--pink)}
    .hero-title .accent-green{color:var(--green)}
    .hero-sub{font-size:1.08rem;color:var(--muted);line-height:1.7;max-width:500px}
    .hero-stats{display:flex;gap:2rem;flex-wrap:wrap;margin-top:.5rem}
    .stat-item{text-align:center}
    .stat-num{font-family:'Playfair Display',serif;font-size:1.9rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
    .stat-label{font-size:.78rem;color:var(--muted);font-weight:500;text-transform:uppercase;letter-spacing:.06em}
    .float-heart{position:absolute;font-size:1.5rem;animation:floatUp 6s ease-in-out infinite;opacity:.18;pointer-events:none}
    @keyframes floatUp{0%{transform:translateY(0) rotate(0deg);opacity:.18}50%{transform:translateY(-40px) rotate(15deg);opacity:.28}100%{transform:translateY(0) rotate(0deg);opacity:.18}}
    @keyframes pulse-ring{0%{box-shadow:0 0 0 0 rgba(233,30,140,.4)}70%{box-shadow:0 0 0 16px rgba(233,30,140,0)}100%{box-shadow:0 0 0 0 rgba(233,30,140,0)}}
    .btn-pulse{animation:pulse-ring 2s ease-out infinite}

    /* Search Box */
    .search-box{background:#fff;border-radius:var(--radius-lg);box-shadow:0 16px 60px rgba(233,30,140,.14);padding:2rem;border:1px solid #F5E4F0;position:relative;z-index:2}
    .search-box h5{font-size:1.15rem;color:var(--dark);margin-bottom:1.2rem}
    .search-box .form-select{border-radius:12px;border:1.5px solid #EDD7EA;font-size:.9rem;padding:.65rem 1rem;color:var(--text);transition:border .2s,box-shadow .2s}
    .search-box .form-select:focus{border-color:var(--pink);box-shadow:0 0 0 3px rgba(233,30,140,.12)}
    .gender-tab{display:flex;border-radius:12px;overflow:hidden;border:1.5px solid #EDD7EA;margin-bottom:1.2rem}
    .gender-tab button{flex:1;border:none;background:transparent;padding:.55rem 0;font-weight:600;font-size:.9rem;transition:all .2s;cursor:pointer;color:var(--muted)}
    .gender-tab button.active{background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff}

    /* ════ HOW IT WORKS ════ */
    .hiw-section{background:#fff;padding:90px 0}
    .step-card{text-align:center;padding:2rem 1.5rem;border-radius:var(--radius);transition:transform .3s,box-shadow .3s;border:1.5px solid transparent}
    .step-card:hover{transform:translateY(-6px);box-shadow:0 16px 48px rgba(233,30,140,.12);border-color:var(--pink-pale)}
    .step-icon{width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.2rem;font-size:1.8rem}
    .step-icon.pink{background:var(--pink-pale);color:var(--pink)}
    .step-icon.green{background:var(--green-pale);color:var(--green)}
    .step-num{font-family:'Playfair Display',serif;font-size:.85rem;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.4rem}
    .step-title{font-size:1.1rem;font-weight:700;color:var(--dark);margin-bottom:.5rem}
    .step-desc{font-size:.9rem;color:var(--muted);line-height:1.6}

    /* ════ FEATURES ════ */
    .features-section{background:linear-gradient(135deg,var(--dark) 0%,#2A1845 100%);padding:90px 0;color:#fff;position:relative;overflow:hidden}
    .features-section::before{content:'';position:absolute;top:-60px;right:-60px;width:350px;height:350px;border-radius:50%;background:radial-gradient(circle,rgba(233,30,140,.2),transparent 70%)}
    .feature-item{display:flex;gap:1rem;align-items:flex-start;margin-bottom:1.8rem}
    .feature-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0}
    .feature-icon.pink{background:rgba(233,30,140,.2);color:var(--pink-light)}
    .feature-icon.green{background:rgba(27,140,94,.2);color:var(--green-light)}
    .feature-title{font-size:1rem;font-weight:700;color:#fff;margin-bottom:.3rem}
    .feature-desc{font-size:.87rem;color:rgba(255,255,255,.55);line-height:1.6}
    .feature-visual{background:rgba(255,255,255,.06);border-radius:var(--radius-lg);border:1px solid rgba(255,255,255,.1);padding:2.5rem;backdrop-filter:blur(8px)}
    .horoscope-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:4px;max-width:300px;margin:0 auto}
    .horoscope-cell{aspect-ratio:1;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:.7rem;color:rgba(255,255,255,.5);font-weight:600;transition:background .4s}
    .horoscope-cell.filled{background:rgba(233,30,140,.2);color:var(--pink-light);border-color:rgba(233,30,140,.3)}
    .horoscope-cell.center{background:transparent;border:none}

    /* ════ PROFILES ════ */
    .profiles-section{background:var(--pink-pale);padding:90px 0}
    .profile-card{background:#fff;border-radius:var(--radius-lg);overflow:hidden;box-shadow:0 4px 24px rgba(233,30,140,.08);transition:transform .3s,box-shadow .3s;height:100%}
    .profile-card:hover{transform:translateY(-6px);box-shadow:0 16px 48px rgba(233,30,140,.16)}
    .profile-img{height:200px;background:linear-gradient(135deg,var(--pink-pale),var(--green-pale));display:flex;align-items:center;justify-content:center;font-size:5rem;position:relative;overflow:hidden}
    .profile-img::after{content:'';position:absolute;inset:0;background:linear-gradient(to top,rgba(255,255,255,.8),transparent)}
    .profile-badge{position:absolute;top:12px;right:12px;background:var(--green);color:#fff;font-size:.7rem;font-weight:700;padding:.25rem .7rem;border-radius:50px;letter-spacing:.05em;z-index:1}
    .profile-body{padding:1.4rem}
    .profile-name{font-size:1.1rem;font-weight:700;color:var(--dark);margin-bottom:.3rem}
    .profile-meta{font-size:.82rem;color:var(--muted);margin-bottom:.8rem}
    .profile-meta span{margin-right:.8rem}
    .profile-tags{display:flex;flex-wrap:wrap;gap:.4rem;margin-bottom:1rem}
    .profile-tag{font-size:.75rem;font-weight:600;padding:.25rem .7rem;border-radius:50px;background:var(--green-pale);color:var(--green)}
    .profile-tag.pink{background:var(--pink-pale);color:var(--pink)}

    /* ════ TESTIMONIALS ════ */
    .testimonials-section{background:#fff;padding:90px 0}
    .testimonial-card{background:linear-gradient(135deg,var(--pink-pale),var(--green-pale));border-radius:var(--radius-lg);padding:2rem;position:relative;height:100%;border:1px solid rgba(233,30,140,.1);transition:transform .3s}
    .testimonial-card:hover{transform:translateY(-4px)}
    .testimonial-card::before{content:'"';font-family:'Playfair Display',serif;font-size:5rem;line-height:1;color:var(--pink);opacity:.2;position:absolute;top:.5rem;left:1.5rem}
    .testimonial-text{font-size:.93rem;color:var(--text);line-height:1.7;margin-bottom:1.2rem;position:relative;z-index:1}
    .testimonial-author{display:flex;align-items:center;gap:.8rem}
    .author-avatar{width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--pink),var(--green));display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0}
    .author-name{font-weight:700;font-size:.9rem;color:var(--dark)}
    .author-loc{font-size:.78rem;color:var(--muted)}
    .star-row{color:#F9A825;font-size:.85rem;margin-bottom:.8rem}

    /* ════ PLANS ════ */
    .plans-section{background:linear-gradient(135deg,var(--green-pale),var(--pink-pale));padding:90px 0}
    .plan-card{background:#fff;border-radius:var(--radius-lg);padding:2.2rem;box-shadow:0 4px 24px rgba(27,140,94,.07);border:2px solid transparent;transition:all .3s;height:100%}
    .plan-card.popular{border-color:var(--pink);box-shadow:var(--shadow-pink);position:relative}
    .plan-card:hover{transform:translateY(-4px);box-shadow:0 16px 48px rgba(233,30,140,.14)}
    .popular-badge{position:absolute;top:-14px;left:50%;transform:translateX(-50%);background:linear-gradient(135deg,var(--pink),var(--pink-light));color:#fff;font-size:.75rem;font-weight:700;padding:.3rem 1.2rem;border-radius:50px;letter-spacing:.08em;text-transform:uppercase}
    .plan-name{font-size:1.1rem;font-weight:700;color:var(--dark);margin-bottom:.5rem}
    .plan-price{font-family:'Playfair Display',serif;font-size:2.6rem;font-weight:900;background:linear-gradient(135deg,var(--pink),var(--green));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
    .plan-period{font-size:.82rem;color:var(--muted);font-weight:500}
    .plan-divider{border-top:1.5px solid var(--pink-pale);margin:1.2rem 0}
    .plan-feature{display:flex;align-items:center;gap:.6rem;font-size:.88rem;color:var(--text);margin-bottom:.7rem}
    .plan-feature i{color:var(--green);font-size:1rem}
    .plan-feature.off{color:var(--muted)}
    .plan-feature.off i{color:#CCC}

    /* ════ NEWSLETTER ════ */
    .newsletter-section{background:linear-gradient(135deg,var(--pink),#C2177A);padding:70px 0;color:#fff}
    .newsletter-section h2{color:#fff}
    .newsletter-input{border-radius:50px;border:none;padding:.75rem 1.5rem;font-size:.95rem;width:100%;outline:none;box-shadow:0 4px 16px rgba(0,0,0,.12)}

    /* ════ DESKTOP FOOTER ════ */
    .desktop-footer{background:var(--dark);padding:18px 0 14px}
    @media(max-width:767.98px){.desktop-footer{display:none}}
    .footer-nav-links{display:flex;justify-content:center;gap:0;flex-wrap:wrap;margin-bottom:.55rem}
    .footer-nav-links a{font-size:.82rem;font-weight:600;color:rgba(255,255,255,.5);padding:0 1.4rem;transition:color .18s}
    .footer-nav-links a:hover{color:var(--pink-light)}
    .footer-nav-links a+a{border-left:1px solid rgba(255,255,255,.12)}
    .footer-copy{font-size:.74rem;color:rgba(255,255,255,.28);text-align:center}

    /* ════ MOBILE BOTTOM NAV ════ */
    .mob-bottom-nav{display:none;position:fixed;bottom:0;left:0;right:0;background:rgba(255,255,255,.97);backdrop-filter:blur(16px);border-top:1px solid #F0E6F6;z-index:1100;justify-content:space-around;align-items:center;padding:.5rem 0 calc(.5rem + env(safe-area-inset-bottom));box-shadow:0 -4px 20px rgba(233,30,140,.1)}
    @media(max-width:767.98px){.mob-bottom-nav{display:flex}body{padding-bottom:72px}}
    .mob-nav-item{display:flex;flex-direction:column;align-items:center;gap:.14rem;flex:1;cursor:pointer;border-radius:14px;transition:all .22s;text-decoration:none!important;border:none;background:transparent}
    .mob-nav-icon{width:44px;height:44px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:1.35rem;transition:box-shadow .25s,transform .22s}
    .mob-nav-label{font-size:.6rem;font-weight:700;letter-spacing:.04em;transition:color .22s}
    .mob-nav-item.n-home .mob-nav-icon{background:linear-gradient(135deg,#FDE8F4,#FFB3D9);color:var(--pink)}.mob-nav-item.n-home .mob-nav-label{color:var(--pink)}
    .mob-nav-item.n-profiles .mob-nav-icon{background:linear-gradient(135deg,#E5F7F0,#B3EDD8);color:var(--green)}.mob-nav-item.n-profiles .mob-nav-label{color:var(--green)}
    .mob-nav-item.n-matches .mob-nav-icon{background:linear-gradient(135deg,#FFF3E0,#FFD599);color:#E65100}.mob-nav-item.n-matches .mob-nav-label{color:#E65100}
    .mob-nav-item.n-account .mob-nav-icon{background:linear-gradient(135deg,#EDE7F6,#C5B3E6);color:#6A1B9A}.mob-nav-item.n-account .mob-nav-label{color:#6A1B9A}
    .mob-nav-item.n-home:hover .mob-nav-icon,.mob-nav-item.n-home.active .mob-nav-icon{box-shadow:0 0 16px 6px rgba(233,30,140,.36);transform:translateY(-5px) scale(1.12)}
    .mob-nav-item.n-profiles:hover .mob-nav-icon,.mob-nav-item.n-profiles.active .mob-nav-icon{box-shadow:0 0 16px 6px rgba(27,140,94,.33);transform:translateY(-5px) scale(1.12)}
    .mob-nav-item.n-matches:hover .mob-nav-icon,.mob-nav-item.n-matches.active .mob-nav-icon{box-shadow:0 0 16px 6px rgba(230,81,0,.28);transform:translateY(-5px) scale(1.12)}
    .mob-nav-item.n-account:hover .mob-nav-icon,.mob-nav-item.n-account.active .mob-nav-icon{box-shadow:0 0 16px 6px rgba(106,27,154,.28);transform:translateY(-5px) scale(1.12)}

    /* Account drawer */
    .acct-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1200;backdrop-filter:blur(4px)}
    .acct-overlay.open{display:block}
    .acct-drawer{position:fixed;bottom:0;left:0;right:0;background:#fff;z-index:1210;border-radius:22px 22px 0 0;transform:translateY(100%);transition:transform .32s cubic-bezier(.4,0,.2,1);padding:0 0 calc(20px + env(safe-area-inset-bottom));box-shadow:0 -8px 40px rgba(233,30,140,.14)}
    .acct-drawer.open{transform:translateY(0)}
    .acct-drawer-handle{width:40px;height:4px;background:#EDD7EA;border-radius:4px;margin:12px auto 8px}
    .acct-drawer-header{display:flex;align-items:center;gap:.9rem;padding:.7rem 1.2rem 1rem;border-bottom:1px solid #F5EDF9}
    .acct-avatar{width:46px;height:46px;border-radius:50%;background:linear-gradient(135deg,var(--pink-pale),var(--green-pale));display:flex;align-items:center;justify-content:center;font-size:1.6rem;border:2px solid var(--pink-pale)}
    .acct-name{font-weight:700;font-size:.95rem;color:var(--dark)}
    .acct-id{font-size:.72rem;color:var(--muted)}
    .acct-menu-item{display:flex;align-items:center;gap:.85rem;padding:.7rem 1.4rem;font-size:.9rem;font-weight:500;color:var(--text);transition:background .15s;cursor:pointer;border:none;background:transparent;width:100%;text-align:left}
    .acct-menu-item:hover{background:var(--pink-pale)}
    .acct-menu-item i{font-size:1.05rem;width:22px;text-align:center}
    .acct-menu-item.danger{color:#C62828}
    .acct-menu-item.danger:hover{background:#FFF0F0}

    #backTop{position:fixed;bottom:88px;right:16px;width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,var(--pink),var(--green));color:#fff;border:none;display:flex;align-items:center;justify-content:center;font-size:1rem;cursor:pointer;opacity:0;transform:translateY(20px);transition:opacity .3s,transform .3s;z-index:999;box-shadow:0 4px 16px rgba(233,30,140,.3)}
    #backTop.visible{opacity:1;transform:translateY(0)}
    @media(min-width:768px){#backTop{bottom:24px;right:22px}}
    .reveal{opacity:0;transform:translateY(30px);transition:opacity .65s ease,transform .65s ease}
    .reveal.visible{opacity:1;transform:none}
</style>
</head>
<body>

<!-- DESKTOP NAVBAR -->
<nav class="site-nav" id="mainNav">
  <div class="container d-flex align-items-center gap-3">
    <a class="nav-brand me-2" href="<?= APP_URL ?>/">&#x1F48D; <?= APP_NAME ?></a>
    <ul class="nav-menu me-auto">
      <li><a href="<?= APP_URL ?>/" class="active">Home</a></li>
      <li><a href="#howitworks">How It Works</a></li>
      <li><a href="<?= APP_URL ?>/search">Profiles</a></li>
      <li><a href="#plans">Plans</a></li>
    </ul>
    <div class="d-flex align-items-center gap-2">
      <button class="lang-btn active" onclick="switchLang(this,'en')">EN</button>
      <button class="lang-btn" onclick="switchLang(this,'ta')">&#x0BA4;</button>
      <a href="<?= APP_URL ?>/login" class="nav-login-btn">Login</a>
      <a href="<?= APP_URL ?>/register" class="nav-reg-btn btn-pulse">Register Free</a>
    </div>
  </div>
</nav>
<div class="mob-nav-sticky" id="mobNav">
  <a class="nav-brand" href="<?= APP_URL ?>/" style="font-size:1.15rem">&#x1F48D; <?= APP_NAME ?></a>
</div>
<div class="mob-lang-bar">
  <button class="lang-btn active" onclick="switchLang(this,'en')">EN</button>
  <button class="lang-btn" onclick="switchLang(this,'ta')">&#x0BA4;</button>
</div>

<!-- HERO -->
<section class="hero" id="hero">
  <span class="float-heart" style="top:15%;left:8%;animation-delay:0s">&#x1F497;</span>
  <span class="float-heart" style="top:30%;left:2%;animation-delay:1.5s">&#x1F338;</span>
  <span class="float-heart" style="top:65%;right:5%;animation-delay:.8s">&#x1F495;</span>
  <span class="float-heart" style="top:10%;right:18%;animation-delay:2s">&#x1F33F;</span>
  <div class="container position-relative" style="z-index:2">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="section-tag">&#x1F48D; Trusted Tamil Matrimony</span>
        <h1 class="hero-title mb-3">Find Your <span class="accent-pink">Soulmate</span>,<br>Begin a <span class="accent-green">Beautiful</span> Journey</h1>
        <p class="hero-sub mb-4"><?= APP_NAME ?> connects Tamil brides and grooms with verified profiles, horoscope matching, and community trust.</p>
        <div class="d-flex flex-wrap gap-3 mb-4">
          <a href="<?= APP_URL ?>/register" class="btn btn-pink btn-lg btn-pulse"><i class="bi bi-person-plus me-2"></i>Create Free Profile</a>
          <a href="<?= APP_URL ?>/search" class="btn btn-green btn-lg"><i class="bi bi-search me-2"></i>Browse Profiles</a>
        </div>
        <div class="hero-stats">
          <div class="stat-item"><div class="stat-num" data-count="<?= $totalUsers ?>"><?= number_format($totalUsers) ?></div><div class="stat-label">Active Profiles</div></div>
          <div class="stat-item"><div class="stat-num" data-count="<?= $totalMatches ?>"><?= number_format($totalMatches) ?></div><div class="stat-label">Matches Made</div></div>
          <div class="stat-item"><div class="stat-num" data-count="98">98</div><div class="stat-label">% Satisfaction</div></div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="search-box reveal">
          <h5><i class="bi bi-heart-fill me-2" style="color:var(--pink)"></i>Find Your Match</h5>
          <div class="gender-tab" id="genderTab">
            <button class="active" onclick="setGender(this,'female')">&#x1F469; Bride</button>
            <button onclick="setGender(this,'male')">&#x1F468; Groom</button>
          </div>
          <form method="GET" action="<?= APP_URL ?>/search/results">
            <input type="hidden" name="gender" id="genderInput" value="female">
            <div class="row g-2 mb-3">
              <div class="col-6"><label class="form-label fw-semibold" style="font-size:.82rem">Age From</label>
                <select class="form-select" name="age_from">
                  <?php for($a=18;$a<=45;$a++): ?><option value="<?= $a ?>"<?= $a===21?' selected':'' ?>><?= $a ?> yrs</option><?php endfor; ?>
                </select>
              </div>
              <div class="col-6"><label class="form-label fw-semibold" style="font-size:.82rem">Age To</label>
                <select class="form-select" name="age_to">
                  <?php for($a=18;$a<=60;$a++): ?><option value="<?= $a ?>"<?= $a===35?' selected':'' ?>><?= $a ?> yrs</option><?php endfor; ?>
                </select>
              </div>
              <div class="col-6"><label class="form-label fw-semibold" style="font-size:.82rem">Religion</label>
                <select class="form-select" name="religion">
                  <option value="">Any</option><option value="Hindu">Hindu</option><option value="Christian">Christian</option><option value="Muslim">Muslim</option>
                </select>
              </div>
              <div class="col-6"><label class="form-label fw-semibold" style="font-size:.82rem">Caste</label>
                <select class="form-select" name="caste">
                  <option value="">Any Caste</option><option value="Iyer">Iyer</option><option value="Iyengar">Iyengar</option><option value="Vellalar">Vellalar</option><option value="Nadar">Nadar</option>
                </select>
              </div>
              <div class="col-12"><label class="form-label fw-semibold" style="font-size:.82rem">Location</label>
                <select class="form-select" name="city">
                  <option value="">Any Location</option><option value="Chennai">Chennai</option><option value="Coimbatore">Coimbatore</option><option value="Madurai">Madurai</option><option value="Trichy">Trichy</option><option value="Salem">Salem</option>
                </select>
              </div>
            </div>
            <button type="submit" class="btn btn-pink w-100 py-2"><i class="bi bi-search me-2"></i>Search Profiles</button>
          </form>
          <p class="text-center mt-3 mb-0" style="font-size:.8rem;color:var(--muted)">Already have an account? <a href="<?= APP_URL ?>/login" style="color:var(--pink);font-weight:600">Login here</a></p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="hiw-section" id="howitworks">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <span class="section-tag">Simple Steps</span>
      <h2 class="fs-1">How It Works</h2>
      <p class="text-muted mt-2" style="max-width:520px;margin:auto">Getting started is quick and free. Your perfect match is just a few steps away.</p>
    </div>
    <div class="row g-4">
      <div class="col-sm-6 col-lg-3 reveal"><div class="step-card"><div class="step-icon pink"><i class="bi bi-person-plus-fill"></i></div><div class="step-num">Step 01</div><div class="step-title">Register Free</div><div class="step-desc">Create your free profile with basic info. Mobile &amp; email OTP verification for security.</div></div></div>
      <div class="col-sm-6 col-lg-3 reveal"><div class="step-card"><div class="step-icon green"><i class="bi bi-card-list"></i></div><div class="step-num">Step 02</div><div class="step-title">Complete Profile</div><div class="step-desc">Add horoscope, photos, education, profession and family details for better matches.</div></div></div>
      <div class="col-sm-6 col-lg-3 reveal"><div class="step-card"><div class="step-icon pink"><i class="bi bi-search-heart"></i></div><div class="step-num">Step 03</div><div class="step-title">Search &amp; Match</div><div class="step-desc">Use advanced filters or get daily recommended matches tailored for you.</div></div></div>
      <div class="col-sm-6 col-lg-3 reveal"><div class="step-card"><div class="step-icon green"><i class="bi bi-chat-heart-fill"></i></div><div class="step-num">Step 04</div><div class="step-title">Connect &amp; Marry</div><div class="step-desc">Send interest, chat with accepted connections and begin your beautiful journey.</div></div></div>
    </div>
  </div>
</section>

<!-- FEATURES -->
<section class="features-section" id="features">
  <div class="container position-relative" style="z-index:2">
    <div class="row g-5 align-items-center">
      <div class="col-lg-6 reveal">
        <span class="section-tag green">Why <?= APP_NAME ?></span>
        <h2 class="fs-1 text-white mb-4">Everything You Need to Find <span style="color:var(--pink-light)">Your Match</span></h2>
        <div class="feature-item"><div class="feature-icon pink"><i class="bi bi-shield-check-fill"></i></div><div><div class="feature-title">Verified Profiles</div><div class="feature-desc">Every profile verified through mobile OTP and manual admin review for authenticity.</div></div></div>
        <div class="feature-item"><div class="feature-icon green"><i class="bi bi-stars"></i></div><div><div class="feature-title">Tamil &#x1E97;&#xE000;&#xE001;&#xE002; Horoscope Matching</div><div class="feature-desc">South Indian 12-house chart with Chevvai, Rahu &amp; Kala Sarpa dosham support.</div></div></div>
        <div class="feature-item"><div class="feature-icon pink"><i class="bi bi-incognito"></i></div><div><div class="feature-title">Privacy Control</div><div class="feature-desc">Control who sees your photos, contact details and profile visibility.</div></div></div>
        <div class="feature-item"><div class="feature-icon green"><i class="bi bi-globe2"></i></div><div><div class="feature-title">Multi-Language Support</div><div class="feature-desc">Browse in Tamil or English — your comfort, your choice.</div></div></div>
        <div class="feature-item"><div class="feature-icon pink"><i class="bi bi-phone-fill"></i></div><div><div class="feature-title">Mobile Responsive</div><div class="feature-desc">Seamlessly use on any device — phone, tablet or desktop.</div></div></div>
      </div>
      <div class="col-lg-6 reveal">
        <div class="feature-visual">
          <div class="text-center mb-3"><span style="font-size:.82rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.1em">Tamil Jathagam Chart</span></div>
          <div class="horoscope-grid" id="horoGrid">
            <div class="horoscope-cell filled">&#x0B9A;&#x0BC2;&#x0BB0;&#x0BBF;</div><div class="horoscope-cell filled">&#x0B9A;&#x0BC6;&#x0BB5;&#x0BCD;</div><div class="horoscope-cell">&#x0B95;&#x0BC1;&#x0BB0;&#x0BC1;</div><div class="horoscope-cell filled">&#x0B9A;&#x0BA9;&#x0BBF;</div>
            <div class="horoscope-cell">&#x0BB0;&#x0BBE;&#x0B95;&#x0BC1;</div><div class="horoscope-cell center"></div><div class="horoscope-cell center"></div><div class="horoscope-cell filled">&#x0B9A;&#x0BC1;&#x0B95;&#x0BCD;</div>
            <div class="horoscope-cell filled">&#x0BAA;&#x0BC1;&#x0BA4;</div><div class="horoscope-cell center"></div><div class="horoscope-cell center"></div><div class="horoscope-cell">&#x0B95;&#x0BC7;&#x0BA4;&#x0BC1;</div>
            <div class="horoscope-cell">&#x0BB2;&#x0B95;&#x0BCD;</div><div class="horoscope-cell filled">&#x0B9A;&#x0BA8;&#x0BCD;</div><div class="horoscope-cell filled">&#x0BAE;&#x0B99;&#x0BCD;</div><div class="horoscope-cell">&#x2014;</div>
          </div>
          <p class="text-center mt-3 mb-0" style="font-size:.78rem;color:rgba(255,255,255,.35)">Interactive horoscope entry — fill planets house by house</p>
          <div class="row g-2 mt-3">
            <div class="col-6"><div style="background:rgba(255,255,255,.06);border-radius:12px;padding:1rem;border:1px solid rgba(255,255,255,.1)"><div style="font-size:.75rem;color:rgba(255,255,255,.4);text-transform:uppercase">Rasi</div><div style="font-size:1rem;font-weight:700;color:#fff;margin-top:.2rem">&#x0BAE;&#x0BBF;&#x0BA4;&#x0BC1;&#x0BA9;&#x0BAE;&#x0BCD;</div></div></div>
            <div class="col-6"><div style="background:rgba(255,255,255,.06);border-radius:12px;padding:1rem;border:1px solid rgba(255,255,255,.1)"><div style="font-size:.75rem;color:rgba(255,255,255,.4);text-transform:uppercase">Nakshatra</div><div style="font-size:1rem;font-weight:700;color:#fff;margin-top:.2rem">&#x0BA4;&#x0BBF;&#x0BB0;&#x0BC1;&#x0BB5;&#x0BBE;&#x0BA4;&#x0BBF;&#x0BB0;&#x0BC8;</div></div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PROFILES -->
<section class="profiles-section" id="profiles">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <span class="section-tag">Today's Matches</span>
      <h2 class="fs-1">Recommended Profiles</h2>
      <p class="text-muted mt-2" style="max-width:480px;margin:auto">Daily curated profiles matched based on your preferences and community.</p>
    </div>
    <div class="d-flex justify-content-center flex-wrap gap-2 mb-4 reveal">
      <button class="btn btn-pink btn-sm">All</button>
      <a href="<?= APP_URL ?>/search/results?caste=Iyer" class="btn btn-outline-pink btn-sm">Iyer</a>
      <a href="<?= APP_URL ?>/search/results?caste=Iyengar" class="btn btn-outline-pink btn-sm">Iyengar</a>
      <a href="<?= APP_URL ?>/search/results?caste=Vellalar" class="btn btn-outline-pink btn-sm">Vellalar</a>
      <a href="<?= APP_URL ?>/search/results?caste=Nadar" class="btn btn-outline-pink btn-sm">Nadar</a>
    </div>
    <div class="row g-4">
      <?php if (!empty($featured)): ?>
      <?php foreach ($featured as $fp): ?>
      <div class="col-sm-6 col-lg-3 reveal"><div class="profile-card">
        <div class="profile-img" style="position:relative">
          <?php if (!empty($fp['photo'])): ?><img src="<?= APP_URL ?>/storage/<?= htmlspecialchars($fp['photo']) ?>" style="width:100%;height:100%;object-fit:cover;position:absolute;inset:0" alt=""><?php else: ?><?= $fp['gender']==="female"?"&#x1F469;":"&#x1F468;" ?><?php endif; ?>
          <span class="profile-badge" style="<?= !empty($fp['is_highlighted'])?"background:var(--pink)":"" ?>"><?= !empty($fp['is_highlighted'])?"&#x2B50; Premium":"&#x2714; Verified" ?></span>
        </div>
        <div class="profile-body">
          <div class="profile-name"><?= htmlspecialchars($fp['name']) ?></div>
          <div class="profile-meta"><span><i class="bi bi-calendar3"></i> <?= $fp['age'] ?? '—' ?> yrs</span> <span><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($fp['city'] ?? '—') ?></span></div>
          <div class="profile-tags"><?php if(!empty($fp['caste'])): ?><span class="profile-tag"><?= htmlspecialchars($fp['caste']) ?></span><?php endif; ?><?php if(!empty($fp['occupation'])): ?><span class="profile-tag pink"><?= htmlspecialchars($fp['occupation']) ?></span><?php endif; ?></div>
          <a href="<?= APP_URL ?>/register" class="btn btn-pink w-100 btn-sm"><i class="bi bi-heart me-1"></i>View Profile</a>
        </div>
      </div></div>
      <?php endforeach; ?>
      <?php else: ?>
      <div class="col-sm-6 col-lg-3 reveal"><div class="profile-card"><div class="profile-img">&#x1F469;<span class="profile-badge">&#x2714; Verified</span></div><div class="profile-body"><div class="profile-name">Priya R.</div><div class="profile-meta"><span><i class="bi bi-calendar3"></i> 26 yrs</span><span><i class="bi bi-geo-alt"></i> Chennai</span></div><div class="profile-tags"><span class="profile-tag">Iyer</span><span class="profile-tag pink">Software Engineer</span></div><a href="<?= APP_URL ?>/register" class="btn btn-pink w-100 btn-sm"><i class="bi bi-heart me-1"></i>View Profile</a></div></div></div>
      <div class="col-sm-6 col-lg-3 reveal"><div class="profile-card"><div class="profile-img" style="background:linear-gradient(135deg,var(--green-pale),var(--pink-pale))">&#x1F468;<span class="profile-badge">&#x2714; Verified</span></div><div class="profile-body"><div class="profile-name">Karthik M.</div><div class="profile-meta"><span><i class="bi bi-calendar3"></i> 29 yrs</span><span><i class="bi bi-geo-alt"></i> Coimbatore</span></div><div class="profile-tags"><span class="profile-tag">Vellalar</span><span class="profile-tag pink">Doctor</span></div><a href="<?= APP_URL ?>/register" class="btn btn-pink w-100 btn-sm"><i class="bi bi-heart me-1"></i>View Profile</a></div></div></div>
      <div class="col-sm-6 col-lg-3 reveal"><div class="profile-card"><div class="profile-img" style="background:linear-gradient(135deg,#FDE8F4,#E5F7F0)">&#x1F469;<span class="profile-badge" style="background:var(--pink)">&#x2B50; Premium</span></div><div class="profile-body"><div class="profile-name">Deepa S.</div><div class="profile-meta"><span><i class="bi bi-calendar3"></i> 24 yrs</span><span><i class="bi bi-geo-alt"></i> Madurai</span></div><div class="profile-tags"><span class="profile-tag">Iyengar</span><span class="profile-tag pink">Teacher</span></div><a href="<?= APP_URL ?>/register" class="btn btn-pink w-100 btn-sm"><i class="bi bi-heart me-1"></i>View Profile</a></div></div></div>
      <div class="col-sm-6 col-lg-3 reveal"><div class="profile-card"><div class="profile-img" style="background:linear-gradient(135deg,#E5F7F0,#FDE8F4)">&#x1F468;<span class="profile-badge">&#x2714; Verified</span></div><div class="profile-body"><div class="profile-name">Rajesh K.</div><div class="profile-meta"><span><i class="bi bi-calendar3"></i> 31 yrs</span><span><i class="bi bi-geo-alt"></i> Trichy</span></div><div class="profile-tags"><span class="profile-tag">Nadar</span><span class="profile-tag pink">CA</span></div><a href="<?= APP_URL ?>/register" class="btn btn-pink w-100 btn-sm"><i class="bi bi-heart me-1"></i>View Profile</a></div></div></div>
      <?php endif; ?>
    </div>
    <div class="text-center mt-5 reveal"><a href="<?= APP_URL ?>/register" class="btn btn-green btn-lg px-5">View All Profiles <i class="bi bi-arrow-right ms-2"></i></a></div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials-section" id="stories">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <span class="section-tag green">Success Stories</span>
      <h2 class="fs-1">Couples Who Found Love Here</h2>
      <p class="text-muted mt-2" style="max-width:480px;margin:auto">Thousands of marriages and counting. Their stories inspire us every day.</p>
    </div>
    <div class="row g-4">
      <?php if (!empty($stories)): ?>
        <?php foreach ($stories as $s): ?>
        <div class="col-md-4 reveal"><div class="testimonial-card"><div class="star-row">&#x2605;&#x2605;&#x2605;&#x2605;&#x2605;</div>
          <?php if (!empty($s['story'])): ?><p class="testimonial-text"><?= htmlspecialchars(mb_substr($s['story'],0,160)) ?>...</p><?php endif; ?>
          <div class="testimonial-author"><div class="author-avatar">&#x1F491;</div><div>
            <div class="author-name"><?= htmlspecialchars($s['bride_name']) ?> &amp; <?= htmlspecialchars($s['groom_name']) ?></div>
            <?php if ($s['married_on']): ?><div class="author-loc"><i class="bi bi-calendar me-1"></i><?= date('F Y',strtotime($s['married_on'])) ?></div><?php endif; ?>
          </div></div>
        </div></div>
        <?php endforeach; ?>
      <?php else: ?>
      <div class="col-md-4 reveal"><div class="testimonial-card"><div class="star-row">&#x2605;&#x2605;&#x2605;&#x2605;&#x2605;</div><p class="testimonial-text">We found each other through <?= APP_NAME ?> within 3 months. The horoscope matching feature helped our parents agree quickly. Forever grateful!</p><div class="testimonial-author"><div class="author-avatar">&#x1F46B;</div><div><div class="author-name">Anand &amp; Kavitha</div><div class="author-loc"><i class="bi bi-geo-alt-fill me-1"></i>Chennai &#x2192; Married 2023</div></div></div></div></div>
      <div class="col-md-4 reveal"><div class="testimonial-card"><div class="star-row">&#x2605;&#x2605;&#x2605;&#x2605;&#x2605;</div><p class="testimonial-text">The caste-based search made it easy. Profile verification gave our family confidence. Blessed to have found this platform for our daughter.</p><div class="testimonial-author"><div class="author-avatar">&#x1F468;&#x200D;&#x1F469;&#x200D;&#x1F467;</div><div><div class="author-name">Suresh (Parent)</div><div class="author-loc"><i class="bi bi-geo-alt-fill me-1"></i>Coimbatore &#x2192; 2024</div></div></div></div></div>
      <div class="col-md-4 reveal"><div class="testimonial-card"><div class="star-row">&#x2605;&#x2605;&#x2605;&#x2605;&#x2605;</div><p class="testimonial-text">Simple, Tamil-friendly interface. I loved that I could fill my horoscope in Tamil. Chat feature made communication smooth before our first meeting!</p><div class="testimonial-author"><div class="author-avatar">&#x1F491;</div><div><div class="author-name">Meena &amp; Vijay</div><div class="author-loc"><i class="bi bi-geo-alt-fill me-1"></i>Madurai &#x2192; Engaged 2024</div></div></div></div></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- PLANS -->
<section class="plans-section" id="plans">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <span class="section-tag">Membership Plans</span>
      <h2 class="fs-1">Choose Your Plan</h2>
      <p class="text-muted mt-2" style="max-width:480px;margin:auto">Start free. Upgrade anytime for unlimited access, priority listing and more.</p>
    </div>
    <div class="row g-4 justify-content-center">
      <?php foreach ($plans as $pl): $isPop=$pl['code']==="GOLD"; ?>
      <div class="col-md-6 col-lg-3 reveal">
        <div class="plan-card <?= $isPop?'popular':'' ?>">
          <?php if ($isPop): ?><div class="popular-badge">Most Popular</div><?php endif; ?>
          <div class="plan-name"><?= htmlspecialchars($pl['name']) ?></div>
          <div class="plan-price"><?= $pl['price']>0?'&#x20B9;'.number_format($pl['price'],0):'&#x20B9;0' ?></div>
          <div class="plan-period"><?= $pl['price']>0?'for '.$pl['duration_days'].' days':'Forever free' ?></div>
          <div class="plan-divider"></div>
          <div class="plan-feature"><i class="bi bi-check-circle-fill"></i><?= $pl['interests_limit']<0?'Unlimited Interests':$pl['interests_limit'].' Interests' ?></div>
          <?php if ($pl['chat_limit']): ?><div class="plan-feature"><i class="bi bi-check-circle-fill"></i>Unlimited Chat</div><?php else: ?><div class="plan-feature off"><i class="bi bi-x-circle-fill"></i>Chat Locked</div><?php endif; ?>
          <?php if ($pl['contact_view']): ?><div class="plan-feature"><i class="bi bi-check-circle-fill"></i>Contact Details</div><?php else: ?><div class="plan-feature off"><i class="bi bi-x-circle-fill"></i>No Contact View</div><?php endif; ?>
          <?php if ($pl['advanced_search']): ?><div class="plan-feature"><i class="bi bi-check-circle-fill"></i>Advanced Filters</div><?php else: ?><div class="plan-feature off"><i class="bi bi-x-circle-fill"></i>Basic Search</div><?php endif; ?>
          <?php if ($pl['highlight']): ?><div class="plan-feature"><i class="bi bi-check-circle-fill"></i>Profile Highlighted</div><?php else: ?><div class="plan-feature off"><i class="bi bi-x-circle-fill"></i>No Highlight</div><?php endif; ?>
          <div class="mt-3"><a href="<?= APP_URL ?>/register" class="<?= $isPop?'btn btn-pink w-100':'btn btn-outline-pink w-100' ?>"><?= $pl['price']>0?'Get '.$pl['name']:'Start Free' ?></a></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="newsletter-section">
  <div class="container text-center">
    <h2 class="mb-2">Your Perfect Match is Waiting &#x1F48D;</h2>
    <p style="color:rgba(255,255,255,.8);max-width:500px;margin:.5rem auto 2rem;font-size:1rem">Join <?= number_format($totalUsers) ?>+ members who found love through <?= APP_NAME ?>.</p>
    <div class="row justify-content-center g-3">
      <div class="col-12 col-md-5"><input type="email" id="nlEmail" class="newsletter-input" placeholder="Enter your email address"></div>
      <div class="col-auto"><a href="<?= APP_URL ?>/register" class="btn btn-green btn-lg"><i class="bi bi-heart me-2"></i>Register Free</a></div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<div class="desktop-footer">
  <div class="footer-nav-links">
    <a href="<?= APP_URL ?>/">Home</a><a href="<?= APP_URL ?>/register">Register</a><a href="<?= APP_URL ?>/login">Login</a><a href="<?= APP_URL ?>/search">Search Profiles</a><a href="#plans">Plans</a><a href="#">Privacy</a><a href="#">Terms</a>
  </div>
  <div class="footer-copy">&#x00A9; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved. Made with &#x2764;&#xFE0F; in Tamil Nadu.</div>
</div>

<!-- MOBILE BOTTOM NAV -->
<nav class="mob-bottom-nav">
  <a href="<?= APP_URL ?>/" class="mob-nav-item n-home active" style="text-decoration:none"><div class="mob-nav-icon"><i class="bi bi-house-fill"></i></div><span class="mob-nav-label">Home</span></a>
  <a href="<?= APP_URL ?>/search" class="mob-nav-item n-profiles" style="text-decoration:none"><div class="mob-nav-icon"><i class="bi bi-search-heart"></i></div><span class="mob-nav-label">Profiles</span></a>
  <a href="<?= APP_URL ?>/register" class="mob-nav-item n-matches" style="text-decoration:none"><div class="mob-nav-icon"><i class="bi bi-person-plus-fill"></i></div><span class="mob-nav-label">Register</span></a>
  <a href="<?= APP_URL ?>/login" class="mob-nav-item n-account" style="text-decoration:none"><div class="mob-nav-icon"><i class="bi bi-person-circle"></i></div><span class="mob-nav-label">Login</span></a>
</nav>

<button id="backTop" onclick="window.scrollTo({top:0,behavior:'smooth'})"><i class="bi bi-arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function setGender(btn,val){document.querySelectorAll('#genderTab button').forEach(b=>b.classList.remove('active'));btn.classList.add('active');document.getElementById('genderInput').value=val;}
function switchLang(btn,l){document.querySelectorAll('.lang-btn').forEach(b=>b.classList.remove('active'));btn.classList.add('active');}
const mainNav=document.getElementById('mainNav'),mobNav=document.getElementById('mobNav');
window.addEventListener('scroll',()=>{const s=window.scrollY>40;mainNav?.classList.toggle('scrolled',s);mobNav?.classList.toggle('scrolled',s);document.getElementById('backTop').classList.toggle('visible',window.scrollY>300);},{passive:true});
const obs=new IntersectionObserver((es)=>{es.forEach((e,i)=>{if(e.isIntersecting){setTimeout(()=>e.target.classList.add('visible'),i*80);obs.unobserve(e.target)}});},{threshold:.12});
document.querySelectorAll('.reveal').forEach(el=>obs.observe(el));
function animateCounter(el,target){let c=0;const step=Math.max(1,Math.ceil(target/60));const t=setInterval(()=>{c=Math.min(c+step,target);el.textContent=c.toLocaleString('en-IN')+(el.dataset.count=='98'?'%':'+');if(c>=target)clearInterval(t);},25);}
const cObs=new IntersectionObserver((es)=>{es.forEach(e=>{if(e.isIntersecting){animateCounter(e.target,parseInt(e.target.dataset.count||0));cObs.unobserve(e.target);}});},{threshold:.5});
document.querySelectorAll('[data-count]').forEach(el=>cObs.observe(el));
const cells=document.querySelectorAll('.horoscope-cell');
setInterval(()=>{const r=Math.floor(Math.random()*cells.length);if(!cells[r]?.classList.contains('center'))cells[r]?.classList.toggle('filled');},2200);
</script>
</body>
</html>
