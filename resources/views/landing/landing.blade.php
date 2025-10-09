<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Ordermate helps restaurants digitize their ordering system, reduce labor costs, and improve customer satisfaction.">
    <title>Ordermate - Run your restaurant smarter, not harder</title>
    <link rel="stylesheet" href="{{ asset('css/style-lp.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

    <div class="ordermate-lp">

        <header class="main-header">
            <img src="{{ asset('images/ordermate_logo_nav.png') }}" alt="Ordermate Logo" class="logo-mm">
        
            <nav class="nav-links">
                <a href="#top">{{__('manager.top')}}</a>
                <a href="#our-product">{{__('manager.our_product')}}</a>
                <a href="#benefits-section">{{__('manager.benefit')}}</a>
                <a href="#pricing-section">{{__('manager.price')}}</a>
                <a href="#contact-section">{{__('manager.contact')}}</a>
                <a href="#language">{{__('manager.language')}}</a>
            </nav>
        
            <div class="header-buttons">
                <a href="{{ route('register') }}" class="btn btn-register">{{__('manager.register')}}</a>
                <button class="hamburger" id="hamburger-btn">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </header>
        
        <!-- スマホ用メニュー -->
        <div class="mobile-menu" id="mobile-menu">
            <a href="#top">{{__('manager.top')}}</a>
            <a href="#our-product">{{__('manager.our_product')}}</a>
            <a href="#benefits-section">{{__('manager.benefit')}}</a>
            <a href="#pricing-section">{{__('manager.price')}}</a>
            <a href="#contact-section">{{__('manager.contact')}}</a>
            <a href="#language">{{__('manager.language')}}</a>
        </div>

        <section class="hero-section" id="top">
            <div class="container-fluid">
                <img src="{{asset('images/re-restaurant.png')}}" alt="Ordermate Restaurant">
            </div>
        </section>

        @if (session('success'))
            <div class="toast-container position-fixed top-50 start-50 translate-middle p-3" style="z-index: 9999;">
                <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
                    aria-atomic="true" style="min-width: 350px;">
                    <div class="d-flex">
                        <div class="toast-body fs-5"> {{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-container position-fixed top-50 start-50 translate-middle p-3" style="z-index: 9999;">
                <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                    aria-atomic="true" style="min-width: 350px;">
                    <div class="d-flex">
                        <div class="toast-body fs-5"> {{ session('error') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"></button>
                    </div>
                </div>
            </div>
        @endif

        <section class="question-section">
            <h2>{{ __('manager.question_title') }}</h2>
            <div class="flex-container">
                <div class="image-placeholder restaurant-image-2">
                    <img src="{{ asset('/images/2-before.png') }}" alt="Ordermate Restaurant 2">
                </div>
                <div class="text-content p-0">
                    <p>{{ __('manager.question_p1') }}</p>
                    <p class="highlight my-0">{{ __('manager.question_p2') }}</p>
                    <a href="{{ route('register') }}" class="btn btn-register-large">{{ __('manager.register_here') }}</a>
                </div>
            </div>
        </section>

        <section class="features-section" id="our-product">
            <h2>{{ __('manager.our_product_title') }}</h2>
        
            <div class="features-container">
                <div class="feature-item">
                    <div class="feature-number">1</div>
                    <h3>{{ __('manager.feature_1_title') }}</h3>
                    <div class="image-placeholder feature-image-1">
                        <img src="{{asset('images/4-cellphone.png')}}" alt="Ordermate Cellphone">
                    </div>
                    <p>{{ __('manager.feature_1_text') }}</p>
                </div>
        
                <div class="feature-item">
                    <div class="feature-number">2</div>
                    <h3>{{ __('manager.feature_2_title') }}</h3>
                    <div class="image-placeholder feature-image-2">
                        <img src="{{asset('images/5-manager.png')}}" alt="Ordermate Cellphone">
                    </div>
                    <p>{{ __('manager.feature_2_text') }}</p>
                </div>
        
                <div class="feature-item">
                    <div class="feature-number">3</div>
                    <h3>{{ __('manager.feature_3_title') }}</h3>
                    <div class="image-placeholder feature-image-3">
                        <img src="{{asset('images/6-managers.png')}}" alt="Ordermate Cellphone">
                    </div>
                    <p>{{ __('manager.feature_3_text') }}</p>
                </div>
        
                <div class="feature-item">
                    <div class="feature-number">4</div>
                    <h3>{{ __('manager.feature_4_title') }}</h3>
                    <div class="image-placeholder feature-image-4">
                        <img src="{{asset('images/10-language.png')}}" alt="Ordermate Cellphone">
                    </div>
                    <p>{{ __('manager.feature_4_text') }}</p>
                </div>
        
                <div class="feature-item">
                    <div class="feature-number">5</div>
                    <h3>{{ __('manager.feature_5_title') }}</h3>
                    <div class="image-placeholder feature-image-5">
                        <img src="{{asset('images/9-payment.png')}}" alt="Ordermate Cellphone">
                    </div>
                    <p>{{ __('manager.feature_5_text') }}</p>
                </div>
            </div>
        </section>
        

        <section class="screenshots-section">
            <h2>{{__('manager.screenshots_title')}}</h2>
            <p class="text-center mb-5">{{__('manager.screenshots_desc')}}</p>

            <div class="screenshots-wrapper">
                <div class="row justify-content-center align-items-center">
                    <!-- 💻 PCフレーム -->
                    <div class="col-lg-6 col-md-12 mb-5">
                        <div class="device-frame pc-frame">
                            <div id="pcCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <img src="{{ asset('images/manager-screenshot1.png') }}" class="d-block w-100" alt="PC Screenshot 1">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="{{ asset('images/manager-screenshot2.png') }}" class="d-block w-100" alt="PC Screenshot 2">
                                    </div>
                                    <div class="carousel-item">
                                        <img src="{{ asset('images/manager-screenshot3.png') }}" class="d-block w-100" alt="PC Screenshot 3">
                                    </div>
                                </div>
                                <button class="carousel-control-prev" type="button" data-bs-target="#pcCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#pcCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                                </button>
                            </div>
                        </div>
                        <p class="caption text-center mt-2">{{__('manager.desktop_interface')}}</p>
                    </div>

                    <!-- 📱 スマホフレーム -->
                    <div class="col-lg-6 col-md-12 mb-5">
                        <div class="device-frame phone-frame">
                        <div id="phoneCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="{{ asset('images/guest-screenshot1.png') }}" class="d-block w-100" alt="Phone Screenshot 1">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('images/guest-screenshot2.png') }}" class="d-block w-100" alt="Phone Screenshot 2">
                            </div>
                            <div class="carousel-item">
                                <img src="{{ asset('images/guest-screenshot3.png') }}" class="d-block w-100" alt="Phone Screenshot 3">
                            </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#phoneCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#phoneCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                        </div>
                        <p class="caption text-center mt-2">{{__('manager.mobile_interface')}}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="benefits-section" id="benefits-section">
            <h2>{{ __('manager.benefits_title') }}</h2>
            <div class="benefits-container">
                <div class="benefits-column restaurant">
                    <div class="tag-restaurant h3">{{ __('manager.benefit_for_shop') }}</div>
                    <ul>
                        <li>{{ __('manager.benefit_shop_1') }}</li>
                        <li>{{ __('manager.benefit_shop_2') }}</li>
                        <li>{{ __('manager.benefit_shop_3') }}</li>
                        <li>{{ __('manager.benefit_shop_4') }}</li>
                    </ul>
                </div>
                <div class="benefits-column customer">
                    <div class="tag-customer h3">{{ __('manager.benefit_for_guest') }}</div>
                    <ul>
                        <li>{{ __('manager.benefit_guest_1') }}</li>
                        <li>{{ __('manager.benefit_guest_2') }}</li>
                        <li>{{ __('manager.benefit_guest_3') }}</li>
                    </ul>
                </div>
            </div>
            <div class="visuals-wrapper">
                <div class="image-placeholder benefit-visual">
                    <img src="{{asset('/images/7-chef.png')}}" alt="Ordermate Chef">
                </div>
                <div class="image-placeholder benefit-visual-2">
                    <img src="{{asset('/images/8-customers.png')}}" alt="Ordermate Customers">
                </div>
            </div>
        </section>

        <section class="pricing-section" id="pricing-section">
            <h2>{{ __('manager.pricing_title') }}</h2>
            {{-- <p class="text-center mb-5">{{ __('manager.pricing_desc') }}</p> --}}

            <div class="pricing-container">
                <div class="pricing-card basic">
                    <h3>{{ __('manager.basic_plan') }}</h3>
                    <p class="price">¥5,000<span>{{__('manager.month')}}</span></p>
                    <ul>
                        <li>{{ __('manager.basic_detail_2') }}</li>
                        <li>{{ __('manager.basic_detail_1') }}</li>
                    </ul>
                    <a href="#" class="btn btn-register-large">{{ __('manager.start_basic') }}</a>
                </div>
                <div class="pricing-card premium">
                    <h3>{{ __('manager.premium_plan') }}</h3>
                    <p class="price">¥10,000<span>{{__('manager.month')}}</span></p>
                    <ul>
                        <li>{{ __('manager.premium_detail_1') }}</li>
                        <li>{{ __('manager.premium_detail_2') }}</li>
                    </ul>
                    <a href="#" class="btn btn-register-large">{{ __('manager.start_premium') }}</a>
                </div>
            </div>
            <p class="note text-center">{{ __('manager.note') }}</p>
        </section>

        <section class="steps-section">
            <h2>{{__('manager.steps_title')}}</h2>
            <div class="steps-container">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step_title">{{__('manager.step_2_title')}}</div>
                    <p>{{__('manager.step_2_text')}}</p>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step_title">{{__('manager.step_3_title')}}</div>
                    <p>{{__('manager.step_3_text')}}</p>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step_title">{{__('manager.step_4_title')}}</div>
                    <p>{{__('manager.step_4_text')}}</p>
                </div>
            </div>

        </section>

            {{-- お問い合わせ --}}
            <section class="contact-section" id="contact-section">
                <h2 class="mb-3">{{ __('manager.contact_title') }}</h2>

                <form action="{{ route('lp.contact.send') }}" method="POST" class="contact-form">
                    @csrf

                    <div class="form-group mb-3 mt-5">
                        <label for="name">{{ __('manager.contact_first_name') }} <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="text" name="name" id="name" class="form-control form-underline"
                                required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="last_name">{{__('manager.contact_last_name')}} <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="text" name="last_name" id="last_name"
                                class="form-control form-underline" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">{{__('manager.contact_email')}} <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="email" name="email" id="email" class="form-control form-underline"
                                required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="phone">{{__('manager.contact_phone')}} <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="tel" name="phone" id="phone" class="form-control form-underline"
                                required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="store_name">{{__('manager.contact_store')}} <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="text" name="store_name" id="store_name"
                                class="form-control form-underline" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="message">{{__('manager.contact_message')}} <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <textarea name="message" id="message" rows="5" class="form-control form-underline" required></textarea>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn btn-register-cta">{{__('manager.send_inquiry')}}</button>
                    </div>
                </form>
            </section>

            <section class="cta-section ordermate-lp">
                <p>{{__('manager.cta_message')}}</p>
            
                <div>
                    <p class="with-text">with</p>
                    <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="Ordermate Logo" class="logo-sm">
                </div>
            
                <a href="{{ route('register') }}" class="btn btn-register-cta">{{__('manager.register_cta')}}</a>
            
                <div class="language-selector" id="language">
                    <label for="language" class="language-label">{{ __('manager.language') }}</label>
                    <select class="language-select" id="language" name="language"
                            onchange="changeLanguage(this.value)">
                        <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                        <option value="ja" {{ app()->getLocale() === 'ja' ? 'selected' : '' }}>日本語</option>
                    </select>
                </div>
            
                <p class="disclaimer fs-6 mt-5">© All Rights are reserved by ordermate</p>
            </section>
            

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ✅ Toast（成功・エラー）の処理
            const successToastEl = document.querySelector('.toast.text-bg-success');
            if (successToastEl) new bootstrap.Toast(successToastEl).show();
        
            const errorToastEl = document.querySelector('.toast.text-bg-danger');
            if (errorToastEl) new bootstrap.Toast(errorToastEl).show();
        
            // ✅ ハンバーガーメニュー開閉処理
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const mobileMenu = document.getElementById('mobile-menu');
        
            if (hamburgerBtn && mobileMenu) {
                // 開閉トグル
                hamburgerBtn.addEventListener('click', function() {
                    mobileMenu.classList.toggle('active');
                });
        
                // ✅ メニュー内のリンクをクリックしたら自動で閉じる
                mobileMenu.querySelectorAll('a').forEach(link => {
                    link.addEventListener('click', () => {
                        mobileMenu.classList.remove('active');
                    });
                });
            }
        });
        function changeLanguage(lang) {
            const url = new URL(window.location.href);
            url.searchParams.set('lang', lang);
            window.location.href = url.toString();
        }
    </script>
        
</body>
</html>
