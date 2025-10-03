<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <nav>
                <a href="#our-product">OUR PRODUCT</a>
                <a href="">CONTACT</a>
                <a href="#" class="btn btn-register">REGISTER</a>
            </nav>
        </header>

        <section class="hero-section">
            <div class="container-fluid">

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
            <h2>Are you keeping your customers waiting?</h2>
            <div class="flex-container">
                <div class="image-placeholder restaurant-image-2">
                </div>
                <div class="text-content">
                    <p>Waiting times for ordering and payment can dramatically reduce customer satisfaction. And during
                        peak hours, staff often can’t serve to provide the best service.</p>
                    <p class="highlight my-0">With
                        <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="Ordermate Logo"
                            class="logo-sm-m">,
                        <br>
                        you can say goodbye to those worries.
                    </p>
                    <a href="{{ route('register') }}" class="btn btn-register-large">REGISTER HERE</a>
                </div>
            </div>
        </section>

        <section class="features-section" id="our-product">
            <div class="feature-item">
                <div class="feature-number">1</div>
                <h3>Quick and Easy Ordering</h3>
                <div class="image-placeholder feature-image-1"></div>
                <p>Effortless ordering via smartphone.</p>
            </div>
            <div class="feature-item">
                <div class="feature-number">2</div>
                <h3>Operational Efficiency</h3>
                <div class="image-placeholder feature-image-2"></div>
                <p>Automated ordering, staff coordination, and real-time kitchen coordination.</p>
            </div>
            <div class="feature-item">
                <div class="feature-number">3</div>
                <h3>Data-driven Sales Growth</h3>
                <div class="image-placeholder feature-image-3"></div>
                <p>Analyze popular items & peak times. Drive menu development & promotions</p>
            </div>
        </section>

        <section class="benefits-section">
            <h2>Benefits of Implementation</h2>
            <div class="benefits-container">
                <div class="benefits-column restaurant">
                    <div class="tag-restaurant h3">For the Restaurant</div>
                    <ul>
                        <li>Reduced labor costs</li>
                        <li>Eliminate order-taking errors,</li>
                        <li>Serve more tables, reduce queue,</li>
                        <li>and more repeat customers.</li>
                    </ul>
                </div>
                <div class="benefits-column customer">
                    <div class="tag-customer h3">For Customers</div>
                    <ul>
                        <li>No waiting</li>
                        <li>Order at their own pace</li>
                        <li>Stress-free dining experience</li>
                    </ul>
                </div>
            </div>
            <div class="visuals-wrapper">
                <div class="image-placeholder benefit-visual">
                </div>
                <div class="image-placeholder benefit-visual-2">
                </div>
            </div>
        </section>

        <section class="steps-section">
            <h2>Implementation Steps</h2>
            <div class="steps-container">
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div class="step_title">Download</div>
                    <p>ownload the app and instantly start your journey to smarter management.</p>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step_title">Register</div>
                    <p>Create your manager account in minutes to secure your store's digital presence.</p>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step_title">Setup Store, Menu & Table Info</div>
                    <p>Effortlessly upload your menu and define table QR codes. Get ready for seamless guest
                        ordering.</p>
                </div>
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step_title">Go Live!</div>
                    <p>Launch your digital system and watch labor costs drop while customer satisfaction soars.</p>
                </div>
            </div>

        </section>

            {{-- お問い合わせ --}}
            <section class="contact-section" id="contact-section">
                <h2>Contact Us</h2>

                <form action="{{ route('lp.contact.send') }}" method="POST" class="contact-form">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="name">First Name <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="text" name="name" id="name" class="form-control form-underline"
                                required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="last_name">Last Name <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="text" name="last_name" id="last_name"
                                class="form-control form-underline" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email Address <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="email" name="email" id="email" class="form-control form-underline"
                                required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="phone">Phone Number (Direct Line) <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="tel" name="phone" id="phone" class="form-control form-underline"
                                required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="store_name">Restaurant Name <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <input type="text" name="store_name" id="store_name"
                                class="form-control form-underline" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="message">Your Message / Inquiry <span class="required-star">*</span></label>
                        <div class="form-control-wrapper">
                            <textarea name="message" id="message" rows="5" class="form-control form-underline" required></textarea>
                        </div>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn btn-register-cta">Send Inquiry</button>
                    </div>
                </form>
            </section>

        <section class="cta-section">
            <p>Take your restaurant to the next level, today.</p>
            <div>
                <p class="with-text">with</p>
                <img src="{{ asset('images/ordermate_logo_main.png') }}" alt="Ordermate Logo" class="logo-sm">
            </div>
            <a href="#" class="btn btn-register-cta">REGISTER HERE</a>

            <br>
            <br>
            <p class="disclaimer">© All Rights are reserved by ordermate</p>
        </section>

    </div>

    <script>
        // ⭐ 修正: 成功またはエラーメッセージがある場合にToastを表示するJS ⭐
        document.addEventListener('DOMContentLoaded', function() {
            // Success Toastの処理
            const successToastEl = document.querySelector('.toast.text-bg-success');
            if (successToastEl) {
                const toast = new bootstrap.Toast(successToastEl);
                toast.show();
            }

            // Error Toastの処理
            const errorToastEl = document.querySelector('.toast.text-bg-danger');
            if (errorToastEl) {
                const toast = new bootstrap.Toast(errorToastEl);
                toast.show();
            }
        });
    </script>

</body>

</html>
