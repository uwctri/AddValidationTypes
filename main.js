(() => {

    // Small amount of css just for the case sensative toggle
    const css = `
    .custom-switch.custom-switch-lg {
        padding-bottom: 1rem;
        padding-left: 2.25rem;
    }
    .custom-switch.custom-switch-lg .custom-control-label {
        padding-left: 0.75rem;
        padding-top: 0.15rem;
    }
    .custom-switch.custom-switch-lg .custom-control-label::before {
        border-radius: 1rem;
        height: 1.5rem;
        width: 2.5rem;
    }
    .custom-switch.custom-switch-lg .custom-control-label::after {
        border-radius: 0.65rem;
        height: calc(1.5rem - 4px);
        width: calc(1.5rem - 4px);
    }
    .custom-switch.custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after {
        transform: translateX(1rem);
    }`

    // Form to add new validation types
    const html = `
<div id="add_validation_form" class="p-4 border rounded mb-4">
    <div class="form-group">
        <label class="font-weight-bold mb-0" for="displayName">Display Name</label> 
        <input id="displayName" name="displayName" placeholder="HealthCare Inc MRN" type="text" class="form-control">
    </div>
    <div class="form-group">
        <label class="font-weight-bold mb-0" for="internalName">Internal Name</label> 
        <input id="internalName" name="internalName" placeholder="healthcare_mrn" type="text" class="form-control">
    </div>
    <div class="form-group">
        <label class="font-weight-bold mb-0" for="phpRegex">PHP Regex</label> 
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">/^</div>
            </div>
            <input id="phpRegex" name="phpRegex" type="text" class="form-control"> 
            <div class="input-group-append">
                <div class="input-group-text">$/</div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="font-weight-bold mb-0" for="jsRegex">JS Regex</label> 
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">/^</div>
            </div>
            <input id="jsRegex" name="jsRegex" type="text" class="form-control"> 
            <div class="input-group-append">
                <div class="input-group-text">$/</div>
            </div>
        </div>
    </div>
    <div class="form-group row mb-0">
        <div class="col-4">
            <label class="font-weight-bold mb-0" for="dataType">Data Type</label> 
            <div>
                <select id="dataType" name="dataType" class="custom-select">
                </select>
            </div>
        </div>
        <div class="col-4">
            <label class="font-weight-bold mb-0" for="caseSensative">Case Sensative?</label> 
            <div class="custom-control custom-switch custom-switch-lg">
                <input name="caseSensative" id="caseSensative" type="checkbox" class="custom-control-input" value=""> 
                <label for="caseSensative" class="custom-control-label"></label>
            </div>
        </div>
        <div class="col-4 text-right">
            <button id="validationAdd" class="btn btn-primary">Add</button>
        </div>
    </div>
</div>`

    const getForm = () => {
        // Grab all used values
        const display = $("#displayName").val()
        const internal = $("#internalName").val()
        let phpRegex = $("#phpRegex").val()
        let jsRegex = $("#jsRegex").val()
        const dataType = $("#dataType").val()
        const caseSensative = $("#caseSensative").is(":checked")

        if (!display || !internal || !phpRegex || !jsRegex) return false

        // TODO check each validation

        phpRegex = `/^${phpRegex}$/`
        jsRegex = `/^${jsRegex}$/`

        if (!caseSensative) {
            phpRegex += "i"
            jsRegex += "i"
        }

        return {
            display, internal, phpRegex, jsRegex, dataType
        }
    }

    // Insert the new form and style the old table
    $("head").append(`<style>${css}</style>`)
    $("#val_table tr td").first().attr("colspan", "4")
    $("#val_table tr:not(:first)").append(`
        <td class="data2" style="text-align:center;font-size:13px">
            <a class="validationRemove hidden">
                <i class="fa-solid fa-trash-can"></i>
            </a>
        </td>
    `)
    $("#val_table").before(html).show()
    ExternalModules.addValTypes.dataTypes.forEach((el) => $("#dataType").append(new Option(el)))
    $("#dataType").val("text") // Default

    // Remove hidden class on rows that were added by EM
    ExternalModules.addValTypes.validationTypes.forEach((el) => $(`#${el} a`).removeClass('hidden'))

    // Validations TODO
    // Display Name (alpha, numeric, space, limited special chars ()-:/.)
    // Internal Name (lower alpha, undersocre, numeric) 
    // PHP Regex (Make sure that \ is escaped)
    // JS Regex (Make sure that \ is escaped)
    // Make sure that display name isn't in use
    // Make sure that internal name isn't in use

    // Setup Add button on new form
    $("#validationAdd").on("click", () => {
        const settings = getForm();
        if (!settings) return;
        $("#validationAdd").prop("disabled", true)
        $.ajax({
            method: 'POST',
            url: ExternalModules.addValTypes.router,
            data: {
                ...settings,
                action: 'add',
                redcap_csrf_token: ExternalModules.addValTypes.csrf
            },
            // Only occurs on network or technical issue
            error: (jqXHR, textStatus, errorThrown) => console.log(`${JSON.stringify(jqXHR)}\n${textStatus}\n${errorThrown}`),
            // Response returned from server (possible 500 error still)
            success: (data) => {
                console.log(data);
                if ((typeof data == "string" && data.length === 0) || data.errors.length) {
                    // TODO show an error
                }
                location.reload()
            }
        })
    })

    // Setup old table Interactivity (delete)
    $("#val_table").on("click", ".validationRemove", (el) => {
        const $el = $(el.currentTarget)
        const $row = $el.closest('tr')
        const name = $row.prop("id")
        if (!$el.is(":visible") || name == "") return
        $.ajax({
            method: 'POST',
            url: ExternalModules.addValTypes.router,
            data: {
                name: name,
                action: 'remove',
                redcap_csrf_token: ExternalModules.addValTypes.csrf
            },
            // Only occurs on network or technical issue
            error: (jqXHR, textStatus, errorThrown) => console.log(`${JSON.stringify(jqXHR)}\n${textStatus}\n${errorThrown}`),
            // Response returned from server (possible 500 error still)
            success: (data) => {
                console.log(data);
                if ((typeof data == "string" && data.length === 0) || data.errors.length) return;
                $row.remove()
            }
        })
    })
})();