<h1>Funciones</h1>
<section class="grid">
    <div class="row">
        <form class="col-12 col-m-8" action="index.php" method="get">
            <div class="flex align-center">
                <div class="col-8 row">
                    <input type="hidden" name="page" value="Maintenance_Funciones_Funciones">
                    <label class="col-3" for="partialDesc">Código o Descripción</label>
                    <input class="col-9" type="text" name="partialDesc" id="partialDesc" value="{{partialDesc}}" />
                    <label class="col-3" for="status">Estado</label>
                    <select class="col-9" name="status" id="status">
                        <option value="EMP" {{status_EMP}}>Todos</option>
                        <option value="ACT" {{status_ACT}}>Activo</option>
                        <option value="INA" {{status_INA}}>Inactivo</option>
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
                <th class="left">Código
                    {{ifnot OrderByFncod}}
                    <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=fncod&orderDescending=0">Código<i
                            class="fas fa-sort"></i></a>
                    {{endifnot OrderByFncod}}
                    {{if OrderFncodDesc}}
                    <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=clear&orderDescending=0">Código<i
                            class="fas fa-sort-down"></i></a>
                    {{endif OrderFncodDesc}}
                    {{if OrderFncod}}
                    <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=fncod&orderDescending=1">Código<i
                            class="fas fa-sort-up"></i></a>
                    {{endif OrderFncod}}
                </th>
                <th class="left">Descripción
                    {{ifnot OrderByFndsc}}
                    <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=fndsc&orderDescending=0">Descripción<i
                            class="fas fa-sort"></i></a>
                    {{endifnot OrderByFndsc}}
                    {{if OrderFndscDesc}}
                    <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=clear&orderDescending=0">Descripción<i
                            class="fas fa-sort-down"></i></a>
                    {{endif OrderFndscDesc}}
                    {{if OrderFndsc}}
                    <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=fndsc&orderDescending=1">Descripción<i
                            class="fas fa-sort-up"></i></a>
                    {{endif OrderFndsc}}
                </th>
                <th class="center">Estado</th>
                <th class="center">Tipo</th>
                <th><a href="index.php?page=Maintenance_Funciones_Funcion&mode=INS">Nuevo</a></th>
            </tr>
        </thead>
        <tbody>
            {{foreach funciones}}
            <tr>
                <td>{{fncod}}</td>
                <td>{{fndsc}}</td>
                <td class="center">{{fnestDsc}}</td>
                <td class="center">{{fntypDsc}}</td>
                <td class="center">
                    <a href="index.php?page=Maintenance_Funciones_Funcion&mode=UPD&id={{fncod}}">Editar</a>
                    &nbsp;
                    <a href="index.php?page=Maintenance_Funciones_Funcion&mode=DEL&id={{fncod}}">Eliminar</a>
                </td>
            </tr>
            {{endfor funciones}}
        </tbody>
    </table>
    {{pagination}}
</section>
