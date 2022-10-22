<?php
require_once "../../redcap_connect.php";

// Display the header
$HtmlPage = new HtmlPage();
$HtmlPage->addStylesheet("home.css", 'screen,print');
$HtmlPage->PrintHeader();
include APP_PATH_VIEWS . 'HomeTabs.php';

// Data for page
$url = APP_PATH_WEBROOT_FULL . "redcap_v" . REDCAP_VERSION . "/ControlCenter/validation_type_setup.php";
$data = [
    [
        "display" => "Integer List",
        "internal" => "list_integer",
        "phpRegex" => "/^((-*\d+, *)*-*\d+)*$/i",
        "jsRegex" => "/^((-*\d+, *)*-*\d+)*$/i",
        "dataType" => "text",
        "examples" => [],
        "notes" => ""
    ],
    [
        "display" => "Number List",
        "internal" => "list_number",
        "phpRegex" => "/^((-*(\d*\.?\d+)+, *)*-*(\d*\.?\d+)+)*$/i",
        "jsRegex" => "/^((-*(\d*\.?\d+)+, *)*-*(\d*\.?\d+)+)*$/i",
        "dataType" => "text",
        "examples" => [],
        "notes" => ""
    ]
];

?>

<style>
    #pagecontainer {
        max-width: 1200px;
    }
</style>

<div class="col">
    <h4>
        <i class="fas fa-flask"></i>
        Regex Repo
    </h4>
    <p>
        The Regex Repo is a collection of validation types and their associated regular expressions that you can add to your REDCap instance with a few clicks.
        These validation types have not been reviewed by Vanderbilt, but are maintained as a part of the
        <a href="https://github.com/uwctri/AddValidationTypes">Add Validation Types</a> external module.
    </p>
    <table class="w-100">
        <thead>
            <th>Display Name</th>
            <th>Internal Name</th>
            <th>Regex</th>
            <th>Match Examples</th>
            <th>Notes</th>
            <th></th>
        </thead>
        <tbody>
            <?php foreach ($data as $row) {
                echo "<tr>
                    <td>$row[display]</td>
                    <td>$row[internal]</td>
                    <td>$row[phpRegex]</td>
                    <td>Examples</td>
                    <td>Notes</td>
                    <td><a href='$url' class='btn btn-primary btn-sm text-white float-right'>Add</a></td>
                </tr>";
            } ?>
        </tbody>
    </table>
</div>

<script>
    $("#pagecontent table").DataTable({
        paging: false,
        columnDefs: [{
            targets: [5],
            orderable: false,
        }]
    })
</script>

<?php
// Display the footer
$HtmlPage->PrintFooter();
?>