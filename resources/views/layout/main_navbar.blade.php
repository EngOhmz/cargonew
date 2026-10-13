
<div class="navbar navbar-expand-lg navbar-dark bg-indigo navbar-static">
    <div class="d-flex flex-1 d-lg-none">
        <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
            <i class="icon-paragraph-justify3"></i>
        </button>
    </div>

    <div class="navbar-brand text-center text-lg-left">

    </div>

    <div class="navbar-collapse collapse flex-lg-1 mx-lg-3 order-2 order-lg-1">
        <div class="navbar-contact d-flex align-items-center py-2 py-lg-0">
            <div class="text-white">
                <small class="d-block d-lg-inline">
                    <i class="icon-phone2"></i> For help support: 
                    <a href="https://wa.me/{{ config('app.support_whatsapp') }}" target="_blank" class="text-white" style="text-decoration: underline;">
                        Call on WhatsApp: {{ config('app.support_phone') }}
                    </a>
                    | 
                    <i class="icon-mail5"></i> 
                    <a href="mailto:{{ config('app.support_email') }}" class="text-white" style="text-decoration: underline;">
                        Email: {{ config('app.support_email') }}
                    </a>
                </small>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end align-items-center flex-1 flex-lg-0 order-1 order-lg-2">
        <ul class="navbar-nav flex-row">
            <li class="nav-item">
            <a href="{{ route('logout') }}"   class="navbar-nav-link navbar-nav-link-toggler"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                   <i class="icon-switch2"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
             
            </li>
        </ul>
    </div>
</div>
