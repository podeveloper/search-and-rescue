@component('mail::message')
    # Değerli {{ isset($user) ? ucwords($user->full_name) : 'Gönüllümüz' }}

    <p style="color:black">
        {!! $content ?? '' !!}
    </p>

    @isset($url)
        @component('mail::button', ['url' => \URL::to($url)])
            Buraya Tıklayın
        @endcomponent
    @endif

    <p style="color:#500050">
        Motiskletli Arama Kurtarma ve Destek Derneği<br>
    </p>
@endcomponent
