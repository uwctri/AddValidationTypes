<?php

namespace UWMadison\AddValidationTypes;

use ExternalModules\AbstractExternalModule;
use RestUtility;

class AddValidationTypes extends AbstractExternalModule
{
    private $inputRegex = [
        "displayName" => "/^[a-zA-Z0-9 ()-:\/.]+$/",
        "internalName" => "/^[a-z0-9_ ]+$/",
        "phpRegex" => "", // Validated with preg_match
        "jsRegex" => ""   // Validated in js
    ];

    public function redcap_control_center()
    {
        if ($this->isPage('ControlCenter/validation_type_setup.php')) {
            $settings = json_encode($this->loadSettings());
            echo "<script>ExternalModules.addValTypes = {$settings}</script>";
            echo "<style>#val_table { display:none; }</style>";
            echo "<script src={$this->getUrl("main.js")}></script>";
        }
    }

    public function process()
    {
        $request = RestUtility::processRequest(false);
        $params = $request->getRequestVars();

        // Run core code
        $result = ["errors" => ["No valid action"]];
        if ($params["action"] == "add") {
            $result = $this->addType($params["display"], $params["internal"], $params["phpRegex"], $params["jsRegex"], $params["dataType"]);
        } elseif ($params["action"] == "remove") {
            $result = $this->removeType($params["name"]);
        }
        return json_encode($result);
    }

    private function loadSettings()
    {
        return [
            "regex" => $this->inputRegex,
            "emTypes" => $this->emValidationTypes(),
            "validationTypes" => $this->allValidationTypes(),
            "dataTypes" => $this->allDataTypes(),
            "csrf"   => $this->getCSRFToken(),
            "router" => $this->getUrl('router.php')
        ];
    }

    private function emValidationTypes()
    {
        return array_filter(array_map('trim', explode(",", $this->getSystemSetting('typesAdded'))));
    }

    private function allValidationTypes()
    {
        // Grab most data from the validation table and format
        $result = [];
        $sql = $this->query("SELECT * FROM redcap_validation_types", []);
        $dataTypes = $this->allDataTypes();
        while ($row = $sql->fetch_assoc()) {
            // Skip pulling the regex, we won't use it 
            $result[$row["validation_name"]] = [
                "internal" => htmlspecialchars($row["validation_name"], ENT_QUOTES),
                "display" => htmlspecialchars($row["validation_label"], ENT_QUOTES),
                "dataType" => htmlspecialchars($row["data_type"], ENT_QUOTES),
                "visible" => $row["visible"] == 1
            ];
        }
        return $result;
    }

    private function allDataTypes()
    {
        // Fetch db metadata and parse the enum for data_type col
        $sql = $this->query("SHOW COLUMNS FROM redcap_validation_types LIKE 'data_type'", []);
        $enum = $sql->fetch_assoc()["Type"];
        preg_match("/enum\((.*)\)$/", $enum, $matches);
        return array_map(function ($value) {
            return trim($value, "'");
        }, explode(',', $matches[1]));
    }

    private function addType($display, $internal, $phpRegex, $jsRegex, $dataType)
    {
        $errors = [];
        // Display Name (alpha, numeric, space, limited special chars ()-:/.)
        if (!preg_match($this->inputRegex["displayName"], $display)) {
            $errors[] = "Incorrectly formatted display name";
        }

        // Internal Name (lower alpha, undersocre, numeric)
        if (!preg_match($this->inputRegex["internalName"], $internal)) {
            $errors[] = "Incorrectly formatted internal name";
        }

        // PHP Regex validation
        if (preg_match($phpRegex, '') === false) {
            $errors[] = "Invalid PHP (PCRE) Regex submitted";
        }

        // Make sure that display name isn't in use
        $allTypes = $this->allValidationTypes();
        $displayNames = array_map(function ($key) use ($allTypes) {
            return str_replace(" ", "", strtolower($allTypes[$key]["display"]));
        }, $allTypes);
        $trimDisplay = str_replace(" ", "", strtolower($display));
        if (in_array($trimDisplay, $displayNames)) {
            $errors[] = "Display name too similar to existing name";
        }

        // Make sure that internal name isn't in use
        if (in_array($internal, array_keys($allTypes))) {
            $errors[] = "Internal name is already in use";
        }

        // Make sure the data type is real
        if (!in_array($dataType, $this->allDataTypes())) {
            $errors[] = "Invalid data type";
        }

        // Perform the DB Update and update the EM's setting
        if (count($errors) == 0) {
            $this->query("
                INSERT INTO redcap_validation_types (validation_name, validation_label, regex_js, regex_php, data_type, legacy_value, visible)
                VALUES (?, ?, ?, ?, ?, NULL, 0)", [$internal, $display, $jsRegex, $phpRegex, $dataType]);
            $types = $this->emValidationTypes();
            $types[] = $internal;
            $this->setSystemSetting("typesAdded", implode(",", $types));
        }

        return ["errors" => $errors];
    }

    private function removeType($name)
    {
        // We can remove only those types that we added
        $types = $this->emValidationTypes();
        if (!in_array($name, $types)) {
            return ["errors" => ["Bad validation type or type was not added by EM"]];
        }

        // Perform delete
        $this->query(" 
            DELETE from redcap_validation_types 
            WHERE validation_name = ?", [$name]);
        $types = array_diff($types, [$name]);
        $this->setSystemSetting('typesAdded', implode(",", $types));
        return ["errors" => []];
    }
}
