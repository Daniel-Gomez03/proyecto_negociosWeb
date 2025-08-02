<section class="depth-2 px-4 py-5">
    <h2>{{modeDsc}}</h2>
</section>
<section class="depth-2 px-4 py-4 my-4 grid row">
    <form method="POST" action="index.php?page=Maintenance_Funciones_Funcion&mode={{mode}}&fncod={{fncod}}"
        class="grid col-12 col-m-8 offset-m-2 col-l-6 offset-l-3">
        <div class="row my-2">
            <label for="fncod" class="col-12 col-m-4 col-l-3">Feature Code:</label>
            <input 
            type="text" 
            name="fncod" 
            id="fncod" 
            value="{{fncod}}" 
            placeholder="Feature code"
            class="col-12 col-m-8 col-l-9" 
            {{readonly}} />
            <input type="hidden" name="xsrtoken" value="{{xsrtoken}}" />
            {{foreach errors_fncod}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_fncod}}
        </div>
        <div class="row my-2">
            <label for="fndsc" class="col-12 col-m-4 col-l-3">Description:</label>
            <input 
            type="text" 
            name="fndsc" 
            id="fndsc" 
            value="{{fndsc}}" 
            placeholder="Feature description"
            class="col-12 col-m-8 col-l-9" 
            {{readonly}} />
            {{foreach errors_fndsc}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_fndsc}}
        </div>
        <div class="row my-2">
            <label for="fnest" class="col-12 col-m-4 col-l-3">Status:</label>
            {{if readonly}}
            <input type="hidden" name="fnest" value="{{fnest}}" />
            <select id="fnest" name="fnest_tmp" disabled readonly class="col-12 col-m-8 col-l-9">
            {{endif readonly}}
            {{ifnot readonly}}
            <select id="fnest" name="fnest" class="col-12 col-m-8 col-l-9">
            {{endifnot readonly}}
                <option value="ACT" {{selectedACT}}>Active</option>
                <option value="INA" {{selectedINA}}>Inactive</option>
            </select>
            {{foreach errors_fnest}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_fnest}}
        </div>
        <div class="row my-2">
            <label for="fntyp" class="col-12 col-m-4 col-l-3">Type:</label>
            {{if readonly}}
            <input type="hidden" name="fntyp" value="{{fntyp}}" />
            <select id="fntyp" name="fntyp_tmp" disabled readonly class="col-12 col-m-8 col-l-9">
            {{endif readonly}}
            {{ifnot readonly}}
            <select id="fntyp" name="fntyp" class="col-12 col-m-8 col-l-9">
            {{endifnot readonly}}
                <option value="CTR" {{selectedCTR}}>Controller</option>
                <option value="FNC" {{selectedFNC}}>Function</option>
                <option value="MNU" {{selectedMNU}}>Menu</option>
            </select>
            {{foreach errors_fntyp}}
            <div class="error col-12">{{this}}</div>
            {{endfor errors_fntyp}}
        </div>

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
                window.location.assign("index.php?page=Maintenance_Funciones_Funciones");
            });
    });
</script>