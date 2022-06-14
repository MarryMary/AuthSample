{% parts_block(css) %}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="{% resources(CSS/Welcome/style.css) %}">
{% endparts_block %}



{% parts_block(contents) %}
    <h1>{{ $title }}</h1>
    <p>
        ClearSkyフレームワークにようこそ！この画面が出ている場合はシステムが正常に動作しています。<br>
        これから本フレームワークを使用した快適なWeb開発/学習をお楽しみください。
    </p>
{% endparts_block %}



{% parts_block(JavaScript) %}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
{% endparts_block %}

{% assembleTo(SampleTemplate/HelloFrame.php) %}