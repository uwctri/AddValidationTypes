$(document).ready(() => {

    let html = `
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
        <label class="font-weight-bold col-3 mb-0">Case Sensative?</label> 
        <div class="col-7">
            <div class="custom-control custom-checkbox custom-control-inline">
                <input name="caseSensative" id="caseSensative_0" type="checkbox" class="custom-control-input" value=""> 
                <label for="caseSensative_0" class="custom-control-label"></label>
            </div>
        </div>
        <div class="col-2 text-right">
            <button name="submit" type="submit" class="btn btn-primary">Add</button>
        </div>
    </div>
</div>`

    $("#val_table tr td").first().attr("colspan", "4")
    $("#val_table tr:not(:first)").append(`
        <td class="data2" style="text-align:center;font-size:13px"><a><i class="fa-solid fa-trash-can hidden"></i></a></td>
    `)
    $("#val_table").before(html)

    // Setup form. Validate on server and client
    // Display Name (alpha, numeric, space, limited special chars ()-:/.)
    // Internal Name (lower alpha, undersocre, numeric)
    // PHP Regex (Make sure that \ is escaped)
    // JS Regex (Make sure that \ is escaped)

    // Make sure that display name isn't in use
    // Make sure that internal name isn't in use
    // Track which internal names we have added so we know what can be reomved
    // Update the table on screen on add

});