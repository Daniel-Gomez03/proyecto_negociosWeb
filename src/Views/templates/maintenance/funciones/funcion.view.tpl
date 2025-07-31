<section class="container-m row px-4 py-4">
  <h1>{{FormTitle}}</h1>
</section>

<section class="container-m row px-4 py-4">
  {{with funcion}}
  <form action="index.php?page=Maintenance_Funciones_Funciones&mode={{~mode}}&id={{fncod}}" method="POST"
    class="col-12 col-m-8 offset-m-2">

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="fncodD">Código</label>
      <input class="col-12 col-m-9" readonly disabled type="text" name="fncodD" id="fncodD"
        value="{{fncod}}" />
      <input type="hidden" name="mode" value="{{~mode}}" />
      <input type="hidden" name="fncod" value="{{fncod}}" />
      <input type="hidden" name="function_xss_token" value="{{~function_xss_token}}" />
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="funcdsc">Descripción</label>
      <input class="col-12 col-m-9" {{~readonly}} type="text" name="funcdsc" id="funcdsc"
        placeholder="Descripción de la Función" value="{{funcdsc}}" />
      {{if funcdsc_error}}
      <div class="col-12 col-m-9 offset-m-3 error">
        {{funcdsc_error}}
      </div>
      {{endif funcdsc_error}}
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="funcest">Estado</label>
      <select name="funcest" id="funcest" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        <option value="ACT" {{funcest_act}}>Activo</option>
        <option value="INA" {{funcest_ina}}>Inactivo</option>
      </select>
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="fntyp">Tipo</label>
      <select name="fntyp" id="fntyp" class="col-12 col-m-9" {{if ~readonly}} readonly disabled {{endif ~readonly}}>
        <option value="PRV" {{fntyp_prv}}>Privado</option>
        <option value="PUB" {{fntyp_pub}}>Público</option>
      </select>
    </div>
  {{endwith funcion}}

    <div class="row my-4 align-center flex-end">
      {{if showCommitBtn}}
      <button class="primary col-12 col-m-2" type="submit" name="btnConfirmar">Confirmar</button>
      &nbsp;
      {{endif showCommitBtn}}
      <button class="col-12 col-m-2" type="button" id="btnCancelar">
        {{if showCommitBtn}} Cancelar {{endif showCommitBtn}}
        {{ifnot showCommitBtn}} Regresar {{endifnot showCommitBtn}}
      </button>
    </div>
  </form>
</section>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const btnCancelar = document.getElementById("btnCancelar");
    btnCancelar.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      window.location.assign("index.php?page=Maintenance_Funciones_Funciones");
    });
  });
</script>