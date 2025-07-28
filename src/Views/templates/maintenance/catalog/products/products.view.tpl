<h1>Administración de Productos</h1>
<section class="grid">
    <div class="row">
        <form class="col-12 col-m-8" action="index.php" method="get">
            <div class="flex align-center">
                <div class="col-8 row">
                    <input type="hidden" name="page" value="Maintenance_Catalog_Products_Products">
                    <label class="col-3" for="partialName">Nombre</label>
                    <input class="col-9" type="text" name="partialName" id="partialName" value="{{partialName}}" />
                    <label class="col-3" for="status">Estado</label>
                    <select class="col-9" name="status" id="status">
                        <option value="EMP" {{statusEMP}}>Todos</option>
                        <option value="ACT" {{statusACT}}>Active</option>
                        <option value="INA" {{statusINA}}>Inactive</option>
                    </select>
                </div>
                <div class="col-4 align-end">
                    <button type="submit">Filtrar</button>
                </div>
            </div>
        </form>
    </div>
</section>
<section class="WWList">
    <table>
        <thead>
            <tr>
                <th> 
                    {{ifnot OrderByProductId}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=productId&orderDescending=0">Id <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByProductId}}
                    {{if OrderProductIdDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=clear&orderDescending=0">Id <i class="fas fa-sort-down"></i></a>
                    {{endif OrderProductIdDesc}}
                    {{if OrderProductId}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=productId&orderDescending=1">Id <i class="fas fa-sort-up"></i></a>
                    {{endif OrderProductId}}
                </th>
                <th class="left">
                    {{ifnot OrderByProductName}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=productName&orderDescending=0">Nombre <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByProductName}}
                    {{if OrderProductNameDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=clear&orderDescending=0">Nombre <i class="fas fa-sort-down"></i></a>
                    {{endif OrderProductNameDesc}}
                    {{if OrderProductName}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=productName&orderDescending=1">Nombre <i class="fas fa-sort-up"></i></a>
                    {{endif OrderProductName}}
                </th>
                <th>
                    {{ifnot OrderByProductPrice}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=productPrice&orderDescending=0">Precio <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByProductPrice}}
                    {{if OrderProductPriceDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=clear&orderDescending=0">Precio <i class="fas fa-sort-down"></i></a>
                    {{endif OrderProductPriceDesc}}
                    {{if OrderProductPrice}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=productPrice&orderDescending=1">Precio <i class="fas fa-sort-up"></i></a>
                    {{endif OrderProductPrice}}
                </th>
                <th class="left">
                    {{ifnot OrderByBrandName}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=brandName&orderDescending=0">Marca <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByBrandName}}
                    {{if OrderBrandNameDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=clear&orderDescending=0">Marca <i class="fas fa-sort-down"></i></a>
                    {{endif OrderBrandNameDesc}}
                    {{if OrderBrandName}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=brandName&orderDescending=1">Marca <i class="fas fa-sort-up"></i></a>
                    {{endif OrderBrandName}}
                </th>
                <th class="left">
                    {{ifnot OrderByCategoryName}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=categoryName&orderDescending=0">Categoría <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByCategoryName}}
                    {{if OrderCategoryNameDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=clear&orderDescending=0">Categoría <i class="fas fa-sort-down"></i></a>
                    {{endif OrderCategoryNameDesc}}
                    {{if OrderCategoryName}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=categoryName&orderDescending=1">Categoría <i class="fas fa-sort-up"></i></a>
                    {{endif OrderCategoryName}}
                </th>
                <th class="left">
                    {{ifnot OrderByProductType}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=productType&orderDescending=0">Tipo <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByProductType}}
                    {{if OrderProductTypeDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=clear&orderDescending=0">Tipo <i class="fas fa-sort-down"></i></a>
                    {{endif OrderProductTypeDesc}}
                    {{if OrderProductType}}
                        <a href="index.php?page=Maintenance_Catalog_Products_Products&orderBy=productType&orderDescending=1">Tipo <i class="fas fa-sort-up"></i></a>
                    {{endif OrderProductType}}
                </th>
                <th>Stock</th>
                <th>Estado</th>
                <th><a href="index.php?page=Maintenance_Catalog_Products_Product&mode=INS">Nuevo</a></th>
            </tr>
        </thead>
        <tbody>
            {{foreach products}}
                <tr>
                    <td>{{productId}}</td>
                    <td>
                        <a class="link" href="index.php?page=Maintenance_Catalog_Products_Product&mode=DSP&productId={{productId}}">
                            {{productName}}
                        </a>
                    </td>
                    <td class="right">${{productPrice}}</td>
                    <td>{{brandName}}</td>
                    <td>{{categoryName}}</td>
                    <td>{{productType}}</td>
                    <td class="center">{{productStock}}</td>
                    <td class="center">{{productStatusDsc}}</td>
                    <td class="center">
                        <a href="index.php?page=Maintenance_Catalog_Products_Product&mode=UPD&productId={{productId}}">Editar</a>
                        &nbsp;
                        <a href="index.php?page=Maintenance_Catalog_Products_Product&mode=DEL&productId={{productId}}">Eliminar</a>
                    </td>
                </tr>
            {{endfor products}}
        </tbody>
    </table>
    {{pagination}}
</section>