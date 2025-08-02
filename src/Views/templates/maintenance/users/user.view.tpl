<section class="depth-2 px-4 py-5">
    <h2>{{modeDsc}}</h2>
</section>
<section class="depth-2 px-4 py-4 my-4 grid row">
    <form method="POST" action="index.php?page=Maintenance_Users_User&mode={{mode}}&usercod={{usercod}}"
        class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3">

        {{if isChangePassword}}
        <div class="row my-2">
            <label for="usercod" class="col-12 col-m-4 col-l-3">User Code:</label>
            <input type="text" name="usercod" id="usercod" value="{{usercod}}" placeholder="User Code"
                class="col-12 col-m-8 col-l-9" readonly />
            <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
        </div>
        <div class="row my-2">
            <label for="useremail" class="col-12 col-m-4 col-l-3">Email:</label>
            <input type="email" name="useremail" id="useremail" value="{{useremail}}" class="col-12 col-m-8 col-l-9"
                readonly />
        </div>
        <div class="row my-2">
            <label for="username" class="col-12 col-m-4 col-l-3">Username:</label>
            <input type="text" name="username" id="username" value="{{username}}" class="col-12 col-m-8 col-l-9"
                readonly />
        </div>
        <div class="row my-2">
            <label for="current_password" class="col-12 col-m-4 col-l-3">Contraseña Actual:</label>
            <input type="password" name="current_password" id="current_password" value=""
                placeholder="Contraseña actual" class="col-12 col-m-8 col-l-9" required />
            {{foreach errors_current_password}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_current_password}}
        </div>
        <div class="row my-2">
            <label for="new_password" class="col-12 col-m-4 col-l-3">Nueva Contraseña:</label>
            <input type="password" name="new_password" id="new_password" value="" placeholder="Nueva contraseña"
                class="col-12 col-m-8 col-l-9" required />
            {{foreach errors_new_password}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_new_password}}
        </div>
        <div class="row my-2">
            <label for="confirm_password" class="col-12 col-m-4 col-l-3">Confirmar Contraseña:</label>
            <input type="password" name="confirm_password" id="confirm_password" value=""
                placeholder="Confirmar nueva contraseña" class="col-12 col-m-8 col-l-9" required />
            {{foreach errors_confirm_password}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_confirm_password}}
        </div>
        {{endif isChangePassword}}

        {{ifnot isChangePassword}}
        <div class="row my-2">
            <label for="usercod" class="col-12 col-m-4 col-l-3">User Code:</label>
            <input type="text" name="usercod" id="usercod" value="{{usercod}}" placeholder="User Code"
                class="col-12 col-m-8 col-l-9" readonly />
            <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
        </div>
        <div class="row my-2">
            <label for="useremail" class="col-12 col-m-4 col-l-3">Email:</label>
            <input type="email" name="useremail" id="useremail" value="{{useremail}}" placeholder="User Email"
                class="col-12 col-m-8 col-l-9" {{readonly}} />
            {{foreach errors_useremail}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_useremail}}
        </div>
        <div class="row my-2">
            <label for="username" class="col-12 col-m-4 col-l-3">Username:</label>
            <input type="text" name="username" id="username" value="{{username}}" placeholder="Username"
                class="col-12 col-m-8 col-l-9" {{readonly}} />
            {{foreach errors_username}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_username}}
        </div>
        <div class="row my-2">
            <label for="userpswd" class="col-12 col-m-4 col-l-3">Contraseña:</label>
            <input type="password" name="userpswd" id="userpswd" value="{{userpswd}}" placeholder="Contraseña"
                class="col-12 col-m-8 col-l-9" {{readonly}} />
            {{foreach errors_userpswd}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_userpswd}}
        </div <div class="row my-2">
        <label for="userfching" class="col-12 col-m-4 col-l-3">Fecha Ingreso:</label>
        <input type="datetime-local" name="userfching" id="userfching" value="{{userfching}}"
            class="col-12 col-m-8 col-l-9" readonly />
        </div>
        <div class="row my-2">
            <label for="userpswdest" class="col-12 col-m-4 col-l-3">Estado Contraseña:</label>
            {{if readonly}}
            <input type="hidden" name="userpswdest" value="{{userpswdest}}" />
            <select id="userpswdest" name="userpswdest_tmp" disabled readonly>
                {{endif readonly}}
                {{ifnot readonly}}
                <select id="userpswdest" name="userpswdest" class="col-12 col-m-8 col-l-9">
                    {{endifnot readonly}}
                    <option value="ACT" {{selectedPswdACT}}>Activa</option>
                    <option value="INA" {{selectedPswdINA}}>Inactiva</option>
                </select>
                {{foreach errors_userpswdest}}
                <div class="error col-12">{{this}}</div>
                {{endfor errors_userpswdest}}
        </div>
        <div class="row my-2">
            <label for="userpswdexp" class="col-12 col-m-4 col-l-3">Expira Contraseña:</label>
            <input type="datetime-local" name="userpswdexp" id="userpswdexp" value="{{userpswdexp}}"
                class="col-12 col-m-8 col-l-9" {{readonly}} />
            {{foreach errors_userpswdexp}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_userpswdexp}}
        </div>
        <div class="row my-2">
            <label for="userest" class="col-12 col-m-4 col-l-3">Estado Usuario:</label>
            {{if readonly}}
            <input type="hidden" name="userest" value="{{userest}}" />
            <select id="userest" name="userest_tmp" disabled readonly>
                {{endif readonly}}
                {{ifnot readonly}}
                <select id="userest" name="userest" class="col-12 col-m-8 col-l-9">
                    {{endifnot readonly}}
                    <option value="ACT" {{selectedUserACT}}>Activo</option>
                    <option value="INA" {{selectedUserINA}}>Inactivo</option>
                </select>
                {{foreach errors_userest}}
                <div class="error col-12">{{this}}</div>
                {{endfor errors_userest}}
        </div>
        <div class="row my-2">
            <label for="userpswdchg" class="col-12 col-m-4 col-l-3">Último Cambio Password:</label>
            <input type="datetime-local" name="userpswdchg" id="userpswdchg" value="{{userpswdchg}}"
                class="col-12 col-m-8 col-l-9" readonly />
        </div>
        <div class="row my-2">
            <label for="usertipo" class="col-12 col-m-4 col-l-3">Tipo Usuario:</label>
            {{if readonly}}
            <input type="hidden" name="usertipo" value="{{usertipo}}" />
            <select id="usertipo" name="usertipo_tmp" disabled readonly>
                {{endif readonly}}
                {{ifnot readonly}}
                <select id="usertipo" name="usertipo" class="col-12 col-m-8 col-l-9">
                    {{endifnot readonly}}
                    <option value="PBL" {{selectedPBL}}>Público</option>
                    <option value="AUD" {{selectedAUD}}>Auditor</option>
                    <option value="ADM" {{selectedADM}}>Administrador</option>
                </select>
                {{foreach errors_usertipo}}
                <div class="error col-12">{{this}}</div>
                {{endfor errors_usertipo}}
        </div>
        {{endifnot isChangePassword}}

        <div class="row">
            <div class="col-12 right">
                <button class="" id="btnCancel" type="button">{{cancelLabel}}</button>
                &nbsp;
                {{if showConfirm}}
                <button class="primary" type="submit">Confirm</button>
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
                window.location.assign("index.php?page=Maintenance_Users_Users");
            });
    });
</script>