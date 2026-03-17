<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Laravel API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://localhost";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.8.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.8.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-POSTapi-register">
                                <a href="#endpoints-POSTapi-register">Handle an incoming registration request.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-login">
                                <a href="#endpoints-POSTapi-login">Handle an incoming authentication request.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-logout">
                                <a href="#endpoints-POSTapi-logout">Destroy an authenticated session.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-books">
                                <a href="#endpoints-GETapi-admin-books">GET api/admin/books</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-admin-books">
                                <a href="#endpoints-POSTapi-admin-books">POST api/admin/books</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-books--id-">
                                <a href="#endpoints-GETapi-admin-books--id-">GET api/admin/books/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-admin-books--id-">
                                <a href="#endpoints-PUTapi-admin-books--id-">PUT api/admin/books/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-admin-books--id-">
                                <a href="#endpoints-DELETEapi-admin-books--id-">DELETE api/admin/books/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-authors">
                                <a href="#endpoints-GETapi-admin-authors">Display a listing of the resource.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-admin-authors">
                                <a href="#endpoints-POSTapi-admin-authors">Store a newly created resource in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-authors--id-">
                                <a href="#endpoints-GETapi-admin-authors--id-">Display the specified resource.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-admin-authors--id-">
                                <a href="#endpoints-PUTapi-admin-authors--id-">Update the specified resource in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-admin-authors--id-">
                                <a href="#endpoints-DELETEapi-admin-authors--id-">Remove the specified resource from storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-genres">
                                <a href="#endpoints-GETapi-admin-genres">Display a listing of the resource.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-admin-genres">
                                <a href="#endpoints-POSTapi-admin-genres">Store a newly created resource in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-genres--id-">
                                <a href="#endpoints-GETapi-admin-genres--id-">Display the specified resource.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-admin-genres--id-">
                                <a href="#endpoints-PUTapi-admin-genres--id-">Update the specified resource in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-admin-genres--id-">
                                <a href="#endpoints-DELETEapi-admin-genres--id-">Remove the specified resource from storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-publishers">
                                <a href="#endpoints-GETapi-admin-publishers">Display a listing of the resource.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-admin-publishers">
                                <a href="#endpoints-POSTapi-admin-publishers">Store a newly created resource in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-publishers--id-">
                                <a href="#endpoints-GETapi-admin-publishers--id-">Display the specified resource.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-admin-publishers--id-">
                                <a href="#endpoints-PUTapi-admin-publishers--id-">Update the specified resource in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-admin-publishers--id-">
                                <a href="#endpoints-DELETEapi-admin-publishers--id-">Remove the specified resource from storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-formats">
                                <a href="#endpoints-GETapi-admin-formats">Display a listing of the resource.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-admin-formats">
                                <a href="#endpoints-POSTapi-admin-formats">Store a newly created resource in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-admin-formats--id-">
                                <a href="#endpoints-GETapi-admin-formats--id-">Display the specified resource.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-admin-formats--id-">
                                <a href="#endpoints-PUTapi-admin-formats--id-">Update the specified resource in storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-admin-formats--id-">
                                <a href="#endpoints-DELETEapi-admin-formats--id-">Remove the specified resource from storage.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-genres">
                                <a href="#endpoints-GETapi-genres">GET api/genres</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-genres--id-">
                                <a href="#endpoints-GETapi-genres--id-">GET api/genres/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-authors">
                                <a href="#endpoints-GETapi-authors">GET api/authors</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-authors--id-">
                                <a href="#endpoints-GETapi-authors--id-">GET api/authors/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-publishers">
                                <a href="#endpoints-GETapi-publishers">GET api/publishers</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-publishers--id-">
                                <a href="#endpoints-GETapi-publishers--id-">GET api/publishers/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-formats">
                                <a href="#endpoints-GETapi-formats">GET api/formats</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-formats--id-">
                                <a href="#endpoints-GETapi-formats--id-">GET api/formats/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-books">
                                <a href="#endpoints-GETapi-books">GET api/books</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-books--id-">
                                <a href="#endpoints-GETapi-books--id-">GET api/books/{id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-covers--filename-">
                                <a href="#endpoints-GETapi-covers--filename-">Получить обложку книги (доступно всем)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-user">
                                <a href="#endpoints-GETapi-user">GET api/user</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-books-file--fileId--read">
                                <a href="#endpoints-GETapi-books-file--fileId--read">Читать файл книги в браузере (только авторизованные)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-books-file--fileId--download">
                                <a href="#endpoints-GETapi-books-file--fileId--download">Скачать файл книги (только авторизованные)</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-favorites">
                                <a href="#endpoints-GETapi-favorites">GET api/favorites</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-favorites--book-">
                                <a href="#endpoints-POSTapi-favorites--book-">POST api/favorites/{book}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-favorites--book-">
                                <a href="#endpoints-DELETEapi-favorites--book-">DELETE api/favorites/{book}</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: March 17, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>http://localhost</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-POSTapi-register">Handle an incoming registration request.</h2>

<p>
</p>



<span id="example-requests-POSTapi-register">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/register" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/register"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-register">
</span>
<span id="execution-results-POSTapi-register" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-register"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-register"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-register" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-register">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-register" data-method="POST"
      data-path="api/register"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-register', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-register"
                    onclick="tryItOut('POSTapi-register');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-register"
                    onclick="cancelTryOut('POSTapi-register');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-register"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-login">Handle an incoming authentication request.</h2>

<p>
</p>



<span id="example-requests-POSTapi-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"login\": \"architecto\",
    \"password\": \"|]|{+-\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "login": "architecto",
    "password": "|]|{+-"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-login">
</span>
<span id="execution-results-POSTapi-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-login" data-method="POST"
      data-path="api/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-login"
                    onclick="tryItOut('POSTapi-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-login"
                    onclick="cancelTryOut('POSTapi-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>login</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="login"                data-endpoint="POSTapi-login"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-login"
               value="|]|{+-"
               data-component="body">
    <br>
<p>Example: <code>|]|{+-</code></p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-logout">Destroy an authenticated session.</h2>

<p>
</p>



<span id="example-requests-POSTapi-logout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/logout" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/logout"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-logout">
</span>
<span id="execution-results-POSTapi-logout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-logout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-logout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-logout" data-method="POST"
      data-path="api/logout"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-logout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-logout"
                    onclick="tryItOut('POSTapi-logout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-logout"
                    onclick="cancelTryOut('POSTapi-logout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-logout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-admin-books">GET api/admin/books</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-books">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/books" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/books"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-books">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-books" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-books"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-books"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-books" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-books">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-books" data-method="GET"
      data-path="api/admin/books"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-books', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-books"
                    onclick="tryItOut('GETapi-admin-books');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-books"
                    onclick="cancelTryOut('GETapi-admin-books');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-books"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/books</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-books"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-books"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-admin-books">POST api/admin/books</h2>

<p>
</p>



<span id="example-requests-POSTapi-admin-books">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/admin/books" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/books"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-admin-books">
</span>
<span id="execution-results-POSTapi-admin-books" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-admin-books"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-admin-books"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-admin-books" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-admin-books">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-admin-books" data-method="POST"
      data-path="api/admin/books"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-admin-books', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-admin-books"
                    onclick="tryItOut('POSTapi-admin-books');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-admin-books"
                    onclick="cancelTryOut('POSTapi-admin-books');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-admin-books"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/admin/books</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-admin-books"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-admin-books"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-admin-books--id-">GET api/admin/books/{id}</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-books--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/books/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/books/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-books--id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-books--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-books--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-books--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-books--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-books--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-books--id-" data-method="GET"
      data-path="api/admin/books/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-books--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-books--id-"
                    onclick="tryItOut('GETapi-admin-books--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-books--id-"
                    onclick="cancelTryOut('GETapi-admin-books--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-books--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/books/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-books--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-books--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-admin-books--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the book. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-PUTapi-admin-books--id-">PUT api/admin/books/{id}</h2>

<p>
</p>



<span id="example-requests-PUTapi-admin-books--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/admin/books/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/books/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-admin-books--id-">
</span>
<span id="execution-results-PUTapi-admin-books--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-admin-books--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-admin-books--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-admin-books--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-admin-books--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-admin-books--id-" data-method="PUT"
      data-path="api/admin/books/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-admin-books--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-admin-books--id-"
                    onclick="tryItOut('PUTapi-admin-books--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-admin-books--id-"
                    onclick="cancelTryOut('PUTapi-admin-books--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-admin-books--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/admin/books/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/admin/books/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-admin-books--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-admin-books--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-admin-books--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the book. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-DELETEapi-admin-books--id-">DELETE api/admin/books/{id}</h2>

<p>
</p>



<span id="example-requests-DELETEapi-admin-books--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/admin/books/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/books/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-admin-books--id-">
</span>
<span id="execution-results-DELETEapi-admin-books--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-admin-books--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-admin-books--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-admin-books--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-admin-books--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-admin-books--id-" data-method="DELETE"
      data-path="api/admin/books/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-admin-books--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-admin-books--id-"
                    onclick="tryItOut('DELETEapi-admin-books--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-admin-books--id-"
                    onclick="cancelTryOut('DELETEapi-admin-books--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-admin-books--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/admin/books/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-admin-books--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-admin-books--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-admin-books--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the book. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-admin-authors">Display a listing of the resource.</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-authors">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/authors" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/authors"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-authors">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-authors" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-authors"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-authors"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-authors" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-authors">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-authors" data-method="GET"
      data-path="api/admin/authors"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-authors', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-authors"
                    onclick="tryItOut('GETapi-admin-authors');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-authors"
                    onclick="cancelTryOut('GETapi-admin-authors');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-authors"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/authors</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-authors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-authors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-admin-authors">Store a newly created resource in storage.</h2>

<p>
</p>



<span id="example-requests-POSTapi-admin-authors">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/admin/authors" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/authors"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-admin-authors">
</span>
<span id="execution-results-POSTapi-admin-authors" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-admin-authors"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-admin-authors"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-admin-authors" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-admin-authors">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-admin-authors" data-method="POST"
      data-path="api/admin/authors"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-admin-authors', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-admin-authors"
                    onclick="tryItOut('POSTapi-admin-authors');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-admin-authors"
                    onclick="cancelTryOut('POSTapi-admin-authors');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-admin-authors"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/admin/authors</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-admin-authors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-admin-authors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-admin-authors--id-">Display the specified resource.</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-authors--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/authors/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/authors/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-authors--id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-authors--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-authors--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-authors--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-authors--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-authors--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-authors--id-" data-method="GET"
      data-path="api/admin/authors/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-authors--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-authors--id-"
                    onclick="tryItOut('GETapi-admin-authors--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-authors--id-"
                    onclick="cancelTryOut('GETapi-admin-authors--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-authors--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/authors/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-authors--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-authors--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-admin-authors--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the author. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-PUTapi-admin-authors--id-">Update the specified resource in storage.</h2>

<p>
</p>



<span id="example-requests-PUTapi-admin-authors--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/admin/authors/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/authors/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-admin-authors--id-">
</span>
<span id="execution-results-PUTapi-admin-authors--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-admin-authors--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-admin-authors--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-admin-authors--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-admin-authors--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-admin-authors--id-" data-method="PUT"
      data-path="api/admin/authors/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-admin-authors--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-admin-authors--id-"
                    onclick="tryItOut('PUTapi-admin-authors--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-admin-authors--id-"
                    onclick="cancelTryOut('PUTapi-admin-authors--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-admin-authors--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/admin/authors/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/admin/authors/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-admin-authors--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-admin-authors--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-admin-authors--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the author. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-DELETEapi-admin-authors--id-">Remove the specified resource from storage.</h2>

<p>
</p>



<span id="example-requests-DELETEapi-admin-authors--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/admin/authors/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/authors/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-admin-authors--id-">
</span>
<span id="execution-results-DELETEapi-admin-authors--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-admin-authors--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-admin-authors--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-admin-authors--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-admin-authors--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-admin-authors--id-" data-method="DELETE"
      data-path="api/admin/authors/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-admin-authors--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-admin-authors--id-"
                    onclick="tryItOut('DELETEapi-admin-authors--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-admin-authors--id-"
                    onclick="cancelTryOut('DELETEapi-admin-authors--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-admin-authors--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/admin/authors/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-admin-authors--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-admin-authors--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-admin-authors--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the author. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-admin-genres">Display a listing of the resource.</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-genres">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/genres" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/genres"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-genres">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-genres" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-genres"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-genres"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-genres" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-genres">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-genres" data-method="GET"
      data-path="api/admin/genres"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-genres', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-genres"
                    onclick="tryItOut('GETapi-admin-genres');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-genres"
                    onclick="cancelTryOut('GETapi-admin-genres');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-genres"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/genres</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-genres"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-genres"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-admin-genres">Store a newly created resource in storage.</h2>

<p>
</p>



<span id="example-requests-POSTapi-admin-genres">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/admin/genres" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/genres"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-admin-genres">
</span>
<span id="execution-results-POSTapi-admin-genres" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-admin-genres"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-admin-genres"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-admin-genres" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-admin-genres">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-admin-genres" data-method="POST"
      data-path="api/admin/genres"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-admin-genres', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-admin-genres"
                    onclick="tryItOut('POSTapi-admin-genres');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-admin-genres"
                    onclick="cancelTryOut('POSTapi-admin-genres');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-admin-genres"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/admin/genres</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-admin-genres"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-admin-genres"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-admin-genres--id-">Display the specified resource.</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-genres--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/genres/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/genres/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-genres--id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-genres--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-genres--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-genres--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-genres--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-genres--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-genres--id-" data-method="GET"
      data-path="api/admin/genres/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-genres--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-genres--id-"
                    onclick="tryItOut('GETapi-admin-genres--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-genres--id-"
                    onclick="cancelTryOut('GETapi-admin-genres--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-genres--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/genres/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-genres--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-genres--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-admin-genres--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the genre. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-PUTapi-admin-genres--id-">Update the specified resource in storage.</h2>

<p>
</p>



<span id="example-requests-PUTapi-admin-genres--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/admin/genres/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/genres/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-admin-genres--id-">
</span>
<span id="execution-results-PUTapi-admin-genres--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-admin-genres--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-admin-genres--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-admin-genres--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-admin-genres--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-admin-genres--id-" data-method="PUT"
      data-path="api/admin/genres/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-admin-genres--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-admin-genres--id-"
                    onclick="tryItOut('PUTapi-admin-genres--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-admin-genres--id-"
                    onclick="cancelTryOut('PUTapi-admin-genres--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-admin-genres--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/admin/genres/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/admin/genres/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-admin-genres--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-admin-genres--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-admin-genres--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the genre. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-DELETEapi-admin-genres--id-">Remove the specified resource from storage.</h2>

<p>
</p>



<span id="example-requests-DELETEapi-admin-genres--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/admin/genres/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/genres/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-admin-genres--id-">
</span>
<span id="execution-results-DELETEapi-admin-genres--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-admin-genres--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-admin-genres--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-admin-genres--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-admin-genres--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-admin-genres--id-" data-method="DELETE"
      data-path="api/admin/genres/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-admin-genres--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-admin-genres--id-"
                    onclick="tryItOut('DELETEapi-admin-genres--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-admin-genres--id-"
                    onclick="cancelTryOut('DELETEapi-admin-genres--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-admin-genres--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/admin/genres/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-admin-genres--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-admin-genres--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-admin-genres--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the genre. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-admin-publishers">Display a listing of the resource.</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-publishers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/publishers" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/publishers"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-publishers">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-publishers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-publishers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-publishers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-publishers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-publishers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-publishers" data-method="GET"
      data-path="api/admin/publishers"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-publishers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-publishers"
                    onclick="tryItOut('GETapi-admin-publishers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-publishers"
                    onclick="cancelTryOut('GETapi-admin-publishers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-publishers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/publishers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-publishers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-publishers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-admin-publishers">Store a newly created resource in storage.</h2>

<p>
</p>



<span id="example-requests-POSTapi-admin-publishers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/admin/publishers" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/publishers"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-admin-publishers">
</span>
<span id="execution-results-POSTapi-admin-publishers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-admin-publishers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-admin-publishers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-admin-publishers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-admin-publishers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-admin-publishers" data-method="POST"
      data-path="api/admin/publishers"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-admin-publishers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-admin-publishers"
                    onclick="tryItOut('POSTapi-admin-publishers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-admin-publishers"
                    onclick="cancelTryOut('POSTapi-admin-publishers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-admin-publishers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/admin/publishers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-admin-publishers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-admin-publishers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-admin-publishers--id-">Display the specified resource.</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-publishers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/publishers/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/publishers/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-publishers--id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-publishers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-publishers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-publishers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-publishers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-publishers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-publishers--id-" data-method="GET"
      data-path="api/admin/publishers/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-publishers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-publishers--id-"
                    onclick="tryItOut('GETapi-admin-publishers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-publishers--id-"
                    onclick="cancelTryOut('GETapi-admin-publishers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-publishers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/publishers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-publishers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-publishers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-admin-publishers--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the publisher. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-PUTapi-admin-publishers--id-">Update the specified resource in storage.</h2>

<p>
</p>



<span id="example-requests-PUTapi-admin-publishers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/admin/publishers/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/publishers/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-admin-publishers--id-">
</span>
<span id="execution-results-PUTapi-admin-publishers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-admin-publishers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-admin-publishers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-admin-publishers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-admin-publishers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-admin-publishers--id-" data-method="PUT"
      data-path="api/admin/publishers/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-admin-publishers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-admin-publishers--id-"
                    onclick="tryItOut('PUTapi-admin-publishers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-admin-publishers--id-"
                    onclick="cancelTryOut('PUTapi-admin-publishers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-admin-publishers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/admin/publishers/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/admin/publishers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-admin-publishers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-admin-publishers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-admin-publishers--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the publisher. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-DELETEapi-admin-publishers--id-">Remove the specified resource from storage.</h2>

<p>
</p>



<span id="example-requests-DELETEapi-admin-publishers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/admin/publishers/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/publishers/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-admin-publishers--id-">
</span>
<span id="execution-results-DELETEapi-admin-publishers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-admin-publishers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-admin-publishers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-admin-publishers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-admin-publishers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-admin-publishers--id-" data-method="DELETE"
      data-path="api/admin/publishers/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-admin-publishers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-admin-publishers--id-"
                    onclick="tryItOut('DELETEapi-admin-publishers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-admin-publishers--id-"
                    onclick="cancelTryOut('DELETEapi-admin-publishers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-admin-publishers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/admin/publishers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-admin-publishers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-admin-publishers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-admin-publishers--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the publisher. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-admin-formats">Display a listing of the resource.</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-formats">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/formats" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/formats"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-formats">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-formats" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-formats"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-formats"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-formats" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-formats">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-formats" data-method="GET"
      data-path="api/admin/formats"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-formats', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-formats"
                    onclick="tryItOut('GETapi-admin-formats');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-formats"
                    onclick="cancelTryOut('GETapi-admin-formats');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-formats"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/formats</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-formats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-formats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-admin-formats">Store a newly created resource in storage.</h2>

<p>
</p>



<span id="example-requests-POSTapi-admin-formats">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/admin/formats" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/formats"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-admin-formats">
</span>
<span id="execution-results-POSTapi-admin-formats" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-admin-formats"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-admin-formats"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-admin-formats" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-admin-formats">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-admin-formats" data-method="POST"
      data-path="api/admin/formats"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-admin-formats', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-admin-formats"
                    onclick="tryItOut('POSTapi-admin-formats');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-admin-formats"
                    onclick="cancelTryOut('POSTapi-admin-formats');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-admin-formats"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/admin/formats</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-admin-formats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-admin-formats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-admin-formats--id-">Display the specified resource.</h2>

<p>
</p>



<span id="example-requests-GETapi-admin-formats--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/admin/formats/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/formats/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-admin-formats--id-">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-admin-formats--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-admin-formats--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-admin-formats--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-admin-formats--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-admin-formats--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-admin-formats--id-" data-method="GET"
      data-path="api/admin/formats/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-admin-formats--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-admin-formats--id-"
                    onclick="tryItOut('GETapi-admin-formats--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-admin-formats--id-"
                    onclick="cancelTryOut('GETapi-admin-formats--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-admin-formats--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/admin/formats/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-admin-formats--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-admin-formats--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-admin-formats--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the format. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-PUTapi-admin-formats--id-">Update the specified resource in storage.</h2>

<p>
</p>



<span id="example-requests-PUTapi-admin-formats--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "http://localhost/api/admin/formats/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/formats/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "PUT",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-admin-formats--id-">
</span>
<span id="execution-results-PUTapi-admin-formats--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-admin-formats--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-admin-formats--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-admin-formats--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-admin-formats--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-admin-formats--id-" data-method="PUT"
      data-path="api/admin/formats/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-admin-formats--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-admin-formats--id-"
                    onclick="tryItOut('PUTapi-admin-formats--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-admin-formats--id-"
                    onclick="cancelTryOut('PUTapi-admin-formats--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-admin-formats--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/admin/formats/{id}</code></b>
        </p>
            <p>
            <small class="badge badge-purple">PATCH</small>
            <b><code>api/admin/formats/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-admin-formats--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-admin-formats--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="PUTapi-admin-formats--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the format. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-DELETEapi-admin-formats--id-">Remove the specified resource from storage.</h2>

<p>
</p>



<span id="example-requests-DELETEapi-admin-formats--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/admin/formats/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/admin/formats/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-admin-formats--id-">
</span>
<span id="execution-results-DELETEapi-admin-formats--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-admin-formats--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-admin-formats--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-admin-formats--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-admin-formats--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-admin-formats--id-" data-method="DELETE"
      data-path="api/admin/formats/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-admin-formats--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-admin-formats--id-"
                    onclick="tryItOut('DELETEapi-admin-formats--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-admin-formats--id-"
                    onclick="cancelTryOut('DELETEapi-admin-formats--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-admin-formats--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/admin/formats/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-admin-formats--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-admin-formats--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="DELETEapi-admin-formats--id-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the format. Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-genres">GET api/genres</h2>

<p>
</p>



<span id="example-requests-GETapi-genres">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/genres" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/genres"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-genres">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">[
    {
        &quot;genre_id&quot;: 1,
        &quot;genre_name&quot;: &quot;Антиутопия&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 2,
        &quot;genre_name&quot;: &quot;Биографии и мемуары&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 3,
        &quot;genre_name&quot;: &quot;Научная фантастика&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 4,
        &quot;genre_name&quot;: &quot;Боевики&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 5,
        &quot;genre_name&quot;: &quot;Криминальные детективы&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 6,
        &quot;genre_name&quot;: &quot;Исторические детективы&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 7,
        &quot;genre_name&quot;: &quot;Автомобили и ПДД&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 8,
        &quot;genre_name&quot;: &quot;Базы данных&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 9,
        &quot;genre_name&quot;: &quot;Детская образовательная литература&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 10,
        &quot;genre_name&quot;: &quot;Программирование&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 11,
        &quot;genre_name&quot;: &quot;Природа и животные&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 12,
        &quot;genre_name&quot;: &quot;Биология&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 13,
        &quot;genre_name&quot;: &quot;Физика&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 14,
        &quot;genre_name&quot;: &quot;Военная проза&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 15,
        &quot;genre_name&quot;: &quot;Исторические приключения&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 16,
        &quot;genre_name&quot;: &quot;Мистика&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 17,
        &quot;genre_name&quot;: &quot;Городское фэнтези&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 18,
        &quot;genre_name&quot;: &quot;Фэнтези&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 19,
        &quot;genre_name&quot;: &quot;Книга-игра&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 20,
        &quot;genre_name&quot;: &quot;Философия&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 21,
        &quot;genre_name&quot;: &quot;Политика&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 22,
        &quot;genre_name&quot;: &quot;История&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 23,
        &quot;genre_name&quot;: &quot;Государство и право&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 24,
        &quot;genre_name&quot;: &quot;Классическая проза&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 25,
        &quot;genre_name&quot;: &quot;Роман&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 26,
        &quot;genre_name&quot;: &quot;Сказки&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 27,
        &quot;genre_name&quot;: &quot;Детские стихи&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 28,
        &quot;genre_name&quot;: &quot;Приключения про индейцев&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 29,
        &quot;genre_name&quot;: &quot;Путешествия и география&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 30,
        &quot;genre_name&quot;: &quot;Математика&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 31,
        &quot;genre_name&quot;: &quot;Справочники&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 32,
        &quot;genre_name&quot;: &quot;Руководства&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 33,
        &quot;genre_name&quot;: &quot;Учебники&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 34,
        &quot;genre_name&quot;: &quot;Публицистика&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 35,
        &quot;genre_name&quot;: &quot;Современная проза&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 36,
        &quot;genre_name&quot;: &quot;Вестерны&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 37,
        &quot;genre_name&quot;: &quot;Кулинария&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 38,
        &quot;genre_name&quot;: &quot;Научпоп&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 39,
        &quot;genre_name&quot;: &quot;Астрономия и Космос&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 40,
        &quot;genre_name&quot;: &quot;Геология и география&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 41,
        &quot;genre_name&quot;: &quot;Технические науки&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 42,
        &quot;genre_name&quot;: &quot;Военная документалистика&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 43,
        &quot;genre_name&quot;: &quot;Военная история&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 44,
        &quot;genre_name&quot;: &quot;Физическая химия&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 45,
        &quot;genre_name&quot;: &quot;Экология&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 46,
        &quot;genre_name&quot;: &quot;Искусство и Дизайн&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 47,
        &quot;genre_name&quot;: &quot;Обществознание&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;genre_id&quot;: 48,
        &quot;genre_name&quot;: &quot;Зоология&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    }
]</code>
 </pre>
    </span>
<span id="execution-results-GETapi-genres" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-genres"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-genres"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-genres" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-genres">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-genres" data-method="GET"
      data-path="api/genres"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-genres', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-genres"
                    onclick="tryItOut('GETapi-genres');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-genres"
                    onclick="cancelTryOut('GETapi-genres');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-genres"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/genres</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-genres"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-genres"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-genres--id-">GET api/genres/{id}</h2>

<p>
</p>



<span id="example-requests-GETapi-genres--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/genres/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/genres/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-genres--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;genre_id&quot;: 1,
    &quot;genre_name&quot;: &quot;Антиутопия&quot;,
    &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
    &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
    &quot;books&quot;: [
        {
            &quot;book_id&quot;: 1,
            &quot;book_title&quot;: &quot;Побег&quot;,
            &quot;description&quot;: &quot;Этот увлекательный роман - первая часть фантастической трилогии американской писательницы Джин Дюпро. Действие первой части происходит в таинственном городе Эмбере, над которым никогда не восходит солнце. Тусклые электрические фонари - единственный источник света для горожан. Но фонари все чаще гаснут, и скоро город окончательно погрузится во тьму. Существуют ли где-то во мраке, окружающем Эмбер, другие острова света? Никто не знает ответа на этот вопрос, и только подростки Лина Мэйфлит и Дун Харроу найдут путь к спасению.&quot;,
            &quot;published_year&quot;: 2008,
            &quot;publisher_id&quot;: 1,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/1.jpg&quot;,
            &quot;pivot&quot;: {
                &quot;genre_id&quot;: 1,
                &quot;book_id&quot;: 1
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-genres--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-genres--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-genres--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-genres--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-genres--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-genres--id-" data-method="GET"
      data-path="api/genres/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-genres--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-genres--id-"
                    onclick="tryItOut('GETapi-genres--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-genres--id-"
                    onclick="cancelTryOut('GETapi-genres--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-genres--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/genres/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-genres--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-genres--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-genres--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the genre. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-authors">GET api/authors</h2>

<p>
</p>



<span id="example-requests-GETapi-authors">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/authors" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/authors"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-authors">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">[
    {
        &quot;author_id&quot;: 1,
        &quot;last_name&quot;: &quot;Дюпро&quot;,
        &quot;first_name&quot;: &quot;Джин&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 2,
        &quot;last_name&quot;: &quot;Володихин&quot;,
        &quot;first_name&quot;: &quot;Дмитрий&quot;,
        &quot;middle_name&quot;: &quot;Михайлович&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 3,
        &quot;last_name&quot;: &quot;Маркеев&quot;,
        &quot;first_name&quot;: &quot;Олег&quot;,
        &quot;middle_name&quot;: &quot;Георгиевич&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 4,
        &quot;last_name&quot;: &quot;Парнов&quot;,
        &quot;first_name&quot;: &quot;Еремей&quot;,
        &quot;middle_name&quot;: &quot;Иудович&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 5,
        &quot;last_name&quot;: &quot;Емельянов&quot;,
        &quot;first_name&quot;: &quot;В.&quot;,
        &quot;middle_name&quot;: &quot;М.&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 6,
        &quot;last_name&quot;: &quot;Рябченко&quot;,
        &quot;first_name&quot;: &quot;Виктор&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 7,
        &quot;last_name&quot;: &quot;Рэнди Дэвис&quot;,
        &quot;first_name&quot;: &quot;Стефан&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 8,
        &quot;last_name&quot;: &quot;Эттенборо&quot;,
        &quot;first_name&quot;: &quot;Дэвид&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 9,
        &quot;last_name&quot;: &quot;Перельман&quot;,
        &quot;first_name&quot;: &quot;Яков&quot;,
        &quot;middle_name&quot;: &quot;Исидорович&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 10,
        &quot;last_name&quot;: &quot;Радзиевская&quot;,
        &quot;first_name&quot;: &quot;Софья&quot;,
        &quot;middle_name&quot;: &quot;Борисовна&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 11,
        &quot;last_name&quot;: &quot;Ренсом&quot;,
        &quot;first_name&quot;: &quot;Риггз&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 12,
        &quot;last_name&quot;: &quot;Браславский&quot;,
        &quot;first_name&quot;: &quot;Дмитрий&quot;,
        &quot;middle_name&quot;: &quot;Юрьевич&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 13,
        &quot;last_name&quot;: &quot;Маркс&quot;,
        &quot;first_name&quot;: &quot;Карл&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 14,
        &quot;last_name&quot;: &quot;Энгельс&quot;,
        &quot;first_name&quot;: &quot;Фридрих&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 15,
        &quot;last_name&quot;: &quot;Булгаков&quot;,
        &quot;first_name&quot;: &quot;Михаил&quot;,
        &quot;middle_name&quot;: &quot;Афанасьевич&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 16,
        &quot;last_name&quot;: &quot;Пушкин&quot;,
        &quot;first_name&quot;: &quot;Александр&quot;,
        &quot;middle_name&quot;: &quot;Сергеевич&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 17,
        &quot;last_name&quot;: &quot;Фидлер&quot;,
        &quot;first_name&quot;: &quot;Аркадий&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 18,
        &quot;last_name&quot;: &quot;Кофлер&quot;,
        &quot;first_name&quot;: &quot;Михаэль&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 19,
        &quot;last_name&quot;: &quot;Миронова&quot;,
        &quot;first_name&quot;: &quot;Татьяна&quot;,
        &quot;middle_name&quot;: &quot;Леонидовна&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 20,
        &quot;last_name&quot;: &quot;Юстейн&quot;,
        &quot;first_name&quot;: &quot;Гордер&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 21,
        &quot;last_name&quot;: &quot;Ламур&quot;,
        &quot;first_name&quot;: &quot;Луис&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 22,
        &quot;last_name&quot;: &quot;Велитов&quot;,
        &quot;first_name&quot;: &quot;Алим&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 23,
        &quot;last_name&quot;: &quot;Уштей&quot;,
        &quot;first_name&quot;: &quot;Анна&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 24,
        &quot;last_name&quot;: &quot;Хокинг&quot;,
        &quot;first_name&quot;: &quot;Стивен&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 25,
        &quot;last_name&quot;: &quot;Хабберт&quot;,
        &quot;first_name&quot;: &quot;Марион Кинг&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 26,
        &quot;last_name&quot;: &quot;Ковпак&quot;,
        &quot;first_name&quot;: &quot;Сидор&quot;,
        &quot;middle_name&quot;: &quot;Артемьевич&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 27,
        &quot;last_name&quot;: &quot;Дигонский&quot;,
        &quot;first_name&quot;: &quot;Сергей&quot;,
        &quot;middle_name&quot;: &quot;Викторович&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 28,
        &quot;last_name&quot;: &quot;Тен&quot;,
        &quot;first_name&quot;: &quot;Вячеслав&quot;,
        &quot;middle_name&quot;: &quot;Владимирович&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 29,
        &quot;last_name&quot;: &quot;Аксенов&quot;,
        &quot;first_name&quot;: &quot;Геннадий&quot;,
        &quot;middle_name&quot;: &quot;Петрович&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 30,
        &quot;last_name&quot;: &quot;Гомбрих&quot;,
        &quot;first_name&quot;: &quot;Эрнст&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 31,
        &quot;last_name&quot;: &quot;Вишняцкий&quot;,
        &quot;first_name&quot;: &quot;Леонид&quot;,
        &quot;middle_name&quot;: &quot;Борисович&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;author_id&quot;: 32,
        &quot;last_name&quot;: &quot;Даррелл&quot;,
        &quot;first_name&quot;: &quot;Джеральд&quot;,
        &quot;middle_name&quot;: null,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    }
]</code>
 </pre>
    </span>
<span id="execution-results-GETapi-authors" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-authors"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-authors"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-authors" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-authors">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-authors" data-method="GET"
      data-path="api/authors"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-authors', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-authors"
                    onclick="tryItOut('GETapi-authors');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-authors"
                    onclick="cancelTryOut('GETapi-authors');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-authors"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/authors</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-authors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-authors"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-authors--id-">GET api/authors/{id}</h2>

<p>
</p>



<span id="example-requests-GETapi-authors--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/authors/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/authors/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-authors--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;author_id&quot;: 1,
    &quot;last_name&quot;: &quot;Дюпро&quot;,
    &quot;first_name&quot;: &quot;Джин&quot;,
    &quot;middle_name&quot;: null,
    &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
    &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
    &quot;books&quot;: [
        {
            &quot;book_id&quot;: 1,
            &quot;book_title&quot;: &quot;Побег&quot;,
            &quot;description&quot;: &quot;Этот увлекательный роман - первая часть фантастической трилогии американской писательницы Джин Дюпро. Действие первой части происходит в таинственном городе Эмбере, над которым никогда не восходит солнце. Тусклые электрические фонари - единственный источник света для горожан. Но фонари все чаще гаснут, и скоро город окончательно погрузится во тьму. Существуют ли где-то во мраке, окружающем Эмбер, другие острова света? Никто не знает ответа на этот вопрос, и только подростки Лина Мэйфлит и Дун Харроу найдут путь к спасению.&quot;,
            &quot;published_year&quot;: 2008,
            &quot;publisher_id&quot;: 1,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/1.jpg&quot;,
            &quot;pivot&quot;: {
                &quot;author_id&quot;: 1,
                &quot;book_id&quot;: 1
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-authors--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-authors--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-authors--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-authors--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-authors--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-authors--id-" data-method="GET"
      data-path="api/authors/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-authors--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-authors--id-"
                    onclick="tryItOut('GETapi-authors--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-authors--id-"
                    onclick="cancelTryOut('GETapi-authors--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-authors--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/authors/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-authors--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-authors--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-authors--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the author. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-publishers">GET api/publishers</h2>

<p>
</p>



<span id="example-requests-GETapi-publishers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/publishers" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/publishers"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-publishers">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">[
    {
        &quot;publisher_id&quot;: 1,
        &quot;publisher_name&quot;: &quot;Махаон&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 2,
        &quot;publisher_name&quot;: &quot;Молодая гвардия&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 3,
        &quot;publisher_name&quot;: &quot;ОЛМА-ПРЕСС&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 4,
        &quot;publisher_name&quot;: &quot;Детская литература&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 5,
        &quot;publisher_name&quot;: &quot;Бук-Пресс&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 6,
        &quot;publisher_name&quot;: &quot;Омское книжное издательство&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 7,
        &quot;publisher_name&quot;: &quot;Диалектика&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 8,
        &quot;publisher_name&quot;: &quot;Мир&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 9,
        &quot;publisher_name&quot;: &quot;Наука&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 10,
        &quot;publisher_name&quot;: &quot;Татарское книжное издательство&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 11,
        &quot;publisher_name&quot;: &quot;Книжный клуб \&quot;Клуб Семейного Досуга\&quot;&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 12,
        &quot;publisher_name&quot;: &quot;Производственно-коммерческий центр \&quot;АТ\&quot;&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 13,
        &quot;publisher_name&quot;: &quot;Государственное издательство политической литературы&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 14,
        &quot;publisher_name&quot;: &quot;Ленинград&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 15,
        &quot;publisher_name&quot;: &quot;Питер&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 16,
        &quot;publisher_name&quot;: &quot;Алгоритм&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 17,
        &quot;publisher_name&quot;: &quot;Амфора&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 18,
        &quot;publisher_name&quot;: &quot;Центрполинраф&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 19,
        &quot;publisher_name&quot;: &quot;Росмэн&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 20,
        &quot;publisher_name&quot;: &quot;Api&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 21,
        &quot;publisher_name&quot;: &quot;Воениздат НКО СССР&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 22,
        &quot;publisher_name&quot;: &quot;Эдиториал УРСС&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 23,
        &quot;publisher_name&quot;: &quot;АСТ&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 24,
        &quot;publisher_name&quot;: &quot;Век2&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;
    },
    {
        &quot;publisher_id&quot;: 25,
        &quot;publisher_name&quot;: &quot;Воздушный транспорт&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;
    }
]</code>
 </pre>
    </span>
<span id="execution-results-GETapi-publishers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-publishers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-publishers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-publishers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-publishers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-publishers" data-method="GET"
      data-path="api/publishers"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-publishers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-publishers"
                    onclick="tryItOut('GETapi-publishers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-publishers"
                    onclick="cancelTryOut('GETapi-publishers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-publishers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/publishers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-publishers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-publishers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-publishers--id-">GET api/publishers/{id}</h2>

<p>
</p>



<span id="example-requests-GETapi-publishers--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/publishers/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/publishers/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-publishers--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;publisher_id&quot;: 1,
    &quot;publisher_name&quot;: &quot;Махаон&quot;,
    &quot;created_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
    &quot;updated_at&quot;: &quot;2026-03-17T11:55:35.000000Z&quot;,
    &quot;books&quot;: [
        {
            &quot;book_id&quot;: 1,
            &quot;book_title&quot;: &quot;Побег&quot;,
            &quot;description&quot;: &quot;Этот увлекательный роман - первая часть фантастической трилогии американской писательницы Джин Дюпро. Действие первой части происходит в таинственном городе Эмбере, над которым никогда не восходит солнце. Тусклые электрические фонари - единственный источник света для горожан. Но фонари все чаще гаснут, и скоро город окончательно погрузится во тьму. Существуют ли где-то во мраке, окружающем Эмбер, другие острова света? Никто не знает ответа на этот вопрос, и только подростки Лина Мэйфлит и Дун Харроу найдут путь к спасению.&quot;,
            &quot;published_year&quot;: 2008,
            &quot;publisher_id&quot;: 1,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:36.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/1.jpg&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-publishers--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-publishers--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-publishers--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-publishers--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-publishers--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-publishers--id-" data-method="GET"
      data-path="api/publishers/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-publishers--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-publishers--id-"
                    onclick="tryItOut('GETapi-publishers--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-publishers--id-"
                    onclick="cancelTryOut('GETapi-publishers--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-publishers--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/publishers/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-publishers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-publishers--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-publishers--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the publisher. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-formats">GET api/formats</h2>

<p>
</p>



<span id="example-requests-GETapi-formats">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/formats" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/formats"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-formats">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">[
    {
        &quot;format_id&quot;: 1,
        &quot;format_name&quot;: &quot;PDF&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;format_id&quot;: 2,
        &quot;format_name&quot;: &quot;TXT&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    },
    {
        &quot;format_id&quot;: 3,
        &quot;format_name&quot;: &quot;FB2&quot;,
        &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;
    }
]</code>
 </pre>
    </span>
<span id="execution-results-GETapi-formats" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-formats"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-formats"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-formats" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-formats">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-formats" data-method="GET"
      data-path="api/formats"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-formats', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-formats"
                    onclick="tryItOut('GETapi-formats');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-formats"
                    onclick="cancelTryOut('GETapi-formats');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-formats"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/formats</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-formats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-formats"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-formats--id-">GET api/formats/{id}</h2>

<p>
</p>



<span id="example-requests-GETapi-formats--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/formats/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/formats/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-formats--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;format_id&quot;: 1,
    &quot;format_name&quot;: &quot;PDF&quot;,
    &quot;created_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
    &quot;updated_at&quot;: &quot;2026-03-17T11:55:34.000000Z&quot;,
    &quot;books&quot;: [
        {
            &quot;book_id&quot;: 4,
            &quot;book_title&quot;: &quot;Ларец Марии Медичи&quot;,
            &quot;description&quot;: &quot;В жизнь молодых людей вошла древняя тайна &mdash; ларец Марии Медичи и семь его загадочных &laquo;спутников&raquo;. Силою обстоятельств чудесная реликвия попадает в тесную комнату в маленьком московском переулке, с этого, собственно, и начинается цепь удивительных происшествий, одним из звеньев которой является исчезновение иностранного туриста.&quot;,
            &quot;published_year&quot;: 1972,
            &quot;publisher_id&quot;: 4,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:37.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:37.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/4.jpg&quot;,
            &quot;pivot&quot;: {
                &quot;format_id&quot;: 1,
                &quot;book_id&quot;: 4
            }
        },
        {
            &quot;book_id&quot;: 5,
            &quot;book_title&quot;: &quot;Энциклопедия начинающего автомобилиста&quot;,
            &quot;description&quot;: &quot;Чуть более ста лет назад изобретатели выпустили из бутылки джинна, имя которому &mdash; автомобиль. С невероятной быстротой расселились его потомки по всему миру. Автомобиль стал самой любимой, послушной и близкой к человеку машиной. И интерес к нему не угасает. Посмотрите, сколько людей обступает всякий раз новую, незнакомую марку. Он стал во многих семьях привычным предметом быта. Даже не верится, что было время, когда люди обходились без автомобиля. Первые автомобили многими воспринимались как проявление нечистой силы, дьявольщины или игрушки для взрослых. И тем не менее автомобиль убедительно доказал свою пригодность служить людям. Сейчас автомобиль является единым и неделимым, почти живым организмом. Только при полной работоспособности всех его составляющих автомобиль может выполнять те функции, которые возлагает на него хозяин. С помощью этой книги вы постигнете азы вождения, узнаете, как сдать экзамен в ГИБДД, разберетесь в устройстве автомобиля. Она будет полезна и при покупке автомобиля, и при его продаже. В книге вы найдете рекомендации по тому, как правильно вести себя на дороге в экстремальных ситуациях, в том числе и ДТП. Вы узнаете все о жизни автомобилиста и уходе за автомобилем.&quot;,
            &quot;published_year&quot;: 2006,
            &quot;publisher_id&quot;: 5,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:37.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:37.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/5.jpg&quot;,
            &quot;pivot&quot;: {
                &quot;format_id&quot;: 1,
                &quot;book_id&quot;: 5
            }
        },
        {
            &quot;book_id&quot;: 7,
            &quot;book_title&quot;: &quot;C++ для \&quot;чайников\&quot;&quot;,
            &quot;description&quot;: &quot;C++ для \&quot;чайников\&quot; 4-е издание. \&quot;Моим друзьям и семье, которые помогли мне стать \&quot;чайником \&quot; в еще большей степени, чем я есть на самом деле\&quot; (Стефан Р. Дэвис). Стефан Р. Дэвис (Stephen R. Davis) &mdash; автор множества книг, включая такие бестсел- леры, как C++ для \&quot;чайников \&quot;, More C++for Dummies и Windows 95 Programming for Dummies. Стефан работает в компании Valtech, специализирующейся в области обучения информатике (Даллас, Техас). Книга, которая у вас в руках, - это введение в язык программирования C++. Она начинается с азов: от читателя не требуется каких-либо знаний в области программирования. В отличие от других книг по программированию на C++, в этой книге вопрос `почему` считается не менее важным, чем вопрос `как`. И потому перед изложением конкретных особенностей языка C++ читателю разъясняется, как они действуют в целом. Ведь каждая структурная особенность языка - это отдельный штрих единой картины. Прочитав книгу, вы сможете написать на C++ вразумительную программу и, что не менее важно, будете понимать, почему и как она работает. Книга рассчитана на пользователей с различным уровнем подготовки.&quot;,
            &quot;published_year&quot;: 2003,
            &quot;publisher_id&quot;: 7,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:37.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:37.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/7.jpg&quot;,
            &quot;pivot&quot;: {
                &quot;format_id&quot;: 1,
                &quot;book_id&quot;: 7
            }
        },
        {
            &quot;book_id&quot;: 18,
            &quot;book_title&quot;: &quot;Linux. Полное руководство&quot;,
            &quot;description&quot;: &quot;Эта книга - перевод девятого издания фундаментального руководства Михаэля Кофлера, уже ставшего классическим произведением по Linux.Михаэль Кофлер открыл путь в мир свободных операционных систем для нескольких поколений пользователей Linux. Журнал Linux-Magazin причисляет его к 15 наиболее влиятельным специалистам в данной области.Книга представляет собой справочник на тему \&quot;Как это делается в Linux\&quot;, она будет полезна и актуальна для всех, кто хочет работать с Linux на ПК или на сервере.&quot;,
            &quot;published_year&quot;: 2011,
            &quot;publisher_id&quot;: 15,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:38.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:38.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/18.jpg&quot;,
            &quot;pivot&quot;: {
                &quot;format_id&quot;: 1,
                &quot;book_id&quot;: 18
            }
        },
        {
            &quot;book_id&quot;: 24,
            &quot;book_title&quot;: &quot;Ядерная энергия и ископаемое топливо&quot;,
            &quot;description&quot;: &quot;Историческая работа американского геофизика М.К.Хабберта об истощении запасов нефти. Фотокопия с оригинала авторской рукописи 1956 года.&quot;,
            &quot;published_year&quot;: 1956,
            &quot;publisher_id&quot;: 20,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:38.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:38.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/24.jpg&quot;,
            &quot;pivot&quot;: {
                &quot;format_id&quot;: 1,
                &quot;book_id&quot;: 24
            }
        },
        {
            &quot;book_id&quot;: 25,
            &quot;book_title&quot;: &quot;От Путивля до Карпат&quot;,
            &quot;description&quot;: &quot;Книга мемуаров командира Сумского партизанского соединения в Великую Отечественную войну, дважды Героя Советского Союза, генерал-майора Сидора Ковпака в литературной записи Евгения Герасимова.&quot;,
            &quot;published_year&quot;: 1945,
            &quot;publisher_id&quot;: 21,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:38.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:38.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/25.jpg&quot;,
            &quot;pivot&quot;: {
                &quot;format_id&quot;: 1,
                &quot;book_id&quot;: 25
            }
        },
        {
            &quot;book_id&quot;: 26,
            &quot;book_title&quot;: &quot;Неизвестный водород&quot;,
            &quot;description&quot;: &quot;Известные технологические процессы рассмотрены с позиции участия в них водорода, практически всегда остающегося не замеченным. Изучена роль водорода в образовании кристаллической структуры графита и сделан вывод, что все твердые углеродистые вещества содержат в своем составе водород. Поскольку явление выделения водорода при нагревании угля, кокса или графита хорошо известно, то во всех высокотемпературных процессах с их участием непременно участвует и водород. Развитие подобных представлений позволило создать новую концепцию твердофазного  восстановления металлов и спекания порошкообразных  веществ,  в  которой  водороду  отводится  роль  транспортирующего агента в газофазных транспортных химических реакциях. На основании экспериментов сделана попытка связать с ювенильным водородно-метановым флюидом образование твердых и жидких ископаемых углеродистых веществ. Изучение возможности получения водорода путем газификации не горючих углеродистых веществ, например графита, является актуальным и вносит вклад в развитие водородной энергетики. Для специалистов в области физической химии, металлургии и материаловедения.&quot;,
            &quot;published_year&quot;: 2006,
            &quot;publisher_id&quot;: 9,
            &quot;created_at&quot;: &quot;2026-03-17T11:55:38.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2026-03-17T11:55:38.000000Z&quot;,
            &quot;cover_path&quot;: &quot;covers/26.jpg&quot;,
            &quot;pivot&quot;: {
                &quot;format_id&quot;: 1,
                &quot;book_id&quot;: 26
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-formats--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-formats--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-formats--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-formats--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-formats--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-formats--id-" data-method="GET"
      data-path="api/formats/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-formats--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-formats--id-"
                    onclick="tryItOut('GETapi-formats--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-formats--id-"
                    onclick="cancelTryOut('GETapi-formats--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-formats--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/formats/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-formats--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-formats--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-formats--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the format. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-books">GET api/books</h2>

<p>
</p>



<span id="example-requests-GETapi-books">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/books" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"page\": 16,
    \"per_page\": 22,
    \"search\": \"g\",
    \"genre_id\": 16,
    \"author_id\": 16,
    \"publisher_id\": 16,
    \"sort\": \"created_at\",
    \"order\": \"asc\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/books"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "page": 16,
    "per_page": 22,
    "search": "g",
    "genre_id": 16,
    "author_id": 16,
    "publisher_id": 16,
    "sort": "created_at",
    "order": "asc"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-books">
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Ошибка валидации параметров&quot;,
    &quot;errors&quot;: {
        &quot;search&quot;: [
            &quot;Количество символов в поле search должно быть не меньше 2.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-books" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-books"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-books"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-books" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-books">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-books" data-method="GET"
      data-path="api/books"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-books', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-books"
                    onclick="tryItOut('GETapi-books');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-books"
                    onclick="cancelTryOut('GETapi-books');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-books"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/books</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-books"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-books"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-books"
               value="16"
               data-component="body">
    <br>
<p>Поле value должно быть не меньше 1. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-books"
               value="22"
               data-component="body">
    <br>
<p>Поле value должно быть не меньше 1. Поле value не может быть больше 100. Example: <code>22</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>search</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="search"                data-endpoint="GETapi-books"
               value="g"
               data-component="body">
    <br>
<p>Количество символов в поле value должно быть не меньше 2. Количество символов в поле value не может превышать 100. Example: <code>g</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>genre_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="genre_id"                data-endpoint="GETapi-books"
               value="16"
               data-component="body">
    <br>
<p>The <code>genre_id</code> of an existing record in the genres table. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>author_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="author_id"                data-endpoint="GETapi-books"
               value="16"
               data-component="body">
    <br>
<p>The <code>author_id</code> of an existing record in the authors table. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>publisher_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="publisher_id"                data-endpoint="GETapi-books"
               value="16"
               data-component="body">
    <br>
<p>The <code>publisher_id</code> of an existing record in the publishers table. Example: <code>16</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>year_from</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="year_from"                data-endpoint="GETapi-books"
               value=""
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>year_to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="year_to"                data-endpoint="GETapi-books"
               value=""
               data-component="body">
    <br>

        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sort</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="sort"                data-endpoint="GETapi-books"
               value="created_at"
               data-component="body">
    <br>
<p>Example: <code>created_at</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>book_title</code></li> <li><code>published_year</code></li> <li><code>created_at</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>order</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="order"                data-endpoint="GETapi-books"
               value="asc"
               data-component="body">
    <br>
<p>Example: <code>asc</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>asc</code></li> <li><code>desc</code></li></ul>
        </div>
        </form>

                    <h2 id="endpoints-GETapi-books--id-">GET api/books/{id}</h2>

<p>
</p>



<span id="example-requests-GETapi-books--id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/books/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/books/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-books--id-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;book_id&quot;: 1,
        &quot;book_title&quot;: &quot;Побег&quot;,
        &quot;description&quot;: &quot;Этот увлекательный роман - первая часть фантастической трилогии американской писательницы Джин Дюпро. Действие первой части происходит в таинственном городе Эмбере, над которым никогда не восходит солнце. Тусклые электрические фонари - единственный источник света для горожан. Но фонари все чаще гаснут, и скоро город окончательно погрузится во тьму. Существуют ли где-то во мраке, окружающем Эмбер, другие острова света? Никто не знает ответа на этот вопрос, и только подростки Лина Мэйфлит и Дун Харроу найдут путь к спасению.&quot;,
        &quot;published_year&quot;: 2008,
        &quot;cover_url&quot;: &quot;/api/covers/1.jpg&quot;,
        &quot;is_favorited&quot;: false,
        &quot;genres&quot;: [
            {
                &quot;genre_id&quot;: 1,
                &quot;genre_name&quot;: &quot;Антиутопия&quot;
            }
        ],
        &quot;authors&quot;: [
            {
                &quot;author_id&quot;: 1,
                &quot;last_name&quot;: &quot;Дюпро&quot;,
                &quot;first_name&quot;: &quot;Джин&quot;,
                &quot;middle_name&quot;: null
            }
        ],
        &quot;publisher&quot;: {
            &quot;publisher_id&quot;: 1,
            &quot;publisher_name&quot;: &quot;Махаон&quot;
        },
        &quot;files&quot;: [
            {
                &quot;file_id&quot;: 2,
                &quot;format_id&quot;: 2,
                &quot;format_name&quot;: &quot;TXT&quot;,
                &quot;file_size_bytes&quot;: null,
                &quot;file_size_mb&quot;: 0,
                &quot;read_url&quot;: &quot;/api/books/file/2/read&quot;,
                &quot;download_url&quot;: &quot;/api/books/file/2/download&quot;
            },
            {
                &quot;file_id&quot;: 1,
                &quot;format_id&quot;: 3,
                &quot;format_name&quot;: &quot;FB2&quot;,
                &quot;file_size_bytes&quot;: null,
                &quot;file_size_mb&quot;: 0,
                &quot;read_url&quot;: &quot;/api/books/file/1/read&quot;,
                &quot;download_url&quot;: &quot;/api/books/file/1/download&quot;
            }
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-books--id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-books--id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-books--id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-books--id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-books--id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-books--id-" data-method="GET"
      data-path="api/books/{id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-books--id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-books--id-"
                    onclick="tryItOut('GETapi-books--id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-books--id-"
                    onclick="cancelTryOut('GETapi-books--id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-books--id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/books/{id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-books--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-books--id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="id"                data-endpoint="GETapi-books--id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the book. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-covers--filename-">Получить обложку книги (доступно всем)</h2>

<p>
</p>



<span id="example-requests-GETapi-covers--filename-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/covers/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/covers/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-covers--filename-">
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Обложка не найдена&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-covers--filename-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-covers--filename-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-covers--filename-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-covers--filename-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-covers--filename-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-covers--filename-" data-method="GET"
      data-path="api/covers/{filename}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-covers--filename-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-covers--filename-"
                    onclick="tryItOut('GETapi-covers--filename-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-covers--filename-"
                    onclick="cancelTryOut('GETapi-covers--filename-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-covers--filename-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/covers/{filename}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-covers--filename-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-covers--filename-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>filename</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="filename"                data-endpoint="GETapi-covers--filename-"
               value="architecto"
               data-component="url">
    <br>
<p>Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-user">GET api/user</h2>

<p>
</p>



<span id="example-requests-GETapi-user">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/user" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/user"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-user">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-user" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-user"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-user"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-user">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-user" data-method="GET"
      data-path="api/user"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-user', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-user"
                    onclick="tryItOut('GETapi-user');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-user"
                    onclick="cancelTryOut('GETapi-user');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-user"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/user</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-books-file--fileId--read">Читать файл книги в браузере (только авторизованные)</h2>

<p>
</p>



<span id="example-requests-GETapi-books-file--fileId--read">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/books/file/architecto/read" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/books/file/architecto/read"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-books-file--fileId--read">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-books-file--fileId--read" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-books-file--fileId--read"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-books-file--fileId--read"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-books-file--fileId--read" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-books-file--fileId--read">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-books-file--fileId--read" data-method="GET"
      data-path="api/books/file/{fileId}/read"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-books-file--fileId--read', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-books-file--fileId--read"
                    onclick="tryItOut('GETapi-books-file--fileId--read');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-books-file--fileId--read"
                    onclick="cancelTryOut('GETapi-books-file--fileId--read');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-books-file--fileId--read"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/books/file/{fileId}/read</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-books-file--fileId--read"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-books-file--fileId--read"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>fileId</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fileId"                data-endpoint="GETapi-books-file--fileId--read"
               value="architecto"
               data-component="url">
    <br>
<p>Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-books-file--fileId--download">Скачать файл книги (только авторизованные)</h2>

<p>
</p>



<span id="example-requests-GETapi-books-file--fileId--download">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/books/file/architecto/download" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/books/file/architecto/download"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-books-file--fileId--download">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-books-file--fileId--download" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-books-file--fileId--download"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-books-file--fileId--download"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-books-file--fileId--download" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-books-file--fileId--download">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-books-file--fileId--download" data-method="GET"
      data-path="api/books/file/{fileId}/download"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-books-file--fileId--download', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-books-file--fileId--download"
                    onclick="tryItOut('GETapi-books-file--fileId--download');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-books-file--fileId--download"
                    onclick="cancelTryOut('GETapi-books-file--fileId--download');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-books-file--fileId--download"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/books/file/{fileId}/download</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-books-file--fileId--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-books-file--fileId--download"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>fileId</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="fileId"                data-endpoint="GETapi-books-file--fileId--download"
               value="architecto"
               data-component="url">
    <br>
<p>Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-favorites">GET api/favorites</h2>

<p>
</p>



<span id="example-requests-GETapi-favorites">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://localhost/api/favorites" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/favorites"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-favorites">
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
access-control-allow-origin: http://localhost:3000
access-control-allow-credentials: true
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Требуется авторизация&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-favorites" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-favorites"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-favorites"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-favorites" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-favorites">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-favorites" data-method="GET"
      data-path="api/favorites"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-favorites', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-favorites"
                    onclick="tryItOut('GETapi-favorites');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-favorites"
                    onclick="cancelTryOut('GETapi-favorites');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-favorites"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/favorites</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-favorites"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-favorites"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-favorites--book-">POST api/favorites/{book}</h2>

<p>
</p>



<span id="example-requests-POSTapi-favorites--book-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "http://localhost/api/favorites/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/favorites/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-favorites--book-">
</span>
<span id="execution-results-POSTapi-favorites--book-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-favorites--book-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-favorites--book-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-favorites--book-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-favorites--book-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-favorites--book-" data-method="POST"
      data-path="api/favorites/{book}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-favorites--book-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-favorites--book-"
                    onclick="tryItOut('POSTapi-favorites--book-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-favorites--book-"
                    onclick="cancelTryOut('POSTapi-favorites--book-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-favorites--book-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/favorites/{book}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-favorites--book-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-favorites--book-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>book</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="book"                data-endpoint="POSTapi-favorites--book-"
               value="architecto"
               data-component="url">
    <br>
<p>Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-DELETEapi-favorites--book-">DELETE api/favorites/{book}</h2>

<p>
</p>



<span id="example-requests-DELETEapi-favorites--book-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "http://localhost/api/favorites/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://localhost/api/favorites/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-favorites--book-">
</span>
<span id="execution-results-DELETEapi-favorites--book-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-favorites--book-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-favorites--book-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-favorites--book-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-favorites--book-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-favorites--book-" data-method="DELETE"
      data-path="api/favorites/{book}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-favorites--book-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-favorites--book-"
                    onclick="tryItOut('DELETEapi-favorites--book-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-favorites--book-"
                    onclick="cancelTryOut('DELETEapi-favorites--book-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-favorites--book-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/favorites/{book}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-favorites--book-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-favorites--book-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>book</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="book"                data-endpoint="DELETEapi-favorites--book-"
               value="architecto"
               data-component="url">
    <br>
<p>Example: <code>architecto</code></p>
            </div>
                    </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
