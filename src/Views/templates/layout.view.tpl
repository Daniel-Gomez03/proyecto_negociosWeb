<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{SITE_TITLE}}</title>
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{BASE_DIR}}/public/css/appstyle.css" />
  <script src="https://kit.fontawesome.com/{{FONT_AWESOME_KIT}}.js" crossorigin="anonymous"></script>
  {{foreach SiteLinks}}
  <link rel="stylesheet" href="{{~BASE_DIR}}/{{this}}" />
  {{endfor SiteLinks}}
  {{foreach BeginScripts}}
  <script src="{{~BASE_DIR}}/{{this}}"></script>
  {{endfor BeginScripts}}
</head>

<body>
  <header>
    <input type="checkbox" class="menu_toggle" id="menu_toggle" />
    <label for="menu_toggle" class="menu_toggle_icon">
      <div class="hmb dgn pt-1"></div>
      <div class="hmb hrz"></div>
      <div class="hmb dgn pt-2"></div>
    </label>
    <h1>{{SITE_TITLE}}</h1>
    <nav id="menu">
      <ul>
        <li><a href="index.php?page={{PUBLIC_DEFAULT_CONTROLLER}}"><i class="fas fa-home"></i>&nbsp;Inicio</a>
        </li>
        {{foreach PUBLIC_NAVIGATION}}
        <li><a href="{{nav_url}}">{{nav_label}}</a></li>
        {{endfor PUBLIC_NAVIGATION}}
      </ul>
    </nav>
    <span>{{if ~CART_ITEMS}}<i class="fa-solid fa-cart-shopping"></i>{{~CART_ITEMS}}{{endif ~CART_ITEMS}}</span>
  </header>
  <main>
    {{{page_content}}}
  </main>
  <footer class="main-footer" id="contacto">
    <div class="footer-content">
      <div class="footer-section">
        <h4>{{SITE_TITLE}}</h4>
        <p>Especialistas en modelismo y coleccionables</p>
        <p>Tegucigalpa, Honduras</p>
      </div>

      <div class="footer-section">
        <h4>Contacto</h4>
        <p><i class="fas fa-envelope"></i> info@hasbunshop.com</p>
        <p><i class="fas fa-phone"></i> +504 9999-9999</p>
      </div>

      <div class="footer-section">
        <h4>Síguenos</h4>
        <div class="social-links">
          <a href="https://www.facebook.com/hasbun.sh/" target="_blank">
            <i class="fab fa-facebook"></i>
          </a>
          <a href="https://www.instagram.com/hasbun_shop/" target="_blank">
            <i class="fab fa-instagram"></i>
          </a>
        </div>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; {{~CURRENT_YEAR}} {{SITE_TITLE}}. Todos los derechos reservados.</p>
    </div>
  </footer>
  {{foreach EndScripts}}
  <script src="/{{~BASE_DIR}}/{{this}}"></script>
  {{endfor EndScripts}}
</body>

</html>