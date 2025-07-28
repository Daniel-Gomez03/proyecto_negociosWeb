<section class="container-m row px-4 py-4">
    <h1>{{formTitle}}</h1>
</section>
<section class="container-m row px-4 py-4">
    {{with brand}}
    <form action="index.php?page=Maintenance_Catalog_Brands_Brand&mode={{mode}}&brandId={{brandId}}" method="POST"
        class="col-12 col-m-8 offset-m-2">
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="brandIdD">Código</label>
            <input class="col-12 col-m-9" readonly disabled type="text" name="brandIdD" id="brandIdD"
                placeholder="Código" value="{{brandId}}" />
            <input type="hidden" name="mode" value="{{mode}}" />
            <input type="hidden" name="brandId" value="{{brandId}}" />
            <input type="hidden" name="token" value="{{brand_xss_token}}" />
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="brandName">Nombre</label>
            <input class="col-12 col-m-9" {{readonly}} type="text" name="brandName" id="brandName"
                placeholder="Nombre de la marca" value="{{brandName}}" />
            {{if brandName_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{brandName_error}}
            </div>
            {{endif brandName_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="brandDescription">Descripción</label>
            <textarea class="col-12 col-m-9" {{readonly}} name="brandDescription" id="brandDescription"
                placeholder="Descripción de la marca">{{brandDescription}}</textarea>
            {{if brandDescription_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{brandDescription_error}}
            </div>
            {{endif brandDescription_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="brandStatus">Estado</label>
            <select name="brandStatus" id="brandStatus" class="col-12 col-m-9" {{if readonly}} readonly disabled
                {{endif readonly}}>
                <option value="ACT" {{brandStatus_act}}>Activo</option>
                <option value="INA" {{brandStatus_ina}}>Inactivo</option>
            </select>
        </div>
        {{endwith brand}}
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
            window.location.assign("index.php?page=Maintenance_Catalog_Brands_Brands");
        });
    });  
</script>