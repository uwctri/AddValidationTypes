<?php

namespace UWMadison\AddValidationTypes;

use ExternalModules\AbstractExternalModule;
use RestUtility;

class AddValidationTypes extends AbstractExternalModule
{
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
            "validationTypes" => $this->emValidationTypes(),
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
        $result = [];
        $sql = $this->query("SELECT * FROM redcap_validation_types", []);
        while ($row = $sql->fetch_assoc()) {
            $result[$row["validation_name"]] = [
                "internal" => $row["validation_name"],
                "display" => $row["validation_label"],
                "phpRegex" => $row["regex_js"],
                "jsRegex" => $row["regex_php"],
                "dataType" => $row["data_type"],
                "visible" => $row["visible"] == 1

            ];
        }
        return $result;
    }

    private function allDataTypes()
    {
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
        if (!preg_match("/^[a-zA-Z0-9 ()-:\/.]+$/", $display)) {
            $errors[] = "Incorrectly formatted display name";
        }

        // Internal Name (lower alpha, undersocre, numeric)
        if (!preg_match("/^[a-z0-9_ ]+$/", $internal)) {
            $errors[] = "Incorrectly formatted internal name";
        }

        // TODO PHP Regex (Make sure that \ is escaped, they will be wrapped in single quotes)
        // We can validate the PHP regex via var_dump(preg_match('~Valid(Regular)Expression~', '') === false);
        // TODO JS Regex (Make sure that \ is escaped, they will be wrapped in single quotes)

        // Make sure that display name isn't in use
        $allTypes = $this->allValidationTypes();
        $displayNames = array_map(function ($key) use ($allTypes) {
            return $allTypes[$key]["display"];
        }, $allTypes);
        if (in_array($display, $displayNames)) {
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
        if (count($errors) == 0) { // TODO this query is failing
            $this->query("
                INSERT INTO redcap_validation_types (validation_name, validation_label, regex_js, regex_php, data_type, legacy_value, visible)
                VALUES ('?', '?', '?', '?', '?', NULL, 0)", [$internal, $display, $jsRegex, $phpRegex, $dataType]);
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
        $this->query(" 
            DELETE from redcap_validation_types 
            WHERE validation_name = '?'", [$name]);
        $types = array_diff($types, [$name]);  // TODO this query might also be failing
        $this->setSystemSetting('typesAdded', implode(",", $types));
        return ["errors" => []];
    }
}
