<footer class="footer">
    <div class="footer-content">
        <div class="social-icons">
            <a href="#"><i class="fa fa-facebook"></i></a>
            <a href="#"><i class="fa fa-instagram"></i></a>
            <a href="#"><i class="fa fa-youtube"></i></a>
            <a href="#"><i class="fa fa-twitter"></i></a>
        </div>

        <div class="footer-links">
            <a href="{{ asset('contact') }}">お問い合わせ</a>
            <a href="{{ asset('kiyaku') }}">利用規約</a>
            <a href="{{ asset('privacy-policy') }}">個人情報保護について</a>
            <a href="{{ asset('/admin/login') }}">CCM法人取引専用サイト</a>
            <a href="{{ asset('tokutei') }}">特定商取引法に基づく表示</a>
        </div>

        <div class="footer-address">
            @if($footerData['footer-address'])
                {{ $footerData['footer-address'] }}
            @else
                <p>住所情報なし</p>
            @endif
        </div>

        <div class="footer-contact">
            @if($footerData['footer-contact'])
                {{ $footerData['footer-contact'] }}
            @else
                <p>連絡先情報なし</p>
            @endif
        </div>

        <div class="footer-copyright">
            @if($footerData['footer-copyright'])
                {{ $footerData['footer-copyright'] }}
            @else
                <p>© {{ date('Y') }} Company Name</p>
            @endif
        </div>
    </div>
</footer>
