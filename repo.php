<?php
require_once "../../redcap_connect.php";

// Display the header
$HtmlPage = new HtmlPage();
$HtmlPage->addStylesheet("home.css", 'screen,print');
$HtmlPage->PrintHeader();
include APP_PATH_VIEWS . 'HomeTabs.php';

// Data for page
$url = APP_PATH_WEBROOT_FULL . "redcap_v" . REDCAP_VERSION . "/ControlCenter/validation_type_setup.php?";
$localMail = explode("@", $project_contact_email);
$localMail = count($localMail) == "2" ? $localMail[1] : "";
$data = [
    [
        "display" => "Integer List",
        "internal" => "list_integer",
        "phpRegex" => "/^((-*\d+, *)*-*\d+)*$/i",
        "jsRegex" => "/^((-*\d+, *)*-*\d+)*$/i",
        "dataType" => "text",
        "examples" => ["1, 2, 3", "34,55,98", "-3, 2, 1"],
        "notes" => "Spaces are optional, but only accepted after a comma. Negative integers are accepted."
    ],
    [
        "display" => "Number List",
        "internal" => "list_number",
        "phpRegex" => "/^((-*(\d*\.?\d+)+, *)*-*(\d*\.?\d+)+)*$/i",
        "jsRegex" => "/^((-*(\d*\.?\d+)+, *)*-*(\d*\.?\d+)+)*$/i",
        "dataType" => "text",
        "examples" => ["1.0, 0.2, 3", "34,55.2,98.34", "-3, 2, 1.0"],
        "notes" => "Spaces are optional, but only accepted after a comma. Negative numbers are accepted."
    ],
    [
        "display" => "Integer Range",
        "internal" => "range_integer",
        "phpRegex" => "/^\d+-\d+$/i",
        "jsRegex" => "/^\d+-\d+$/i",
        "dataType" => "text",
        "examples" => ["0-4", "55-4", "1-5"],
        "notes" => "Spaces are not accepted"
    ],
    [
        "display" => "URL",
        "internal" => "url",
        "phpRegex" => "/^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$/",
        "jsRegex" => "/^https?:\/\/(?:www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$/",
        "dataType" => "text",
        "examples" => [],
        "notes" => "Requires fully qualified address, i.e. with protocol."
    ],
    [
        "display" => "URL (No Protocol)",
        "internal" => "url_no_protocol",
        "phpRegex" => "/^[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$/",
        "jsRegex" => "/^[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b(?:[-a-zA-Z0-9()@:%_\+.~#?&\/=]*)$/",
        "dataType" => "text",
        "examples" => [],
        "notes" => ""
    ],
    [
        "display" => "URL (Protocol Optional)",
        "internal" => "url_opt_protocol",
        "phpRegex" => "/^(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})$/",
        "jsRegex" => "/^(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})$/",
        "dataType" => "text",
        "examples" => [],
        "notes" => ""
    ],
    [
        "display" => "IPv4 Address",
        "internal" => "ipv4",
        "phpRegex" => "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i",
        "jsRegex" => "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i",
        "dataType" => "text",
        "examples" => [],
        "notes" => ""
    ],
    [
        "display" => "IPv6 Address",
        "internal" => "ipv6",
        "phpRegex" => "/^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/i",
        "jsRegex" => "/^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/i",
        "dataType" => "text",
        "examples" => [],
        "notes" => "All IPv4 addresses are accepted as valid IPv6 addresses. Acceptes zero compressed and link local."
    ],
    [
        "display" => "MAC Address",
        "internal" => "mac_addr",
        "phpRegex" => "/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/i",
        "jsRegex" => "/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/i",
        "dataType" => "text",
        "examples" => [],
        "notes" => "Allows colon or dash as delimiter."
    ],
];

if ($localMail) {
    $escapedMail = str_replace(".", "\.", $localMail);
    $data[] = [
        "display" => "Email ($localMail)",
        "internal" => "email_local_domain",
        "phpRegex" => "/^[A-Za-z0-9._%+-]+@{$escapedMail}$/i",
        "jsRegex" => "/^[A-Za-z0-9._%+-]+@{$escapedMail}$/i",
        "dataType" => "email",
        "examples" => ["example@$localMail"],
        "notes" => "Generated using your Contact Admin email"
    ];
}

?>

<style>
    #pagecontainer,
    p {
        max-width: 1200px;
    }

    ul {
        margin-bottom: 0;
        padding-left: 1rem;
    }
</style>

<div class="col">
    <h4>
        <i class="fas fa-flask"></i>
        Regex Repo
    </h4>
    <p>
        The Regex Repo is a collection of validation types and their associated regular expressions that you can add to your REDCap instance with a few clicks.
        These validation types have not been reviewed by Vanderbilt, but are maintained as a part of the <a href="https://github.com/uwctri/AddValidationTypes">
            Add Validation Types</a> external module. If you have any issues or questions please open a github issue.
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
                $caseSensative = substr($row["phpRegex"], -1) != "i";
                $phpRegex = substr(substr($row["phpRegex"], 2), 0, $caseSensative ? -2 : -3);
                $jsRegex = substr(substr($row["jsRegex"], 2), 0, $caseSensative ? -2 : -3);
                $addUrl = "{$url}displayName=$row[display]&internalName=$row[internal]&phpRegex=$phpRegex&jsRegex=$jsRegex&dataType=$row[dataType]";
                $addUrl .= $caseSensative ? "&caseSensative" : "";
                $examples = count($row["examples"]) > 0 ? "<li>" . implode("</li><li>", $row["examples"]) . "</li>" : "";
                echo "<tr>
                    <td>$row[display]</td>
                    <td>$row[internal]</td>
                    <td>$row[phpRegex]</td>
                    <td><ul>$examples</ul></td>
                    <td>$row[notes]</td>
                    <td><a href='$addUrl' class='btn btn-primary btn-sm text-white float-right'>Add</a></td>
                </tr>";
            } ?>
        </tbody>
    </table>
</div>

<script>
    $("#pagecontent table").DataTable({
        paging: false,
        columnDefs: [{
            targets: [2, 3, 4, 5],
            orderable: false,
        }]
    })
</script>

<?php
// Display the footer
$HtmlPage->PrintFooter();
?>