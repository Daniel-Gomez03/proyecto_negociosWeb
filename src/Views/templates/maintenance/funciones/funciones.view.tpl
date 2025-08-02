<h1>Administración de Funciones</h1>
<section class="grid">
    <div class="row">
        <form class="col-12 col-m-8" action="index.php" method="get">
            <div class="flex align-center">
                <div class="col-8 row">
                    <input type="hidden" name="page" value="Maintenance_Funciones_Funciones">
                    <label class="col-3" for="partialCode">Código</label>
                    <input class="col-9" type="text" name="partialCode" id="partialCode" value="{{partialCode}}" />
                    <label class="col-3" for="status">Estado</label>
                    <select class="col-9" name="status" id="status">
                        <option value="EMP" {{status_EMP}}>Todos</option>
                        <option value="ACT" {{status_ACT}}>Activo</option>
                        <option value="INA" {{status_INA}}>Inactivo</option>
                    </select>
                    <label class="col-3" for="type">Type</label>
                    <select class="col-9" name="type" id="type">
                        <option value="EMP" {{type_EMP}}>All</option>
                        <option value="FNC" {{type_FNC}}>Función</option>
                        <option value="CTR" {{type_CTR}}>Controller</option>
                        <option value="MNU" {{type_CTR}}>Menú</option>
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
                    Código
                    {{ifnot OrderByFncod}}
                        <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=fncod&orderDescending=0">Código <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByFncod}}
                    {{if OrderFncodDesc}}
                        <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=clear&orderDescending=0">Código <i class="fas fa-sort-down"></i></a>
                    {{endif OrderFncodDesc}}
                    {{if OrderFncod}}
                        <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=fncod&orderDescending=1">Código <i class="fas fa-sort-up"></i></a>
                    {{endif OrderFncod}}
                </th>
                <th class="left">
                    Descripción
                    {{ifnot OrderByFndsc}}
                        <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=fndsc&orderDescending=0">Descripción <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByFndsc}}
                    {{if OrderFndscDesc}}
                        <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=clear&orderDescending=0">Descripción <i class="fas fa-sort-down"></i></a>
                    {{endif OrderFndscDesc}}
                    {{if OrderFndsc}}
                        <a href="index.php?page=Maintenance_Funciones_Funciones&orderBy=fndsc&orderDescending=1">Descripción <i class="fas fa-sort-up"></i></a>
                    {{endif OrderFndsc}}
                </th>
                <th>Estado</th>
                <th>Tipo</th>
                <th>
                    <a href="index.php?page=Maintenance_Funciones_Funcion&mode=INS">Nuevo</a>
                </th>
            </tr>
        </thead>
        <tbody>
            {{foreach funciones}}
            <tr>
                <td>
                    <a class="link" href="index.php?page=Maintenance_Funciones_Funcion&mode=DSP&fncod={{fncod}}">
                        {{fncod}}
                    </a>
                </td>
                <td>{{fndsc}}</td>
                <td class="center">{{fnestDsc}}</td>
                <td class="center">{{fntypDsc}}</td>
                <td class="center">
                    <a href="index.php?page=Maintenance_Funciones_Funcion&mode=UPD&fncod={{fncod}}">Editar</a>
                    &nbsp;
                    <a href="index.php?page=Maintenance_Funciones_Funcion&mode=DEL&fncod={{fncod}}">Eliminar</a>
                </td>
            </tr>
            {{endfor funciones}}
        </tbody>
    </table>
    {{pagination}}
</section>