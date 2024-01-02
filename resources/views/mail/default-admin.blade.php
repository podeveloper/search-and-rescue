@component('mail::message')
    # Değerli Yönetici

    <p style="color:black">
        {{$content}}
    </p>

    @isset($url)
        @component('mail::button', ['url' => \URL::to($url)])
            Panele Git
        @endcomponent
    @endif

    <p style="color:#500050">
        Motiskletli Arama Kurtarma ve Destek Derneği<br>
    </p>
@endcomponent
