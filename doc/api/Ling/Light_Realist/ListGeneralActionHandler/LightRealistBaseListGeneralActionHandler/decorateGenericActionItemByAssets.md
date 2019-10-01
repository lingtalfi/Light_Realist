[Back to the Ling/Light_Realist api](https://github.com/lingtalfi/Light_Realist/blob/master/doc/api/Ling/Light_Realist.md)<br>
[Back to the Ling\Light_Realist\ListGeneralActionHandler\LightRealistBaseListGeneralActionHandler class](https://github.com/lingtalfi/Light_Realist/blob/master/doc/api/Ling/Light_Realist/ListGeneralActionHandler/LightRealistBaseListGeneralActionHandler.md)


LightRealistBaseListGeneralActionHandler::decorateGenericActionItemByAssets
================



LightRealistBaseListGeneralActionHandler::decorateGenericActionItemByAssets — the calling class source file.




Description
================


protected [LightRealistBaseListGeneralActionHandler::decorateGenericActionItemByAssets](https://github.com/lingtalfi/Light_Realist/blob/master/doc/api/Ling/Light_Realist/ListGeneralActionHandler/LightRealistBaseListGeneralActionHandler/decorateGenericActionItemByAssets.md)(string $actionName, array &$item, string $requestId, string $dir, array $options = []) : void




Decorates the given [generic action item](https://github.com/lingtalfi/Light_Realist/blob/master/doc/pages/generic-action-item.md) using mostly asset files found around
the calling class source file.


The available options are:

- modalVariables: an array of variables to pass to the modal template (if you use a modal template only).
             Inside the modal template, the variables are accessible via the $z variable (which represents this modalVariables array).
- generate_ajax_params: bool=true, whether to automatically generate ajax parameters. See the code for more info.
                     The ajax parameters basically will be transmitted to the js handler via the **params** argument of the f callable.




Parameters
================


- actionName

    

- item

    

- requestId

    

- dir

    

- options

    


Return values
================

Returns void.


Exceptions thrown
================

- [Exception](http://php.net/manual/en/class.exception.php).&nbsp;







Source Code
===========
See the source code for method [LightRealistBaseListGeneralActionHandler::decorateGenericActionItemByAssets](https://github.com/lingtalfi/Light_Realist/blob/master/ListGeneralActionHandler/LightRealistBaseListGeneralActionHandler.php#L82-L124)


See Also
================

The [LightRealistBaseListGeneralActionHandler](https://github.com/lingtalfi/Light_Realist/blob/master/doc/api/Ling/Light_Realist/ListGeneralActionHandler/LightRealistBaseListGeneralActionHandler.md) class.

Previous method: [setContainer](https://github.com/lingtalfi/Light_Realist/blob/master/doc/api/Ling/Light_Realist/ListGeneralActionHandler/LightRealistBaseListGeneralActionHandler/setContainer.md)<br>Next method: [getTableNameByRequestId](https://github.com/lingtalfi/Light_Realist/blob/master/doc/api/Ling/Light_Realist/ListGeneralActionHandler/LightRealistBaseListGeneralActionHandler/getTableNameByRequestId.md)<br>
