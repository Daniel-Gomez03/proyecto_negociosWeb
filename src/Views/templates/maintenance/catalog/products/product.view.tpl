<section class="container-m row px-4 py-4">
    <h1>{{formTitle}}</h1>
</section>
<section class="container-m row px-4 py-4">
    {{with product}}
    <form action="index.php?page=Maintenance_Catalog_Products_Product&mode={{mode}}&productId={{productId}}" method="POST"
        class="col-12 col-m-8 offset-m-2">
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productIdD">Código</label>
            <input class="col-12 col-m-9" readonly disabled type="text" name="productIdD" id="productIdD"
                placeholder="Código" value="{{productId}}" />
            <input type="hidden" name="mode" value="{{mode}}" />
            <input type="hidden" name="productId" value="{{productId}}" />
            <input type="hidden" name="token" value="{{product_xss_token}}" />
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productName">Producto</label>
            <input class="col-12 col-m-9" {{readonly}} type="text" name="productName" id="productName"
                placeholder="Nombre del Producto" value="{{productName}}" />
            {{if productName_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{productName_error}}
            </div>
            {{endif productName_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productPrice">Precio</label>
            <input class="col-12 col-m-9" {{readonly}} type="number" step="0.01" name="productPrice" id="productPrice"
                placeholder="Precio" value="{{productPrice}}" />
            {{if productPrice_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{productPrice_error}}
            </div>
            {{endif productPrice_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productStock">Stock</label>
            <input class="col-12 col-m-9" {{readonly}} type="number" name="productStock" id="productStock"
                placeholder="Stock disponible" value="{{productStock}}" />
            {{if productStock_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{productStock_error}}
            </div>
            {{endif productStock_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productBrandId">Marca</label>
            <select class="col-12 col-m-9" name="productBrandId" id="productBrandId" {{readonly}}>
                <option value="">Seleccione una marca</option>
                {{foreach brands}}
                <option value="{{value}}" {{selected}}>{{text}}</option>
                {{endfor brands}}
            </select>
            {{if productBrandId_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{productBrandId_error}}
            </div>
            {{endif productBrandId_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productCategoryId">Categoría</label>
            <select class="col-12 col-m-9" name="productCategoryId" id="productCategoryId" {{readonly}}>
                <option value="">Seleccione una categoría</option>
                {{foreach categories}}
                <option value="{{value}}" {{selected}}>{{text}}</option>
                {{endfor categories}}
            </select>
            {{if productCategoryId_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{productCategoryId_error}}
            </div>
            {{endif productCategoryId_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productType">Tipo de Producto</label>
            <select class="col-12 col-m-9" name="productType" id="productType" {{readonly}} onchange="showSpecificFields()">
                <option value="">Seleccione un tipo</option>
                <option value="gunpla" {{if productType === 'gunpla'}}selected{{endif productType}}>Gunpla</option>
                <option value="lego" {{if productType === 'lego'}}selected{{endif productType}}>LEGO</option>
                <option value="blokees" {{if productType === 'blokees'}}selected{{endif productType}}>Blokees</option>
            </select>
        </div>

        <!-- Campos específicos para Gunpla -->
        <div id="gunplaFields" style="display: {{if productType === 'gunpla'}}block{{else}}none{{endif productType}};">
            <div class="row my-2 align-center">
                <label class="col-12 col-m-3" for="gunplaGrade">Grado</label>
                <input class="col-12 col-m-9" {{readonly}} type="text" name="gunplaGrade" id="gunplaGrade"
                    placeholder="Ej: Master Grade, High Grade" value="{{gunplaGrade}}" />
                {{if gunplaGrade_error}}
                <div class="col-12 col-m-9 offset-m-3 error">
                    {{gunplaGrade_error}}
                </div>
                {{endif gunplaGrade_error}}
            </div>
            <div class="row my-2 align-center">
                <label class="col-12 col-m-3" for="gunplaScale">Escala</label>
                <input class="col-12 col-m-9" {{readonly}} type="text" name="gunplaScale" id="gunplaScale"
                    placeholder="Ej: 1/144, 1/100" value="{{gunplaScale}}" />
                {{if gunplaScale_error}}
                <div class="col-12 col-m-9 offset-m-3 error">
                    {{gunplaScale_error}}
                </div>
                {{endif gunplaScale_error}}
            </div>
            <div class="row my-2 align-center">
                <label class="col-12 col-m-3">Características</label>
                <div class="col-12 col-m-9">
                    <label>
                        <input type="checkbox" name="gunplaPremiumBandai" {{if gunplaPremiumBandai}}checked{{endif gunplaPremiumBandai}} {{readonly}} />
                        Premium Bandai
                    </label>
                    &nbsp;
                    <label>
                        <input type="checkbox" name="gunplaGundamBase" {{if gunplaGundamBase}}checked{{endif gunplaGundamBase}} {{readonly}} />
                        Gundam Base
                    </label>
                </div>
            </div>
        </div>

        <!-- Campos específicos para LEGO -->
        <div id="legoFields" style="display: {{if productType === 'lego'}}block{{else}}none{{endif productType}};">
            <div class="row my-2 align-center">
                <label class="col-12 col-m-3" for="legoLine">Línea</label>
                <input class="col-12 col-m-9" {{readonly}} type="text" name="legoLine" id="legoLine"
                    placeholder="Ej: Star Wars, Harry Potter" value="{{legoLine}}" />
                {{if legoLine_error}}
                <div class="col-12 col-m-9 offset-m-3 error">
                    {{legoLine_error}}
                </div>
                {{endif legoLine_error}}
            </div>
            <div class="row my-2 align-center">
                <label class="col-12 col-m-3" for="legoSetNumber">Número de Set</label>
                <input class="col-12 col-m-9" {{readonly}} type="text" name="legoSetNumber" id="legoSetNumber"
                    placeholder="Ej: 75375, 71043" value="{{legoSetNumber}}" />
                {{if legoSetNumber_error}}
                <div class="col-12 col-m-9 offset-m-3 error">
                    {{legoSetNumber_error}}
                </div>
                {{endif legoSetNumber_error}}
            </div>
            <div class="row my-2 align-center">
                <label class="col-12 col-m-3" for="legoPieceCount">Piezas</label>
                <input class="col-12 col-m-9" {{readonly}} type="number" name="legoPieceCount" id="legoPieceCount"
                    placeholder="Número de piezas" value="{{legoPieceCount}}" />
                {{if legoPieceCount_error}}
                <div class="col-12 col-m-9 offset-m-3 error">
                    {{legoPieceCount_error}}
                </div>
                {{endif legoPieceCount_error}}
            </div>
        </div>

        <!-- Campos específicos para Blokees -->
        <div id="blokeesFields" style="display: {{if productType === 'blokees'}}block{{else}}none{{endif productType}};">
            <div class="row my-2 align-center">
                <label class="col-12 col-m-3" for="blokeesVersion">Versión</label>
                <input class="col-12 col-m-9" {{readonly}} type="text" name="blokeesVersion" id="blokeesVersion"
                    placeholder="Ej: Edición Estándar, Deluxe" value="{{blokeesVersion}}" />
                {{if blokeesVersion_error}}
                <div class="col-12 col-m-9 offset-m-3 error">
                    {{blokeesVersion_error}}
                </div>
                {{endif blokeesVersion_error}}
            </div>
            <div class="row my-2 align-center">
                <label class="col-12 col-m-3" for="blokeesSize">Tamaño</label>
                <input class="col-12 col-m-9" {{readonly}} type="text" name="blokeesSize" id="blokeesSize"
                    placeholder="Ej: Pequeño (12cm), Grande (25cm)" value="{{blokeesSize}}" />
                {{if blokeesSize_error}}
                <div class="col-12 col-m-9 offset-m-3 error">
                    {{blokeesSize_error}}
                </div>
                {{endif blokeesSize_error}}
            </div>
        </div>

        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productDescription">Descripción</label>
            <textarea class="col-12 col-m-9" {{readonly}} name="productDescription" id="productDescription"
                placeholder="Descripción del Producto">{{productDescription}}</textarea>
            {{if productDescription_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{productDescription_error}}
            </div>
            {{endif productDescription_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productImgUrl">Url de Imagen</label>
            <input class="col-12 col-m-9" {{readonly}} type="text" name="productImgUrl" id="productImgUrl"
                placeholder="URL de la imagen" value="{{productImgUrl}}" />
            {{if productImgUrl_error}}
            <div class="col-12 col-m-9 offset-m-3 error">
                {{productImgUrl_error}}
            </div>
            {{endif productImgUrl_error}}
        </div>
        <div class="row my-2 align-center">
            <label class="col-12 col-m-3" for="productStatus">Estado</label>
            <select name="productStatus" id="productStatus" class="col-12 col-m-9" {{if readonly}} readonly disabled
                {{endif readonly}}>
                <option value="ACT" {{productStatus_act}}>Activo</option>
                <option value="INA" {{productStatus_ina}}>Inactivo</option>
            </select>
        </div>
        {{endwith product}}
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
            window.location.assign("index.php?page=Maintenance_Catalog_Products_Products");
        });
    });
</script>