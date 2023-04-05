(() => {

    const module = ExternalModules.UWMadison.AddValidationTypes
    const css = `
    .validationRemove {
        color: #3e3e3e;
    }
	.form-switch.form-switch-md .form-check-input {
		height: 1.5rem;
		width: calc(2rem + 0.75rem);
		border-radius: 3rem;
	}`

    // Form to add new validation types
    const html = `
<div id="addValidationForm" class="p-4 border rounded mb-4">
    <div class="form-group">
        <label class="font-weight-bold mb-0" for="displayName">Display Name</label> 
        <input id="displayName" name="displayName" placeholder="HealthCare Inc MRN" type="text" class="form-control">
    </div>
    <div class="form-group">
        <label class="font-weight-bold mb-0" for="internalName">Internal Name</label> 
        <input id="internalName" name="internalName" placeholder="healthcare_mrn" type="text" class="form-control">
    </div>
    <div class="form-group">
        <label class="font-weight-bold mb-0" for="phpRegex">PHP Regex 
            <i class="far fa-question-circle text-secondary" data-bs-toggle="popover" data-bs-content='
                REDCap uses regular expressions on both the client (JS) and server (PHP) sides to verify the format of a field. 
                Sites like <a href="https://regexr.com/">Regexr</a> can be used to design and easily test both JS and PCRE regex, 
                but as of 2018 there are not many differences between JS and PCRE regex.
            '></i>
        </label> 
        <div class="input-group">
            <div class="input-group-text">/^</div>
            <input id="phpRegex" name="phpRegex" type="text" class="form-control"> 
            <div class="input-group-text">$/</div>
        </div>
    </div>
    <div class="form-group">
        <label class="font-weight-bold mb-0" for="jsRegex">JS Regex</label> 
        <div class="input-group">
            <div class="input-group-text">/^</div>
            <input id="jsRegex" name="jsRegex" type="text" class="form-control"> 
            <div class="input-group-text">$/</div>
        </div>
    </div>
    <div class="form-group row mb-0">
        <div class="col-4">
            <label class="font-weight-bold mb-0" for="dataType">Data Type 
                <i class="far fa-question-circle text-secondary" data-bs-toggle="popover" data-bs-content='
                    All validations have a "Data Type" that describes what kind of data is being validated. 
                    The Data Type will determine if a field can be used with certain special functions. 
                    If you are not certain what to use the "text" option is likely the best choice. 
                    The list of options available for a Data Type are determined by Vanderbilt and cannot be changed.
                '></i>
            </label> 
            <div>
                <select id="dataType" name="dataType" class="form-select">
                </select>
            </div>
        </div>
        <div class="col-4">
            <label class="font-weight-bold mb-0" for="caseSensative">Case Sensative?</label> 
            <div class="form-check form-switch form-switch-md">
                <input name="caseSensative" id="caseSensative" type="checkbox" class="form-check-input" value=""> 
                <label for="caseSensative" class="form-check-label"></label>
            </div>
        </div>
        <div class="col-4 text-right">
            <button onclick="window.location.href='${module.settings.repo}';" class="btn btn-secondary btn-sm mt-4 mr-2 text-white">RegexRepo</button>
            <button id="validationAdd" class="btn btn-primary mt-3" style="font-size:16px">Add</button>
        </div>
    </div>
</div>`

    const genericError = (err) => {
        console.log(err)
        Swal.fire({
            icon: "error",
            title: "Unexpected Server Error",
            html: "An unknown error has occured and the server has failed to respond.<br>" + err
        })
    }

    const responseError = (errs, msg) => {
        console.log(errs)
        errs.forEach((el) => { msg = msg + "<br>" + el })
        Swal.fire({
            icon: "error",
            title: "Unable to add Validation Type",
            html: msg
        })
    }

    const getForm = () => {

        // Grab all used values
        const display = $("#displayName").val()
        const internal = $("#internalName").val()
        let phpRegex = $("#phpRegex").val()
        let jsRegex = $("#jsRegex").val()
        const dataType = $("#dataType").val()
        const caseSensative = $("#caseSensative").is(":checked")

        if (!display || !internal || !phpRegex || !jsRegex) return false
        if ($("#addValidationForm .is-invalid").length) return false

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

    const validateField = (el) => {

        // Check if a value exists
        const $self = $(el.currentTarget)
        $self.removeClass("is-invalid")
        if ($self.val() == "") return

        // Grab all info and do a regex test
        const value = $self.val()
        const name = $self.prop('id')
        const pattern = module.settings.regex[name].slice(1, -1)
        const regex = new RegExp(pattern)
        if (!regex.test(value)) $self.addClass("is-invalid")

        // Special cases to check for some fields
        if (name == "jsRegex") {
            try {
                new RegExp(value)
            } catch (e) {
                $self.addClass("is-invalid")
            }
        }
        if (name == "displayName") {
            const names = Object.values(module.settings.validationTypes).map(el => el['display'].toLowerCase().replaceAll(" ", ""))
            const trimName = value.toLowerCase().replaceAll(" ", "")
            if (names.includes(trimName)) $self.addClass("is-invalid")
        }
        if (name == "internalName") {
            const names = Object.keys(module.settings.validationTypes)
            if (names.includes(value)) $self.addClass("is-invalid")
        }
    }

    // Style old table
    $("head").append(`<style>${css}</style>`)
    $("#val_table tr td").first().attr("colspan", "4")
    $("#val_table tr:not(:first)").append(`
        <td class="data2" style="text-align:center;font-size:13px">
            <a class="validationRemove hidden">
                <i class="fa-solid fa-trash-can"></i>
            </a>
        </td>
    `)
    module.settings.emTypes.forEach((el) => $(`#${el} a`).removeClass('hidden'))

    // Insert the new form and setup
    $("#val_table").before(html).show()
    module.settings.dataTypes.forEach((el) => $("#dataType").append(new Option(el)))
    $("#dataType").val("text") // Default
    $("#addValidationForm input").on("keyup", (el) => validateField(el))
    // Populate form with info form URL (Regex Repo)
    for (const [name, value] of (new URLSearchParams(window.location.search)).entries()) {
        if (name == "caseSensative") $("#caseSensative").prop("checked", true)
        $(`#addValidationForm #${name}`).val(value).trigger("keyup")
    }

    // Setup Add button on new form
    $("#validationAdd").on("click", () => {
        const settings = getForm()
        if (!settings) return
        const btn = $("#validationAdd");
        btn.prop("disabled", true)
        module.ajax("add", settings).then((response) => {
            if (response.errors.length == 0) {
                window.location = window.location.href.split("?")[0]
                return
            }
            btn.prop("disabled", false)
            responseError(response.errors,
                "A server error has prevented the validation type from being added to your Redcap instance.")
        }).catch((err) => {
            btn.prop("disabled", false)
            genericError(err)
        })
    })

    // Setup old table Interactivity (delete)
    $("#val_table").on("click", ".validationRemove", (el) => {
        const $el = $(el.currentTarget)
        const $row = $el.closest('tr')
        const name = $row.prop("id")
        if (!$el.is(":visible") || name == "") return
        module.ajax("remove", { name }).then((response) => {
            if (response.errors.length == 0) {
                $row.remove()
                return;
            }
            responseError(response.errors,
                "A server error has prevented the validation type from being removed to your Redcap instance.")
        }).catch((err) => {
            genericError(err)
        })
    })

    // Setup nice popover functionality
    let popoverList = [];
    $('[data-bs-toggle="popover"]').toArray().map((el) => {
        popoverList.push(new bootstrap.Popover(el, {
            html: true,
            trigger: 'hover',
            delay: {
                hide: 20000
            }
        }))
    })
    $("body").on('click', (e) => {
        if (!$(e.target).hasClass('popover-body'))
            popoverList.map((pop) => pop.hide())
    })
})()