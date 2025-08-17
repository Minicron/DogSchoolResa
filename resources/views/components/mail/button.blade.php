@component('mail::message')
@slot('button')
<div style="text-align: center;">
    <a href="{{ $url }}" class="button" style="
        display: inline-block;
        background: linear-gradient(135deg, #3AAFA9 0%, #2B7A78 100%);
        color: #ffffff;
        padding: 15px 30px;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 16px;
        margin: 25px 0;
        text-align: center;
        box-shadow: 0 4px 15px rgba(59, 175, 169, 0.3);
        transition: all 0.3s ease;
    ">
        {{ $slot }}
    </a>
</div>
@endslot
@endcomponent
