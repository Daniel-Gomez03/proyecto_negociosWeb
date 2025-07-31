<h1>Administración de Marcas</h1>
<section class="grid">
    <div class="row">
        <form class="col-12 col-m-8" action="index.php" method="get">
            <div class="flex align-center">
                <div class="col-8 row">
                    <input type="hidden" name="page" value="Maintenance_Catalog_Brands_Brands">
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
                Id
                    {{ifnot OrderByBrandId}}
                        <a href="index.php?page=Maintenance_Catalog_Brands_Brands&orderBy=brandId&orderDescending=0">Id <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByBrandId}}
                    {{if OrderBrandIdDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Brands_Brands&orderBy=clear&orderDescending=0">Id <i class="fas fa-sort-down"></i></a>
                    {{endif OrderBrandIdDesc}}
                    {{if OrderBrandId}}
                        <a href="index.php?page=Maintenance_Catalog_Brands_Brands&orderBy=brandId&orderDescending=1">Id <i class="fas fa-sort-up"></i></a>
                    {{endif OrderBrandId}}
                </th>
                <th class="left">
                Nombre
                    {{ifnot OrderByBrandName}}
                        <a href="index.php?page=Maintenance_Catalog_Brands_Brands&orderBy=brandName&orderDescending=0">Nombre <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByBrandName}}
                    {{if OrderBrandNameDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Brands_Brands&orderBy=clear&orderDescending=0">Nombre <i class="fas fa-sort-down"></i></a>
                    {{endif OrderBrandNameDesc}}
                    {{if OrderBrandName}}
                        <a href="index.php?page=Maintenance_Catalog_Brands_Brands&orderBy=brandName&orderDescending=1">Nombre <i class="fas fa-sort-up"></i></a>
                    {{endif OrderBrandName}}
                </th>
                <th class="left">Descripción</th>
                <th>Estado</th>
                <th><a href="index.php?page=Maintenance_Catalog_Brands_Brand&mode=INS">Nuevo</a></th>
            </tr>
        </thead>
        <tbody>
            {{foreach brands}}
                <tr>
                    <td>{{brandId}}</td>
                    <td>
                        <a class="link" href="index.php?page=Maintenance_Catalog_Brands_Brand&mode=DSP&brandId={{brandId}}">
                            {{brandName}}
                        </a>
                    </td>
                    <td>{{brandDescription}}</td>
                    <td class="center">{{brandStatusDsc}}</td>
                    <td class="center">
                        <a href="index.php?page=Maintenance_Catalog_Brands_Brand&mode=UPD&brandId={{brandId}}">Editar</a>
                        &nbsp;
                        <a href="index.php?page=Maintenance_Catalog_Brands_Brand&mode=DEL&brandId={{brandId}}">Eliminar</a>
                    </td>
                </tr>
            {{endfor brands}}
        </tbody>
    </table>
    {{pagination}}
</section>