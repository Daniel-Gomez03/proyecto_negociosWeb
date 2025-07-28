<section class="container-m row px-4 py-4">
  <h1>{{FormTitle}}</h1>
</section>

<section class="container-m row px-4 py-4">
  {{with product}}
  <form action="index.php?page=Products_Product&mode={{~mode}}&productId={{productId}}" method="POST" class="col-12 col-m-8 offset-m-2">

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="productIdD">Código</label>
      <input class="col-12 col-m-9" readonly disabled type="text" name="productIdD" id="productIdD" placeholder="Código" value="{{productId}}" />
      <input type="hidden" name="mode" value="{{~mode}}" />
      <input type="hidden" name="productId" value="{{productId}}" />
      <input type="hidden" name="token" value="{{~product_xss_token}}" />
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="productName">Producto</label>
      <input class="col-12 col-m-9" {{~readonly}} type="text" name="productName" id="productName" placeholder="Nombre del Producto" value="{{productName}}" />
      {{if productName_error}}
      <div class="col-12 col-m-9 offset-m-3 error">{{productName_error}}</div>
      {{endif productName_error}}
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="productDescription">Descripción</label>
      <textarea class="col-12 col-m-9" {{~readonly}} name="productDescription" id="productDescription" placeholder="Descripción del Producto">{{productDescription}}</textarea>
      {{if productDescription_error}}
      <div class="col-12 col-m-9 offset-m-3 error">{{productDescription_error}}</div>
      {{endif productDescription_error}}
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="productPrice">Precio</label>
      <input class="col-12 col-m-9" {{~readonly}} type="number" step="0.01" name="productPrice" id="productPrice" placeholder="Precio" value="{{productPrice}}" />
      {{if productPrice_error}}
      <div class="col-12 col-m-9 offset-m-3 error">{{productPrice_error}}</div>
      {{endif productPrice_error}}
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="productStock">Stock</label>
      <input class="col-12 col-m-9" {{~readonly}} type="number" name="productStock" id="productStock" placeholder="Stock" value="{{productStock}}" />
      {{if productStock_error}}
      <div class="col-12 col-m-9 offset-m-3 error">{{productStock_error}}</div>
      {{endif productStock_error}}
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="productBrandId">Marca</label>
      <select class="col-12 col-m-9" name="productBrandId" id="productBrandId" {{if ~readonly}} disabled {{endif ~readonly}}>
        <option value="0">-- Seleccione una marca --</option>
        <option value="1" {{selectedBrand_1}}>Marca 1</option>
        <option value="2" {{selectedBrand_2}}>Marca 2</option>
        <!-- Aquí deberías reemplazar por un loop dinámico para marcas -->
      </select>
      {{if productBrandId_error}}
      <div class="col-12 col-m-9 offset-m-3 error">{{productBrandId_error}}</div>
      {{endif productBrandId_error}}
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="productCategoryId">Categoría</label>
      <select class="col-12 col-m-9" name="productCategoryId" id="productCategoryId" {{if ~readonly}} disabled {{endif ~readonly}}>
        <option value="0">-- Seleccione una categoría --</option>
        <option value="1" {{selectedCategory_1}}>Categoría 1</option>
        <option value="2" {{selectedCategory_2}}>Categoría 2</option>
        <!-- Aquí también reemplazar por un loop dinámico para categorías -->
      </select>
      {{if productCategoryId_error}}
      <div class="col-12 col-m-9 offset-m-3 error">{{productCategoryId_error}}</div>
      {{endif productCategoryId_error}}
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="productImgUrl">URL de Imagen (250x200)</label>
      <input class="col-12 col-m-9" {{~readonly}} type="text" name="productImgUrl" id="productImgUrl" placeholder="URL de Imagen" value="{{productImgUrl}}" />
      {{if productImgUrl_error}}
      <div class="col-12 col-m-9 offset-m-3 error">{{productImgUrl_error}}</div>
      {{endif productImgUrl_error}}
    </div>

    <div class="row my-2 align-center">
      <label class="col-12 col-m-3" for="productStatus">Estado</label>
      <select name="productStatus" id="productStatus" class="col-12 col-m-9" {{if ~readonly}} disabled {{endif ~readonly}}>
        <option value="ACT" {{productStatus_act}}>Activo</option>
        <option value="INA" {{productStatus_ina}}>Inactivo</option>
      </select>
      {{if productStatus_error}}
      <div class="col-12 col-m-9 offset-m-3 error">{{productStatus_error}}</div>
      {{endif productStatus_error}}
    </div>

    <div class="row my-4 align-center flex-end">
      {{if showCommitBtn}}
      <button class="primary col-12 col-m-2" type="submit" name="btnConfirmar">Confirmar</button>
      &nbsp;
      {{endif showCommitBtn}}
      <button class="col-12 col-m-2" type="button" id="btnCancelar">
        {{if showCommitBtn}}Cancelar{{endif showCommitBtn}}
        {{ifnot showCommitBtn}}Regresar{{endif showCommitBtn}}
      </button>
    </div>

  </form>
  {{endwith}}

</section>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const btnCancelar = document.getElementById("btnCancelar");
    btnCancelar.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      window.location.assign("index.php?page=Products_Products");
    });
  });
</script>
