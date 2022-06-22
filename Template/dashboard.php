<!-- Coding by CodingLab | www.codinglabweb.com -->
<!DOCTYPE html>
<html lang="ja">
<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="/AuthSample/CSS/main.css"> 
    <link rel="stylesheet" href="/AuthSample/CSS/mypage.css">
    <link rel="stylesheet" href="/AuthSample/CSS/style.css">
    <?=isset($css) ? $css : ''?>
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    
    <title><?=isset($SERVICE_NAME) ? $SERVICE_NAME : 'HolyLive'?></title>
</head>
<body>
    <nav class="sidebar close">
        <header>
            <div class="image-text">
                <span class="image">
                    <img src="<?=$pict?>" alt="">
                </span>

                <div class="text logo-text">
                    <span class="name"><?=$name?></span>
                    <p class="profession"></p>
                </div>
            </div>

            <i class='bx bx-chevron-right toggle'></i>
        </header>

        <div class="menu-bar">
            <div class="menu">

                <li class="search-box">
                    <i class='bx bx-search icon'></i>
                    <input type="text" placeholder="検索">
                </li>

                <ul class="menu-links">
                    <li class="nav-link">
                        <!-- hrefは全てフルパスURLで書くこと（このテンプレートがどこから呼び出されるかわからないので） -->
                        <a href="/AuthSample/MyPage/home.php">
                            <i class='bx bx-home-alt icon' ></i>
                            <span class="text nav-text">マイページ</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-bar-chart-alt-2 icon' ></i>
                            <span class="text nav-text">学習成果</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-bell icon'></i>
                            <span class="text nav-text">通知</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="#">
                            <i class='bx bx-heart icon' ></i>
                            <span class="text nav-text">オファー</span>
                        </a>
                    </li>

                    <li class="nav-link">
                        <a href="/AuthSample/MyPage/ProfileSetting.php">
                            <i class='bx bxs-exit icon' ></i>
                            <span class="text nav-text">プロフィール設定</span>
                        </a>
                    </li>

                </ul>
            </div>

            <div class="bottom-content">
                <li class="">
                    <a href="/AuthSample/Process/Logout.php">
                        <i class='bx bx-log-out icon' ></i>
                        <span class="text nav-text">ログアウト</span>
                    </a>
                </li>
                
            </div>
        </div>

    </nav>

    <section class="home">
        <?=$content?>
    </section>

    <script src="/AuthSample/JavaScript/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <?=isset($js) ? $js : ''?>
</body>
</html>