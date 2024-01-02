<h3> Yeni Aday Üye Kaydı ({{date('Y-m-d')}}) </h3>
<p>
    İsim & Soyisim: {{$user->full_name ?? $user->name . ' ' . $user->surname}} <br>
    Email: {{$user->email}} <br>
    Telefon: {{$user->phone}} <br>
</p>
<h4>
    Motosikletli Arama Kurtarma ve Destek Derneği
</h4>
