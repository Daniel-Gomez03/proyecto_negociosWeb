<section class="container-m row px-4 py-4">
  <div class="col-12 col-m-8 offset-m-2">
    <div class="error-container">
      <h1 class="error-title">
        <i class="fas fa-exclamation-triangle"></i> Oops
      </h1>
      <p class="error-message">Error {{CLIENT_ERROR_CODE}}</p>
      <p class="error-details">{{CLIENT_ERROR_MSG}}</p>

      {{if DEVELOPMENT}}
      <hr />
      <h2>{{ERROR_CODE}}</h2>
      <h3>{{ERROR_MSG}}</h3>
      <hr />
      {{endif DEVELOPMENT}}
    </div>
  </div>
</section>