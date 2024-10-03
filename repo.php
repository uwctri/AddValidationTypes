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
        "display" => "Letters, Numbers, and Spaces",
        "internal" => "alpha_numeric_space",
        "phpRegex" => "/^[A-Z0-9 ]*$/i",
        "jsRegex" => "/^[A-Z0-9 ]*$/i",
        "dataType" => "text",
        "examples" => ["55 examples", "1a 3b"],
        "notes" => "You may want to add underscores or other characters"
    ],
    [
        "display" => "Letters w/ Spaces",
        "internal" => "alpha_space",
        "phpRegex" => "/^[A-Z ]*$/i",
        "jsRegex" => "/^[A-Z ]*$/i",
        "dataType" => "text",
        "examples" => ["Hello world"],
        "notes" => ""
    ],
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
        "display" => "Four Dash Four ID",
        "internal" => "id_four_four",
        "phpRegex" => "/^\d{4}-\d{4}$/i",
        "jsRegex" => "/^\d{4}-\d{4}$/i",
        "dataType" => "text",
        "examples" => ["2020-4444", "1921-3141", "0918-2412"],
        "notes" => "Example of a possible ID format"
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
        "phpRegex" => "/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/i",
        "jsRegex" => "/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/i",
        "dataType" => "text",
        "examples" => [],
        "notes" => "Allows colon or dash as delimiter."
    ],
    [
        "display" => "Date (Y-M-D, allow missing info)",
        "internal" => "date_ymd_missing",
        "phpRegex" => "/^\d{4}-\d{2}-\d{2}$/i",
        "jsRegex" => "/^\d{4}-\d{2}-\d{2}$/i",
        "dataType" => "text",
        "examples" => ["1992-03-00", "0000-00-00"],
        "notes" => "Allows for any numbers to be used, does not check for valid dates, only format."
    ],
    [
        "display" => "Mobile Phone e164 format",
        "internal" => "mobile_e164",
        "phpRegex" => "/^\+[1-9]\d{5,13}$/i",
        "jsRegex" => "/^\+[1-9]\d{5,13}$/i",
        "dataType" => "phone",
        "examples" => ["+123456", "+19981231234"],
        "notes" => ""
    ],
    [
        "display" => "UDI Barcode",
        "internal" => "udi_barcode",
        "phpRegex" => "/^\(01\)\d{14}((\((11|17)\)\d{6})|(\((10|21)\)[A-Z0-9]{1,20}))*$/i",
        "jsRegex" => "/^\(01\)\d{14}((\((11|17)\)\d{6})|(\((10|21)\)[A-Z0-9]{1,20}))*$/i",
        "dataType" => "text",
        "examples" => ["(01)51022222233336
                        (11)141231
                        (17)150707
                        (10)A213B1
                        (21)1234"],
        "notes" => ""
    ],
    [
        "display" => "Windows File Path",
        "internal" => "windows_path",
        "phpRegex" => "/^(?<drive>[A-Z]:)?(?<path>(?:[\\]?(?:[\w !#()-]+|[.]{1,2})+)*[\\])?(?<filename>(?:[.]?[\w !#()-]+)+)?[.]?$/i",
        "jsRegex" => "/^(?<drive>[A-Z]:)?(?<path>(?:[\\]?(?:[\w !#()-]+|[.]{1,2})+)*[\\])?(?<filename>(?:[.]?[\w !#()-]+)+)?[.]?$/i",
        "dataType" => "text",
        "examples" => ["C:\\foo", "\\foo", "\\foo\\fake.example"],
        "notes" => ""
    ],
    [
        "display" => "Hex Color Code",
        "internal" => "color_hex",
        "phpRegex" => "/^#([A-F0-9]{6}|[A-F0-9]{3})$/i",
        "jsRegex" => "/^#([A-F0-9]{6}|[A-F0-9]{3})$/i",
        "dataType" => "text",
        "examples" => ["#fff", "#D3D3D3", "#A0a0b0"],
        "notes" => ""
    ],
    [
        "display" => "Time (12hr)",
        "internal" => "time_str_12",
        "phpRegex" => "/^([1-9]|[0][1-9]|[1][0-2]):([0-5][0-9])(|(| )(am|pm|AM|PM))$/",
        "jsRegex" => "/^([1-9]|[0][1-9]|[1][0-2]):([0-5][0-9])(|(| )(am|pm|AM|PM))$/",
        "dataType" => "text",
        "examples" => ["03:45 pm", "12:00 AM", "9:00pm", "1:12"],
        "notes" => "This is a plain text version of a 12 hour time format. It cannot be used with normal time-related action tags or calculations."
    ],
    [
        "display" => "Japanese Hiragana",
        "internal" => "jp_hiragana",
        "phpRegex" => "/^[ぁ-んー゛゜ゝゞ]*$/u",
        "jsRegex" => "/^[ぁ-んー゛゜ゝゞ]*$/u",
        "dataType" => "text",
        "examples" => ["みずようかん"],
        "notes" => "ひらがなのみ。（あ〜ん、濁音、破擦音、長音記号、小文字の全角ひらがなも含む）"
    ],
    [
        "display" => "Letters, Spaces, Hypens (Global)",
        "internal" => "alpha_space_hyphen_unicode",
        "phpRegex" => "/^[\p{L}\p{M}\s-]+$/iu",
        "jsRegex" => "/^[\p{Letter}\p{Mark}\s-]+$/iu",
        "dataType" => "text",
        "examples" => ["Adam Núñez", "你好", "こんにちは"],
        "notes" => ""
    ]
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
                // Cleanup PHP regex
                $phpRegex = explode("/", substr($row["phpRegex"], 2));
                array_pop($phpRegex);
                $phpRegex = urlencode(rtrim(implode("/", $phpRegex), "$"));

                // Cleanup JS regex
                $jsRegex = explode("/", substr($row["jsRegex"], 2));
                array_pop($jsRegex);
                $jsRegex = urlencode(rtrim(implode("/", $jsRegex), "$"));

                // Encode things for URL
                $flags = end(explode("/", $row["phpRegex"]));
                $caseSensative = !str_contains($flags, "i");
                $unicode = str_contains($flags, "u");
                $display = urlencode($row["display"]);
                $internal = urlencode($row["internal"]);

                /// Build URL
                $addUrl = "{$url}displayName=$display&internalName=$internal&phpRegex=$phpRegex&jsRegex=$jsRegex&dataType=$row[dataType]";
                $addUrl .= $caseSensative ? "&caseSensative" : "";
                $addUrl .= $unicode ? "&unicode" : "";
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