<h1>Administración de Categorías</h1>
<section class="grid">
    <div class="row">
        <form class="col-12 col-m-8" action="index.php" method="get">
            <div class="flex align-center">
                <div class="col-8 row">
                    <input type="hidden" name="page" value="Maintenance_Catalog_Categories_Categories">
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
                    {{ifnot OrderByCategoryId}}
                        <a href="index.php?page=Maintenance_Catalog_Categories_Categories&orderBy=categoryId&orderDescending=0">Id <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByCategoryId}}
                    {{if OrderCategoryIdDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Categories_Categories&orderBy=clear&orderDescending=0">Id <i class="fas fa-sort-down"></i></a>
                    {{endif OrderCategoryIdDesc}}
                    {{if OrderCategoryId}}
                        <a href="index.php?page=Maintenance_Catalog_Categories_Categories&orderBy=categoryId&orderDescending=1">Id <i class="fas fa-sort-up"></i></a>
                    {{endif OrderCategoryId}}
                </th>
                <th class="left">
                    {{ifnot OrderByCategoryName}}
                        <a href="index.php?page=Maintenance_Catalog_Categories_Categories&orderBy=categoryName&orderDescending=0">Nombre <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByCategoryName}}
                    {{if OrderCategoryNameDesc}}
                        <a href="index.php?page=Maintenance_Catalog_Categories_Categories&orderBy=clear&orderDescending=0">Nombre <i class="fas fa-sort-down"></i></a>
                    {{endif OrderCategoryNameDesc}}
                    {{if OrderCategoryName}}
                        <a href="index.php?page=Maintenance_Catalog_Categories_Categories&orderBy=categoryName&orderDescending=1">Nombre <i class="fas fa-sort-up"></i></a>
                    {{endif OrderCategoryName}}
                </th>
                <th class="left">Descripción</th>
                <th>Estado</th>
                <th><a href="index.php?page=Maintenance_Catalog_Categories_Category&mode=INS">Nuevo</a></th>
            </tr>
        </thead>
        <tbody>
            {{foreach categories}}
                <tr>
                    <td>{{categoryId}}</td>
                    <td>
                        <a class="link" href="index.php?page=Maintenance_Catalog_Categories_Category&mode=DSP&categoryId={{categoryId}}">
                            {{categoryName}}
                        </a>
                    </td>
                    <td>{{categoryDescription}}</td>
                    <td class="center">{{categoryStatusDsc}}</td>
                    <td class="center">
                        <a href="index.php?page=Maintenance_Catalog_Categories_Category&mode=UPD&categoryId={{categoryId}}">Editar</a>
                        &nbsp;
                        <a href="index.php?page=Maintenance_Catalog_Categories_Category&mode=DEL&categoryId={{categoryId}}">Eliminar</a>
                    </td>
                </tr>
            {{endfor categories}}
        </tbody>
    </table>
    {{pagination}}
</section>