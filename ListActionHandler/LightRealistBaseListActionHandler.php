<?php


namespace Ling\Light_Realist\ListActionHandler;


use Ling\Light_Realist\Exception\LightRealistException;

/**
 * The LightRealistBaseListActionHandler class.
 */
abstract class LightRealistBaseListActionHandler extends LightRealistAbstractListActionHandler
{


    /**
     * Builds the LightRealistBaseListActionHandler instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->handledIds = [
            "Light_Realist-delete_rows",
            "Light_Realist-print",
            "Light_Realist-rows_to_csv",
        ];
    }



    /**
     * @implementation
     */
    public function getJsActionCode(string $actionId): string
    {
        $rawCallable = '';
        switch ($actionId) {
            case "Light_Realist-delete_rows":
                $rawCallable = $this->getJsCodeByFileName("delete_rows.js");
                break;
            case "Light_Realist-print":
                $rawCallable = $this->getJsCodeByFileName("print.js");
                break;
            default:
                break;
        }

        return $rawCallable;
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * Returns the js code found in the file identified by the given fileName.
     *
     * @param string $fileName
     * @return string
     * @throws \Exception
     */
    protected function getJsCodeByFileName(string $fileName): string
    {
        $dir = __DIR__ . "/jsActionFiles";
        $file = $dir . "/" . $fileName;
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        throw new LightRealistException("File not found with name $fileName.");
    }
}