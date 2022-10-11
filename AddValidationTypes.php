<?php

namespace UWMadison\AddValidationTypes;

use ExternalModules\AbstractExternalModule;
use RestUtility;

class AddValidationTypes extends AbstractExternalModule
{
    public function redcap_control_center()
    {
        // Custom Config page
        if ($this->isPage('ControlCenter/validation_type_setup.php')) {
            echo "<script src={$this->getUrl("main.js")}></script>";
        }
    }

    public function process()
    {
        global $Proj; // Can we remove this?

        $request = RestUtility::processRequest(false);
        $params = $request->getRequestVars();

        // Run core code
        $result = ["error" => "No valid action"];
        if ($params["action"] == "add") {
            $this->addType($params["display"], $params["internal"], $params["php"], $params["js"]);
        } elseif ($params["action"] == "remove") {
            $this->removeType($params["internal"]);
        }
        return json_encode($result);
    }

    private function addType($display, $internal, $phpRegex, $jsRegex)
    {
        // Validate everything, make sure name is unique
        // We can validate the PHP regex via var_dump(preg_match('~Valid(Regular)Expression~', '') === false);
    }

    private function removeType($name)
    {
        // We can remove only those types that we added
    }
}
