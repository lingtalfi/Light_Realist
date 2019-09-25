<?php


namespace Ling\Light_Realist\ListActionHandler;


/**
 * The LightRealistListActionHandlerInterface interface.
 */
interface LightRealistListActionHandlerInterface
{

    /**
     * Returns the array of handled list action ids.
     *
     * @return array
     */
    public function getHandledIds(): array;

    /**
     * Returns the js action code for this list action.
     *
     * @param string $actionId
     * @return string
     */
    public function getJsActionCode(string $actionId): string;


}