<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atte</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/verify-email.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header__utilities">
                <a class="header__logo" href="/">Atte</a>
            </div>
        </div>
    </header>

    <div class="verify-email__content">
        <div class="verify-email__heading">
            <h2>本人確認</h2>
        </div>
        <div>
            @if (session('my_status') == 'verification-link-sent')
            <div class="verify-email__message">
                <h3>本人確認の為、メールアドレス認証にご協力をお願いします</h3>
                <p> *********************************************************<br>
                    本人確認メールを再送しました<br>
                    ご登録いただいたメールアドレスをご確認ください<br>
                    *********************************************************</p>
                <a class="verify-email__back" href="/">メール送信画面へ戻る</a>
            </div>


            @else
            <div class="verify-email__message">
                <h3>本人確認の為、メールアドレス認証にご協力をお願いします</h3>
                <p> *********************************************************<br>
                    ご登録いただいたメールアドレス宛に、本人確認メールを送信しました<br>
                    メールを開いて認証ボタンをクリックし、Atteにログインしてください<br>
                    *********************************************************</p>
                <p>※もしメールが届いていない場合は、以下のボタンをクリックしてメールを再送してください</p>
                <form method="post" action="{{ route('verification.send') }}">
                    @method('post')
                    @csrf
                    <div class="verify-email__button">
                        <button class="verify-email__button-submit" type="submit">本人確認メール再送</button>
                    </div>
                </form>
            </div>
            @endif


        </div>
    </div>

    <footer class="footer">
        <div class="footer__inner">
            <small>Atte,inc.</small>
        </div>
    </footer>
</body>

</html>