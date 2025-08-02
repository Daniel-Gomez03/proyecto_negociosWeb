<section class="depth-2 px-4 py-5">
    <h2>{{modeDsc}}</h2>
</section>
<section class="depth-2 px-4 py-4 my-4 grid row">
    <form method="POST" action="index.php?page=Maintenance_Roles_Rol&mode={{mode}}&rolescod={{rolescod}}"
        class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3">
        <div class="row my-2">
            <label for="rolescod" class="col-12 col-m-4 col-l-3">Código Rol:</label>
            <input 
            type="text" 
            name="rolescod" 
            id="rolescod" 
            value="{{rolescod}}" 
            placeholder="Código del rol"
            class="col-12 col-m-8 col-l-9" 
            {{readonly}} />
            <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
            {{foreach errors_rolescod}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_rolescod}}
        </div>
        <div class="row my-2">
            <label for="rolesdsc" class="col-12 col-m-4 col-l-3">Descripción:</label>
            <input 
            type="text" 
            name="rolesdsc" 
            id="rolesdsc" 
            value="{{rolesdsc}}" 
            placeholder="Descripción del rol"
            class="col-12 col-m-8 col-l-9" 
            {{readonly}} />
            {{foreach errors_rolesdsc}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_rolesdsc}}
        </div>
        <div class="row my-2">
            <label for="rolesest" class="col-12 col-m-4 col-l-3">Estado:</label>
            {{if readonly}}
            <input type="hidden" name="rolesest" value="{{rolesest}}" />
            <select id="rolesest" name="rolesest_tmp" disabled readonly class="col-12 col-m-8 col-l-9">
            {{endif readonly}}
            {{ifnot readonly}}
            <select id="rolesest" name="rolesest" class="col-12 col-m-8 col-l-9">
            {{endifnot readonly}}
                <option value="ACT" {{selectedACT}}>Activo</option>
                <option value="INA" {{selectedINA}}>Inactivo</option>
            </select>
            {{foreach errors_rolesest}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_rolesest}}
        </div>

        <div class="row">
            <div class="col-12 right">
                <button class="" id="btnCancel" type="button">{{cancelLabel}}</button>
                &nbsp;
                {{if showConfirm}}
                <button class="primary" type="submit">Confirmar</button>
                {{endif showConfirm}}
            </div>
        </div>
        {{if errors_global}}
        <div class="row">
            <ul class="col-12">
                {{foreach errors_global}}
                <li class="error">{{this}}</li>
                {{endfor errors_global}}
            </ul>
        </div>
        {{endif errors_global}}
    </form>
</section>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById("btnCancel")
            .addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                window.location.assign("index.php?page=Maintenance_Roles_Roles");
            });
    });
</script>