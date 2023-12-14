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
            .editor > .title {
                margin: 2px 0px 12px;
                font-weight: 500;
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
            #success {
                box-sizing: border-box;
                opacity: 0;
                border: 2px solid #517c85;
                background: #92d3dc;
                border-radius: 6px;
                color: #2e3537;
                padding: 10px 18px;
                width: 80%;
                margin: 30px 0px 20px;
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
                        return
                    }
                    document.getElementById('success').style.opacity = '1'
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
                    <h1 class="title" id="articleTitle">Article title</h1>
                    <form action="editor.php?action=update" target="ifr" method="POST">
                        <div class="line"><textarea name="text" id="article-content"></textarea></div>
                        <div class="line"><input type="submit" value="Save" /></div>
                        <input type="hidden" name="article" id="article_id" />
                        <div id="success">Article was saved</div>
                    </form>
                    <iframe name="ifr" id="ifr" style="display: none; visibility: hidden"></iframe>
                </div>
            </div>
        </div>
    </body>
</html>