<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DukaFlow - Smart POS & Inventory</title>
    <meta name="description" content="Manage sales, stock, expenses and profits easily. Built for Tanzanian businesses.">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --dark: #0f172a;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: #1e293b;
        }

        .navbar {
            background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,.08);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.45rem;
            color: var(--dark) !important;
        }

        .navbar-brand i { color: var(--primary); }

        .hero {
            background: linear-gradient(135deg, #eff6ff 0%, #f8fafc 100%);
            padding: 4.5rem 0 4rem;
        }

        .hero h1 {
            font-weight: 800;
            font-size: 2.6rem;
            line-height: 1.2;
            color: var(--dark);
        }

        .hero .lead {
            font-size: 1.15rem;
            color: #475569;
            max-width: 520px;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            background: #eff6ff;
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 1rem;
        }

        .feature-card {
            border: none;
            border-radius: 1rem;
            transition: all 0.25s ease;
            height: 100%;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,.08);
        }

        .section-title { font-weight: 800; color: var(--dark); }

        /* Mockup */
        .mockup-window {
            background: #1e293b;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.25);
        }
        .mockup-header {
            background: #334155;
            padding: 0.6rem 1rem;
            display: flex;
            gap: 6px;
        }
        .mockup-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .mockup-body {
            background: #f8fafc;
            padding: 1rem;
            min-height: 280px;
        }
        .mock-card {
            background: #fff;
            border-radius: 8px;
            padding: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }

        /* Pricing */
        .price-card {
            border: none;
            border-radius: 1.25rem;
            transition: all 0.25s;
        }
        .price-card.popular {
            border: 2px solid var(--primary);
            transform: scale(1.03);
        }
        .price-card:hover {
            box-shadow: 0 15px 30px rgba(0,0,0,.1);
        }

        /* Team */
        #team {
            background-color: mediumslateblue !important;
        }

        /* Features */
        #features {
            background-color: mediumslateblue !important;
        }

        /* WhatsApp floating */
        .whatsapp-float {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 58px;
            height: 58px;
            background: #25d366;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.45);
            z-index: 1000;
            transition: transform 0.2s;
            text-decoration: none;
        }
        .whatsapp-float:hover {
            transform: scale(1.08);
            color: #fff;
        }

        .lang-btn {
            font-size: 0.8rem;
            padding: 0.25rem 0.6rem;
        }

        .cta-section {
            background: var(--dark);
            color: #fff;
            border-radius: 1.5rem;
            padding: 3.2rem 2rem;
        }

        .footer {
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            padding: 2.5rem 0;
        }

        /* Language content */
        .lang-sw { display: none; }
        body.sw .lang-en { display: none; }
        body.sw .lang-sw { display: block; }
        body.sw .lang-sw-inline { display: inline; }
        .lang-sw-inline { display: none; }
        body.sw .lang-en-inline { display: none; }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .price-card.popular { transform: none; }
        }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top py-2">
        <div class="container">
            <a class="navbar-brand mb-1" href="/">
                <i class="bi bi-shop-window me-1"></i> DukaFlow
            </a>

            <div class="d-flex align-items-center gap-2 pb-1">
                <!-- Language Toggle -->
                <div class="btn-group btn-group-sm me-1">
                    <button type="button" class="btn btn-outline-secondary lang-btn" onclick="setLang('en')" id="btn-en">EN</button>
                    <button type="button" class="btn btn-outline-secondary lang-btn" onclick="setLang('sw')" id="btn-sw">SW</button>
                </div>

                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">
                        <span class="lang-en">Login</span>
                        <span class="lang-sw lang-sw-inline">Ingia</span>
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                        <span class="lang-en">Get Started</span>
                        <span class="lang-sw lang-sw-inline">Anza Sasa</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center gy-5">
                <div class="col-lg-6">
                    <h1 class="mb-3">
                        <span class="lang-en">Stop writing sales in notebooks.</span>
                        <span class="lang-sw">Usiandike tena mauzo yako kwenye daftari.</span>
                    </h1>
                    <p class="lead mb-4">
                        <span class="lang-en">
                            DukaFlow helps you manage sales, stock, expenses and profits — 
                            all from your phone or computer. Built for Tanzanian businesses.
                        </span>
                        <span class="lang-sw">
                            DukaFlow inakusaidia kusimamia mauzo, ghala, matumizi na faida — 
                            moja kwa moja kwenye simu au computer. Inaendana kabisa na biashara za Tanzania.
                        </span>
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            <span class="lang-en">Start Free</span>
                            <span class="lang-sw lang-sw-inline">Anza Bure</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                            <span class="lang-en">Login</span>
                            <span class="lang-sw lang-sw-inline">Ingia</span>
                        </a>
                    </div>
                    <p class="mt-3 small text-muted">
                        <span class="lang-en">No credit card required to get started • Ready in minutes</span>
                        <span class="lang-sw">Hakuna kadi ya kufungulia inayohitajika • Anza mara moja</span>
                    </p>
                </div>

                <!-- Screenshot Mockup -->
                <div class="col-lg-6">
                    <div class="mockup-window">
                        <div class="mockup-header">
                            <div class="mockup-dot" style="background:#ef4444"></div>
                            <div class="mockup-dot" style="background:#eab308"></div>
                            <div class="mockup-dot" style="background:#22c55e"></div>
                        </div>
                        <div class="mockup-body">
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <div class="mock-card text-center">
                                        <div class="small text-muted">Sales Today</div>
                                        <div class="fw-bold text-primary">TZS 1.25M</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mock-card text-center">
                                        <div class="small text-muted">Products</div>
                                        <div class="fw-bold">348</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mock-card text-center">
                                        <div class="small text-muted">Low Stock</div>
                                        <div class="fw-bold text-warning">7</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mock-card mb-2">
                                <div class="d-flex justify-content-between small">
                                    <span>POS / New Sale</span>
                                    <span class="badge bg-primary">Active</span>
                                </div>
                                <div class="progress mt-2" style="height:6px">
                                    <div class="progress-bar" style="width:70%"></div>
                                </div>
                            </div>
                            <div class="mock-card">
                                <div class="small text-muted mb-1">Recent Sales</div>
                                <div class="d-flex justify-content-between small">
                                    <span>Sugar 2kg × 3</span>
                                    <span class="fw-semibold">12,000</span>
                                </div>
                                <div class="d-flex justify-content-between small mt-1">
                                    <span>Cooking Oil 1L</span>
                                    <span class="fw-semibold">5,500</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="py-5" id="features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title mb-2">
                    <span class="lang-en text-white">Everything you need to run your shop</span>
                    <span class="lang-sw text-white">Kila kitu unachohitaji kuendesha duka lako</span>
                </h2>
                <p class="text-muted col-lg-7 mx-auto">
                    <span class="lang-en text-white">From the counter to reports — full control of your business.</span>
                    <span class="lang-sw text-white">Kutoka mapokezi hadi ripoti — udhibiti kamili wa biashara yako.</span>
                </p>
            </div>

            <div class="row g-4">
                <!-- Feature cards remain mostly visual, short text in both languages -->
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body p-4">
                            <div class="feature-icon"><i class="bi bi-cart-check"></i></div>
                            <h5 class="fw-bold">
                                <span class="lang-en">Fast POS</span>
                                <span class="lang-sw">Mfumo wa mauzo wa haraka</span>
                            </h5>
                            <p class="text-muted mb-0 small">
                                <span class="lang-en">Sell quickly with barcode, Cash, M-Pesa & automatic stock update.</span>
                                <span class="lang-sw">Uza haraka kwa barcode, Cash, M-Pesa na hesabu za ghalani zinajisasisha zenyewe.</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body p-4">
                            <div class="feature-icon"><i class="bi bi-box-seam"></i></div>
                            <h5 class="fw-bold">
                                <span class="lang-en">Stock Management</span>
                                <span class="lang-sw">Usimamizi wa Bidhaa</span>
                            </h5>
                            <p class="text-muted mb-0 small">
                                <span class="lang-en">Know exactly what you have. Get low-stock alerts instantly.</span>
                                <span class="lang-sw">Tambua bidhaa zilizopo. Pata tahadhari ya bidhaa zinazokaribia kuisha.</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body p-4">
                            <div class="feature-icon"><i class="bi bi-graph-up-arrow"></i></div>
                            <h5 class="fw-bold">
                                <span class="lang-en">Sales & Profit Reports</span>
                                <span class="lang-sw">Ripoti za Mauzo na Faida</span>
                            </h5>
                            <p class="text-muted mb-0 small">
                                <span class="lang-en">Daily to yearly reports. See real profit and best sellers.</span>
                                <span class="lang-sw">Ripoti za kila siku hadi mwaka. Ona faida halisi na bidhaa zinazouzika kwa wingi.</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body p-4">
                            <div class="feature-icon"><i class="bi bi-people"></i></div>
                            <h5 class="fw-bold">
                                <span class="lang-en">Customers & Credit</span>
                                <span class="lang-sw">Wateja na Madeni</span>
                            </h5>
                            <p class="text-muted mb-0 small">
                                <span class="lang-en">Track who owes you. Manage credit limits easily.</span>
                                <span class="lang-sw">Fuatilia wanaokudai. Simamia kikomo cha deni kwa urahisi.</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body p-4">
                            <div class="feature-icon"><i class="bi bi-building"></i></div>
                            <h5 class="fw-bold">
                                <span class="lang-en">Multi-Shop</span>
                                <span class="lang-sw">Maduka Mengi</span>
                            </h5>
                            <p class="text-muted mb-0 small">
                                <span class="lang-en">Manage multiple shops from one account.</span>
                                <span class="lang-sw">Simamia maduka mengi kwa akaunti moja pekee.</span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card shadow-sm">
                        <div class="card-body p-4">
                            <div class="feature-icon"><i class="bi bi-phone"></i></div>
                            <h5 class="fw-bold">
                                <span class="lang-en">Works Anywhere</span>
                                <span class="lang-sw">Fanya Kazi Popote</span>
                            </h5>
                            <p class="text-muted mb-0 small">
                                <span class="lang-en">Phone, tablet or computer — even when away from the shop.</span>
                                <span class="lang-sw">Simu, tablet au computer — hata ukiwa mbali na duka.</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pricing -->
    <section class="py-5 bg-light" id="pricing">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title mb-2">
                    <span class="lang-en">Simple & Flexible Pricing</span>
                    <span class="lang-sw">Bei Rahisi na Zinazobadilika</span>
                </h2>
                <p class="text-muted">
                    <span class="lang-en">Choose the plan that fits your business</span>
                    <span class="lang-sw">Chagua kifurushi kinachofaa biashara yako</span>
                </p>
            </div>

            <div class="row g-4 justify-content-center">

                <!-- Starter -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 1.25rem;">
                        <div class="card-body p-4 p-lg-5">
                            <h5 class="fw-bold text-center mb-3">Starter</h5>
                            <div class="text-center mb-1">
                                <span class="display-5 fw-bold">Free</span>
                            </div>
                            <p class="text-center text-muted small mb-4">
                                <span class="lang-en">Perfect to get started</span>
                                <span class="lang-sw">Bora kwa kuanzia</span>
                            </p>

                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 1 Shop</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> POS & Sales</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Up to 100 Products</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Basic Reports</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> 1 User</li>
                                <li class="mb-2 text-muted"><i class="bi bi-x-circle me-2"></i> Customer Credit</li>
                                <li class="mb-2 text-muted"><i class="bi bi-x-circle me-2"></i> Multi-shop</li>
                            </ul>

                            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 fw-semibold">
                                <span class="lang-en">Start Free</span>
                                <span class="lang-sw lang-sw-inline">Anza Bure</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Business (Most Popular) -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow position-relative" 
                        style="border-radius: 1.25rem; border: 2px solid #2563eb !important;">
                        
                        <div class="position-absolute top-0 start-50 translate-middle">
                            <span class="badge bg-primary px-3 py-2 rounded-pill">Most Popular</span>
                        </div>

                        <div class="card-body p-4 p-lg-5">
                            <h5 class="fw-bold text-center mb-3 text-primary">Business</h5>
                            <div class="text-center mb-1">
                                <span class="display-5 fw-bold">TZS 25,000</span>
                            </div>
                            <p class="text-center text-muted small mb-4">per month</p>

                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Up to 3 Shops</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Unlimited Products</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Full Reports (Daily–Yearly)</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Customer Credit </li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Low Stock Alerts</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Up to 5 Users</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Expense Tracking</li>
                            </ul>

                            <a href="{{ route('register') }}" class="btn btn-primary w-100 fw-semibold">
                                <span class="lang-en">Choose Business</span>
                                <span class="lang-sw lang-sw-inline">Chagua Business</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pro -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm" style="border-radius: 1.25rem;">
                        <div class="card-body p-4 p-lg-5">
                            <h5 class="fw-bold text-center mb-3">Pro</h5>
                            <div class="text-center mb-1">
                                <span class="display-5 fw-bold">TZS 49,000</span>
                            </div>
                            <p class="text-center text-muted small mb-4">per month</p>

                            <ul class="list-unstyled small mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Unlimited Shops</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Everything in Business</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Advanced Profit Reports</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Unlimited Users</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Data Export (Excel / PDF)</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Priority Support</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Early access to new features</li>
                            </ul>

                            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100 fw-semibold">
                                <span class="lang-en">Choose Pro</span>
                                <span class="lang-sw lang-sw-inline">Chagua Pro</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Our Team -->
    <section id="team" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-white mb-2">Meet Our Team</h2>
                <p class="text-white">The people behind DukaFlow</p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Team Member 1 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card border-0 h-100 text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <img src="{{ asset('assets/images/profile3.jpg') }}" 
                                    alt="Team Member"
                                    class="rounded-circle shadow"
                                    style="width: 120px; height: 120px; object-fit: cover; border: 3px solid rgba(245, 158, 11, 0.3);">
                            </div>
                            <h5 class="fw-bold mb-1">Msafiri Juma</h5>
                            <p class="small mb-2" style="color: black">Founder | IT Support</p>
                            <p class="text-muted small mb-0">
                                Ensures the platform runs smoothly and provides technical support to our users.
                            </p>
                            <div class="d-flex justify-content-center gap-3 mt-3">
                                <a href="tel:+255687328084" class="btn btn-outline-primary btn-sm mt-3">
                                    <i class="bi bi-phone me-2"></i> Call
                                </a>
                                <a href="https://wa.me/255749696868" target="_blank" class="btn btn-primary btn-sm mt-3">
                                    <i class="fab fa-whatsapp me-2"></i> WhatsApp
                                </a>
                            </div>
                        </div>
                        <!-- Social icons -->
                        <div class="card-footer border-0 d-flex justify-content-center gap-3" style="background-color: #0f172a">
                            <a href="https://github.com/msafirijuma" class="text-light" target="_blank" title="GitHub">
                                <i class="fab fa-github fa-lg"></i>
                            </a>
                            <a href="https://twitter.com/Zul_fiqar99" class="text-light" target="_blank" title="Twitter">
                                <i class="fab fa-twitter fa-lg"></i>
                            </a>
                            <a href="https://www.facebook.com/profile.php?id=100087432184854" class="text-light" target="_blank" title="Facebook">
                                <i class="fab fa-facebook-f fa-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Team Member 2 -->
                <div class="col-md-4 col-sm-6">
                    <div class="card border-0 h-100 text-center">
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <img src="{{ asset('assets/images/male-user.png') }}" 
                                    alt="Team Member"
                                    class="rounded-circle shadow"
                                    style="width: 120px; height: 120px; object-fit: cover; border: 3px solid rgba(245, 158, 11, 0.3);">
                            </div>
                            <h5 class="fw-bold mb-1">Noel Faraja</h5>
                            <p class="small mb-2" style="color: black">Co. Founder | IT Support</p>
                            <p class="text-muted small mb-0">
                                Ensuring platform run smoothly and safety.
                            </p>
                            <div class="d-flex justify-content-center gap-3 mt-3">
                                <a href="tel:+255712483688" class="btn btn-outline-primary btn-sm mt-3">
                                    <i class="bi bi-phone me-2"></i> Call
                                </a>
                                <a href="https://wa.me/255712483688" target="_blank" class="btn btn-primary btn-sm mt-3">
                                    <i class="fab fa-whatsapp me-2"></i> WhatsApp
                                </a>
                            </div>
                        </div>
                        <!-- Social icons -->
                        <div class="card-footer border-0 d-flex justify-content-center gap-3" style="background-color: #0f172a">
                            <a href="#" class="text-light" target="_blank" title="Instagram">
                                <i class="fab fa-instagram fa-lg"></i>
                            </a>
                            <a href="#" class="text-light" target="_blank" title="Facebook">
                                <i class="fab fa-facebook-f fa-lg"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-5">
        <div class="container">
            <div class="cta-section text-center">
                <h2 class="fw-bold mb-3">
                    <span class="lang-en">Ready to run your shop smarter?</span>
                    <span class="lang-sw">Uko tayari kuendesha duka lako kijanja zaidi?</span>
                </h2>
                <p class="mb-4 opacity-75 col-lg-6 mx-auto">
                    <span class="lang-en">Join business owners who know their real profit every day.</span>
                    <span class="lang-sw">Jiunge na wamiliki wa biashara wanaojua faida yao kila siku.</span>
                </p>
                <a href="{{ route('register') }}" class="btn btn-light btn-lg fw-semibold">
                    <span class="lang-en">Create Free Account</span>
                    <span class="lang-sw lang-sw-inline">Fungua Account Bure</span>
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="fw-bold mb-1">
                        <i class="bi bi-shop-window me-1 text-primary"></i> DukaFlow
                    </div>
                    <div class="text-muted small">
                        <span class="lang-en">Smart POS & Inventory for Tanzanian businesses.</span>
                        <span class="lang-sw">POS na Inventory janja kwa biashara za Tanzania.</span>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('login') }}" class="text-decoration-none text-muted me-3">Login</a>
                    <a href="{{ route('register') }}" class="text-decoration-none text-muted me-3">Register</a>
                    <span class="text-muted small">© {{ date('Y') }} DukaFlow</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/255687328084?text=Habari%2C%20ningependa%20kujua%20zaidi%20kuhusu%20DukaFlow"
       class="whatsapp-float" target="_blank" title="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setLang(lang) {
            if (lang === 'sw') {
                document.body.classList.add('sw');
                document.getElementById('btn-sw').classList.add('btn-secondary');
                document.getElementById('btn-sw').classList.remove('btn-outline-secondary');
                document.getElementById('btn-en').classList.add('btn-outline-secondary');
                document.getElementById('btn-en').classList.remove('btn-secondary');
                localStorage.setItem('dukaflow_lang', 'sw');
            } else {
                document.body.classList.remove('sw');
                document.getElementById('btn-en').classList.add('btn-secondary');
                document.getElementById('btn-en').classList.remove('btn-outline-secondary');
                document.getElementById('btn-sw').classList.add('btn-outline-secondary');
                document.getElementById('btn-sw').classList.remove('btn-secondary');
                localStorage.setItem('dukaflow_lang', 'en');
            }
        }

        // Load saved language
        document.addEventListener('DOMContentLoaded', function () {
            const saved = localStorage.getItem('dukaflow_lang') || 'en';
            setLang(saved);
        });
    </script>
</body>
</html>