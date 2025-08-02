<h1>Administración de Usuarios</h1>
<section class="grid">
    <div class="row">
        <form class="col-12 col-m-8" action="index.php" method="get">
            <div class="flex align-center">
                <div class="col-8 row">
                    <input type="hidden" name="page" value="Maintenance_Users_Users">
                    <label class="col-3" for="partialName">Nombre/Email</label>
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
                    {{ifnot OrderByUsercod}}
                        <a href="index.php?page=Maintenance_Users_Users&orderBy=usercod&orderDescending=0">Id <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByUsercod}}
                    {{if OrderUsercodDesc}}
                        <a href="index.php?page=Maintenance_Users_Users&orderBy=clear&orderDescending=0">Id <i class="fas fa-sort-down"></i></a>
                    {{endif OrderUsercodDesc}}
                    {{if OrderUsercod}}
                        <a href="index.php?page=Maintenance_Users_Users&orderBy=usercod&orderDescending=1">Id <i class="fas fa-sort-up"></i></a>
                    {{endif OrderUsercod}}
                </th>
                <th class="left">
                    Email
                    {{ifnot OrderByUseremail}}
                        <a href="index.php?page=Maintenance_Users_Users&orderBy=useremail&orderDescending=0">Email <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByUseremail}}
                    {{if OrderUseremailDesc}}
                        <a href="index.php?page=Maintenance_Users_Users&orderBy=clear&orderDescending=0">Email <i class="fas fa-sort-down"></i></a>
                    {{endif OrderUseremailDesc}}
                    {{if OrderUseremail}}
                        <a href="index.php?page=Maintenance_Users_Users&orderBy=useremail&orderDescending=1">Email <i class="fas fa-sort-up"></i></a>
                    {{endif OrderUseremail}}
                </th>
                <th class="left">
                    Username
                    {{ifnot OrderByUsername}}
                        <a href="index.php?page=Maintenance_Users_Users&orderBy=username&orderDescending=0">Username <i class="fas fa-sort"></i></a>
                    {{endifnot OrderByUsername}}
                    {{if OrderUsernameDesc}}
                        <a href="index.php?page=Maintenance_Users_Users&orderBy=clear&orderDescending=0">Username <i class="fas fa-sort-down"></i></a>
                    {{endif OrderUsernameDesc}}
                    {{if OrderUsername}}
                        <a href="index.php?page=Maintenance_Users_Users&orderBy=username&orderDescending=1">Username <i class="fas fa-sort-up"></i></a>
                    {{endif OrderUsername}}
                </th>
                <th>Estado</th>
                <th>Tipo</th>
                <th>Acciones</th>
                <th>
                    <a href="index.php?page=Maintenance_Users_User&mode=INS&usercod=">Nuevo</a>
                </th>
            </tr>
        </thead>
        <tbody>
            {{foreach users}}
                <tr>
                    <td>{{usercod}}</td>
                    <td>{{useremail}}</td>
                    <td>{{username}}</td>
                    <td>{{userestDsc}}</td>
                    <td>{{usertipoDsc}}</td>
                    <td>
                        <a href="index.php?page=Maintenance_Users_User&mode=CHGPWD&usercod={{usercod}}">
                            Cambiar contraseña
                        </a>
                    </td>
                    <td>
                        <a href="index.php?page=Maintenance_Users_User&mode=UPD&usercod={{usercod}}">Editar</a>
                        &nbsp;
                        <a href="index.php?page=Maintenance_Users_User&mode=DSP&usercod={{usercod}}">Ver</a>
                        &nbsp;
                        <a href="index.php?page=Maintenance_Users_User&mode=DEL&usercod={{usercod}}">Eliminar</a>
                    </td>
                </tr>
            {{endfor users}}
        </tbody>
    </table>
    {{pagination}}
</section>