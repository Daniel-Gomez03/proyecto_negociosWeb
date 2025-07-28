<section class="container-m row px-4 py-4">
    <h1>{{formTitle}}</h1>
</section>
<section class="container-m row px-4 py-4">
    {{with category}}
    <form action="index.php?page=Maintenance_Catalog_Categories_Category&mode={{mode}}&categoryId={{categoryId}}" method="POST"
        class="col-12 col-m-8 offset-m-2">
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="categoryIdD">Código</label>
            <input class="col-12 col-m-9" readonly disabled type="text" name="categoryIdD" id="categoryIdD"
                placeholder="Código" value="{{categoryId}}" />
            <input type="hidden" name="mode" value="{{mode}}" />
            <input type="hidden" name="categoryId" value="{{categoryId}}" />
            <input type="hidden" name="token" value="{{category_xss_token}}" />
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="categoryName">Nombre</label>
            <input class="col-12 col-m-9" {{readonly}} type="text" name="categoryName" id="categoryName"
                placeholder="Nombre de la categoría" value="{{categoryName}}" />
            {{if categoryName_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{categoryName_error}}
            </div>
            {{endif categoryName_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="categoryDescription">Descripción</label>
            <textarea class="col-12 col-m-9" {{readonly}} name="categoryDescription" id="categoryDescription"
                placeholder="Descripción de la categoría">{{categoryDescription}}</textarea>
            {{if categoryDescription_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{categoryDescription_error}}
            </div>
            {{endif categoryDescription_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="categoryStatus">Estado</label>
            <select name="categoryStatus" id="categoryStatus" class="col-12 col-m-9" {{if readonly}} readonly disabled
                {{endif readonly}}>
                <option value="ACT" {{categoryStatus_act}}>Activo</option>
                <option value="INA" {{categoryStatus_ina}}>Inactivo</option>
            </select>
        </div>
        {{endwith category}}
        <div class="row my-4 align-center flex-end">
            {{if showCommitBtn}}
            <button class="primary col-12 col-m-2" type="submit" name="btnConfirmar">Confirmar</button>
            &nbsp;
            {{endif showCommitBtn}}
            <button class="col-12 col-m-2" type="button" id="btnCancelar">
                {{if showCommitBtn}}
                Cancelar
                {{endif showCommitBtn}}
                {{ifnot showCommitBtn}}
                Regresar
                {{endifnot showCommitBtn}}
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
            window.location.assign("index.php?page=Maintenance_Catalog_Categories_Categories");
        });
    });  
</script>