<?php
    require "controller.php";
?>
<html>
    <head>
        <title>Admin Page</title>
        <style>
            body {
                font-family: Arial;
                color: #222;
            }
            body > .wrapper {
                margin: 0px auto;
                max-width: 1100px;
                padding: 18px 0px;
            }
            #leftCol {
                float: left;
                width: 32%;
                min-width: 340px;
                max-height: 100vh;
            }
            #leftCol > .item {
                list-style-type: none;
                padding: 8px 12px;
                border-radius: 3px;
                cursor: pointer;
            }
            #leftCol > .item:hover {
                background: #d7e7f5;
            }
            #leftCol > .item .updatedAt {
                padding:1px 0px;
                color: #999;
                font-size: 0.85em;
            }
            #leftCol > .item.active {
                background: #1a7abe;
                color: #f4f4f4;
            }
            #leftCol > .item.active .updatedAt {
                color: #c7cdd5;
            }

            .editor {
                float: left;
                box-sizing: border-box;
                padding: 8px 58px 18px;
                width: 68%;
            }
            .editor form > :first-child {
                padding-right: 28px;
            }
            .editor .title {
                margin: 2px 0px 12px;
                font-weight: 500;
                display: inline;
            }
            #article_title_input {
                display: none;
                font-size: 1.15em;
                padding: 3px 5px;
                border: 1.5px solid #cfcfcf;
                border-radius: 4px;
                margin: 0px 4px 0px 0px;
                min-width: calc(80% - 34px);
            }
            #titleEditBtn {
                display: inline-block;
                vertical-align: middle;
                margin: 4px;
                padding: 5px 3px;
                width: 30px;
                height: 28px;
                border-radius: 4px;
                position: relative;
                top: -7px;
                border: none;
                background: none;
                cursor: pointer;
            }
            #titleEditBtn:focus {
                outline-color: #35ccff;
                outline-offset: 1px;
            }
            #titleEditBtn > svg {
                fill: #bdbdc3;
                margin-left: -1px;
            }
            #titleEditBtn:hover {
                background: #e8e8e8;
            }
            #titleEditBtn:hover > svg {
                fill: #c3c3c8;
            }
            #article-content {
                font-size: 1.08em;
                padding: 4px 6px;
                min-width: 80%;
                max-width: 100%;
                min-height: 260px;
            }
            .line {
                padding: 7px 0px;
            }
            input[type="submit"], input[type="button"], button {
                font-size: 1.13em;
                padding: 8px 13px;
                background: linear-gradient(to bottom, #ffffff38 0%, #847f7f3b 100%), #2d7bb9;
                border: 1px solid #556982;
                border-radius: 6px;
                cursor: pointer;
                color: #fcfcfc;
                text-shadow: 1px 1px #31495a, -1px 1px #7993c0;
            }
            #success, #error {
                box-sizing: border-box;
                opacity: 0;
                border-radius: 6px;
                color: #2e3537;
                padding: 10px 18px;
                width: 80%;
                margin: 30px 0px 20px;
                transition: opacity 0.18s;
            }
            #success {
                border: 2px solid #517c85;
                background: #92d3dc;
            }
            #error {
                border: 2px solid #8d5c68;
                background: #e2a8b0;
            }
        </style>
        <script>
            
            window.addEventListener('DOMContentLoaded', function() {
                let items = document.querySelectorAll('#leftCol > .item');
                items.forEach((item) => {
                    item.addEventListener('click', (event) => {
                        let el = event.target;
                        while (!el.classList.contains('item') && el.parentNode) {
                            el = el.parentNode;
                        }
                        loadArticle(el);
                    });
                });
                if (items.length) {
                    items[0].click();
                }
                document.getElementById('ifr').onload = function() {
                    if (this.contentWindow.location.href == 'about:blank') {
                        return;
                    }
                    try {
                        var result = JSON.parse(this.contentWindow.document.body.textContent);
                        if (result.result == 'success') {
                            document.getElementById('error').style.display = 'none'
                            document.getElementById('error').style.opacity = '0'
                            document.getElementById('success').style.display = ''
                            setTimeout(() => document.getElementById('success').style.opacity = '1', 100)
                            document.querySelector('#leftCol > .item.active').childNodes[0].textContent = document.getElementById('articleTitle').textContent
                            if (result.timestamp) {
                                document.querySelector('#leftCol > .item.active').children[0].textContent = 'Last updated on ' + result.timestamp
                            }
                        } else {
                            document.getElementById('success').style.display = 'none'
                            document.getElementById('success').style.opacity = '0'
                            document.getElementById('error').style.display = ''
                            setTimeout(() => document.getElementById('error').style.opacity = '1', 100)
                        }
                    } catch (e) {
                        console.log(e);
                    }
                }
                document.getElementById('titleEditBtn').onclick = function(event) {
                    if (document.getElementById('article_title_input').style.display == '') {
                        document.getElementById('articleTitle').style.display = 'none'
                        document.getElementById('article_title_input').style.display = 'inline-block'
                        document.getElementById('article_title_input').focus()
                    } else {
                        if (timer) {
                            clearTimeout(timer);
                            timer = null;
                        }
                        document.getElementById('articleTitle').textContent = document.getElementById('article_title_input').value
                        document.getElementById('articleTitle').style.display = ''
                        document.getElementById('article_title_input').style.display = ''
                    }
                    event.preventDefault();
                }

                var timer = null;
                var flag = false;

                document.getElementById('article_title_input').onblur = 
                document.getElementById('article_title_input').onkeyup =
                    function(event) {
                        if (flag) {
                            flag = false;
                            return;
                        }
                        if (!event.key || event.key == "Enter") {
                            flag = true;
                            timer = setTimeout(() => document.getElementById('titleEditBtn').click(), 100);
                        }
                        if (event.key && event.key == "Enter") {
                            event.preventDefault();
                        }
                    }
                    document.getElementById('article_title_input').onkeydown = function(event) {
                        if (event.key == "Enter") {
                            event.preventDefault();
                        }
                    }
            });

            function loadArticle(el) {
                document.getElementById('success').style.opacity = '0'
                document.getElementById('success').style.display = 'none'
                setTimeout(() => document.getElementById('success').style.display = '', 100)

                if (!el.getAttribute('data-href')) {
                    return;
                }

                [].forEach.call(el.parentNode.children, (item) => {
                    item.classList.remove('active');
                });

                fetch('editor.php?action=get&article=' + el.getAttribute('data-href'))
                    .then(res => res.text())
                    .then(content => {
                        document.getElementById('articleTitle').textContent = el.childNodes[0].textContent
                        document.getElementById('article_title_input').value = el.childNodes[0].textContent
                        document.getElementById('article_id').value = el.getAttribute('data-href');
                        document.getElementById('article-content').value = content;
                    });

                el.classList.add('active');
            }

        </script>
    </head>
    <body>
        <div class="wrapper">
            <nav id="leftCol">
                <?php
                    $articles = Controller::getArticlesList();
                    foreach ($articles as $article) {
                        $article['text'] = "";
                        echo '<li class="item" data-href="' . $article['href'] . '">' . trim($article['title']) .
                            '<div class="updatedAt">Last updated on '. $article['modified_on'] . '</div></li>';
                    }
                ?>
            </nav>
            <div id="main">
                <div class="editor">
                    <form action="editor.php?action=update" target="ifr" method="POST">
                        <div><h1 class="title" id="articleTitle">Article title</h1><input name="title" id="article_title_input" /><button id="titleEditBtn"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#bdbdc3" version="1.1" id="Capa_1" width="15px" height="15px" viewBox="0 0 528.899 528.899" xml:space="preserve"><g><path d="M328.883,89.125l107.59,107.589l-272.34,272.34L56.604,361.465L328.883,89.125z M518.113,63.177l-47.981-47.981   c-18.543-18.543-48.653-18.543-67.259,0l-45.961,45.961l107.59,107.59l53.611-53.611   C532.495,100.753,532.495,77.559,518.113,63.177z M0.3,512.69c-1.958,8.812,5.998,16.708,14.811,14.565l119.891-29.069   L27.473,390.597L0.3,512.69z"/></g></svg></button></div>
                        <div class="line"><textarea name="text" id="article-content"></textarea></div>
                        <div class="line"><input type="submit" value="Save" /></div>
                        <input type="hidden" name="article" id="article_id" />
                        <div id="success">Article has been saved</div>
                        <div id="error">An error has occured</div>
                    </form>
                    <iframe name="ifr" id="ifr" style="display: none; visibility: hidden"></iframe>
                </div>
            </div>
        </div>
    </body>
</html>