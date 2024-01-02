<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Eğitim Hatırlatıcısı</title>
</head>
<body>
<p>
    Sayın {{ ucwords($user->full_name ?? $user->name . ' ' . $user->surname) }},
</p>

<p>
    Görünüşe göre on gün veya daha fazla süredir eğitim içeriklerini izlemediniz.
</p>

<p>
    Saygılarımızla,
</p>
</body>
</html>
