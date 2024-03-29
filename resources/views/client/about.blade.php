@extends('client.layouts.main')
@section('content')

    <div class="page-heading header-about header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>О компании</h4>
                        <h2>Lumos</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="best-features about-features">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>О нас</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="right-image">
                        <img
                            src="{{Storage::url('about/240_F_613556581_kBWsPNma6uDmcF3XnWwsKf8LFMXvH8tD.jpg')}}"
                            alt="">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="left-content">
                        <h4>Кто мы и почему мы?</h4>
                        <p>Потому что мы классные.</p>
                        <ul class="social-icons">
                            <li><a href="https://facebook.com/"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="https://youtube.com/"><i class="fa fa-youtube"></i></a></li>
                            <li><a href="https://vk.com/"><i class="fa fa-vk"></i></a></li>
                            <li><a href="https://instagram.com/"><i class="fa fa-instagram"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="team-members">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Наша команда</h2>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <div class="thumb-container">
                            <img
                                src="{{Storage::url('about/medium-shot-woman-doing-artwork_23-2148966933.jpg')}}"
                                alt="">
                            <div class="hover-effect">
                                <div class="hover-content">
                                    <ul class="social-icons">
                                        <li><a href="https://facebook.com/"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="https://youtube.com/"><i class="fa fa-youtube"></i></a></li>
                                        <li><a href="https://vk.com/"><i class="fa fa-vk"></i></a></li>
                                        <li><a href="https://instagram.com/"><i class="fa fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="down-content">
                            <h4>Johnny William</h4>
                            <span>Разработчик этого гениального сайта</span>
                            <p>PHP and Laravel.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <div class="thumb-container">
                            <img
                                src="{{Storage::url('about/woman-building-her-own-dried-flowers-arrangement_23-2149626632.jpg')}}"
                                alt="">
                            <div class="hover-effect">
                                <div class="hover-content">
                                    <ul class="social-icons">
                                        <li><a href="https://facebook.com/"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="https://youtube.com/"><i class="fa fa-youtube"></i></a></li>
                                        <li><a href="https://vk.com/"><i class="fa fa-vk"></i></a></li>
                                        <li><a href="https://instagram.com/"><i class="fa fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="down-content">
                            <h4>Karry Pitcher</h4>
                            <span>Креативный директор</span>
                            <p>Что-то на креативном</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <div class="thumb-container">
                            <img
                                src="{{Storage::url('about/woman-building-her-own-dried-flowers-arrangement_23-2149626654.jpg')}}"
                                alt="">
                            <div class="hover-effect">
                                <div class="hover-content">
                                    <ul class="social-icons">
                                        <li><a href="https://facebook.com/"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="https://youtube.com/"><i class="fa fa-youtube"></i></a></li>
                                        <li><a href="https://vk.com/"><i class="fa fa-vk"></i></a></li>
                                        <li><a href="https://instagram.com/"><i class="fa fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="down-content">
                            <h4>Michael Soft</h4>
                            <span>HR</span>
                            <p>Что-то на HR-овском</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <div class="thumb-container">
                            <img
                                src="{{Storage::url('about/woman-craftmaster-at-a-pottery-shop_1303-23729.jpg')}}"
                                alt="">
                            <div class="hover-effect">
                                <div class="hover-content">
                                    <ul class="social-icons">
                                        <li><a href="https://facebook.com/"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="https://youtube.com/"><i class="fa fa-youtube"></i></a></li>
                                        <li><a href="https://vk.com/"><i class="fa fa-vk"></i></a></li>
                                        <li><a href="https://instagram.com/"><i class="fa fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="down-content">
                            <h4>Mary Cool</h4>
                            <span>Шоколатье</span>
                            <p>Родилась с шоколадом в руках.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <div class="thumb-container">
                            <img
                                src="{{Storage::url('about/woman-makes-handmade-diy-lamp-from-jute-rope_266247-3229.jpg')}}"
                                alt="">
                            <div class="hover-effect">
                                <div class="hover-content">
                                    <ul class="social-icons">
                                        <li><a href="https://facebook.com/"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="https://youtube.com/"><i class="fa fa-youtube"></i></a></li>
                                        <li><a href="https://vk.com/"><i class="fa fa-vk"></i></a></li>
                                        <li><a href="https://instagram.com/"><i class="fa fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="down-content">
                            <h4>George Walker</h4>
                            <span>Свечник</span>
                            <p>Родился со свечкой в руках.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member">
                        <div class="thumb-container">
                            <img
                                src="{{Storage::url('about/woman-working-in-her-pottery-workshop_23-2148846469.jpg')}}"
                                alt="">
                            <div class="hover-effect">
                                <div class="hover-content">
                                    <ul class="social-icons">
                                        <li><a href="https://facebook.com/"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="https://youtube.com/"><i class="fa fa-youtube"></i></a></li>
                                        <li><a href="https://vk.com/"><i class="fa fa-vk"></i></a></li>
                                        <li><a href="https://instagram.com/"><i class="fa fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="down-content">
                            <h4>Kate Town</h4>
                            <span>Мыловар</span>
                            <p>Родилась с мылом в руках.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="happy-clients">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Наши клиенты</h2>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="owl-clients owl-carousel">
                        <div class="client-item">
                            <img
                                src="{{Storage::url('about/240_F_106399721_iZxzNqWSdE9TegnVT1IlE7DsZgbCIOcq.jpg')}}"
                                alt="1">

                        </div>

                        <div class="client-item">
                            <img
                                src="{{Storage::url('about/240_F_309202280_CgsWoCAdLBe9INBvdwBKUkpaLEP4XNLa.jpg')}}"
                                alt="2">
                        </div>

                        <div class="client-item">
                            <img
                                src="{{Storage::url('about/240_F_616837236_kf8zpl8OzbkiMx86j3vpMSPTrQlAm9xe.jpg')}}"
                                alt="3">
                        </div>

                        <div class="client-item">
                            <img
                                src="{{Storage::url('about/240_F_613232986_z3fizRZx4LXlyxOGlC7fDGnkadZI4G6c.jpg')}}"
                                alt="4">
                        </div>

                        <div class="client-item">
                            <img
                                src="{{Storage::url('about/240_F_597996928_YpHojBpjfIRMFqOVi7Qc9E5ehfq5ZYR5.jpg')}}"
                                alt="5">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="find-us">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <h2>Наше расположение</h2>
                    </div>
                </div>
                <div class="col-md-8">
                    <!-- How to change your own map point
        1. Go to Google Maps
        2. Click on your location point
        3. Click "Share" and choose "Embed map" tab
        4. Copy only URL and paste it within the src="" field below
    -->
                    <div id="map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2951.7277376835364!2d46.031626722800304!3d51.52977567104912!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4114c7c01d42bc9d%3A0x8f159d7de59d6eba!2z0KHQsNGA0LDRgtC-0LLRgdC60LDRjyDQs9C-0YHRg9C00LDRgNGB0YLQstC10L3QvdCw0Y8g0LrQvtC90YHQtdGA0LLQsNGC0L7RgNC40Y8g0LjQvNC10L3QuCDQmy4g0JIuINCh0L7QsdC40L3QvtCy0LA!5e0!3m2!1sru!2sru!4v1673352522258!5m2!1sru!2sru"
                            width="100%" height="330px" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="left-content">
                        <h4>Время работы</h4>
                        пн: 09:00 - 20:00<br>вт: 09:00 - 20:00<br>ср: 09:00 - 20:00<br>чт: 09:00 - 20:00<br>пт: 09:00 -
                        20:00<br>сб: 09:00 - 20:00<br>вс: 09:00 - 20:00<br><br>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
