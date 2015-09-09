<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>Madokami.com &middot; Kawaii File Hosting</title>
        <link rel="shortcut icon" href=favicon.ico type="image/x-icon">
        <link rel="stylesheet" href="{{ asset('css/madokami.css') }}">
    </head>
    <body>
        <div class="container">
            <div class="jumbotron">
                <h1>Ohayou!</h1>

                <p class="lead">Max upload size is 50MiB, read the <a href="faq.html">FAQ</a></p>
                <noscript>
                    <p class="alert alert-error"><strong>Enable JavaScript</strong> you fucking autist neckbeard, it's
                        not gonna hurt you</p>
                </noscript>
                <p id="no-file-api" class="alert alert-error">
                    <strong>Your browser is shit.</strong> Install the latest<a href="http://firefox.com/">Firefox</a>
                    or <a href="http://chrome.google.com/">Botnet</a> and come back &lt;3
                </p>
                <a href="javascript:;" id="upload-btn" class="btn">Select <span>or drop </span>file(s)</a>
                <input type="file" id="upload-input" name="files[]" multiple data-max-size="50MiB">
                <ul id="upload-filelist"></ul>
            </div>

            <nav>
                <ul>
                    <li>
                        <a href="https://madokami.com/">Home</a></li>
                    <li>
                        <a href="https://fufufu.moe/">Board</a></li>
                    <li>
                        <a href="https://github.com/kimoi/Pomf">Github</a></li>
                    <li>
                </ul>
            </nav>
            <script src="{{ asset('js/madokami.js') }}"></script>
        </div>

        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-53705363-3', 'auto');
            ga('send', 'pageview');
        </script>
    </body>
</html>