<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= h($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="/AuthSample/CSS/Auth.css">
    <?=isset($GAuthJS) ? $GAuthJS : ''; ?>
</head>
<body>
    <div class="background" style="padding-top: 7%;">
        <div class="mx-auto" style="width:50%;">
            <div class="card">
                <h1><?= h($card_name) ?></h1>
                <p style="color: <?= $errtype ? 'red' : 'black' ?>;" id="message"><?= h($message) ?></p>
                <hr>
                <?= $form ?>
                <div style="margin: auto;">
                    <?=isset($GAuthButton) ? $GAuthButton : '' ?>
                </div>
                <div style="margin-top: 5%;">
                    <?= $option ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
    <?=isset($JS) ? $JS : ''; ?>
    <script src="<?=isset($scriptTo) ? $scriptTo : ''; ?>"></script>
</body>
</html>