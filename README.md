Light_Realist
===========
2019-08-09 -> 2021-07-30



A list helper for your [light](https://github.com/lingtalfi/Light) projects.

This is a [Light framework plugin](https://github.com/lingtalfi/Light/blob/master/doc/pages/plugin.md).


This is part of the [universe framework](https://github.com/karayabin/universe-snapshot).




Install
==========
Using the [planet installer](https://github.com/lingtalfi/Light_PlanetInstaller) via [light-cli](https://github.com/lingtalfi/Light_Cli)
```bash
lt install Ling.Light_Realist
```

Using the [uni](https://github.com/lingtalfi/universe-naive-importer) command.
```bash
uni import Ling/Realist
```

Or just download it and place it where you want otherwise.






Summary
===========
- [Light_Realist api](https://github.com/lingtalfi/Light_Realist/blob/master/doc/api/Ling/Light_Realist.md) (generated with [DocTools](https://github.com/lingtalfi/DocTools))
- [Documentation](https://github.com/lingtalfi/Light-Realist/tree/master/doc/pages/documentation.md)
- [Services](#services)



Services
=========


This plugin provides the following services:

- realist


 



Here is the content of the an example service configuration file:

```yaml
realist:
    instance: Ling\Light_Realist\Service\LightRealistService
    methods:
        setContainer:
            container: @container()
        setBaseDir:
            dir: ${app_dir}
        registerDynamicInjectionHandler:
            identifier: Ling.Light_Realist
            handler:
                instance: Ling\Light_Realist\DynamicInjection\LightRealistDynamicInjectionHandler

realist_action_handler:
    instance: Ling\Light_Realist\Service\LightRealistService
    methods:
        setContainer:
            container: @container()
        setBaseDir:
            dir: ${app_dir}




# --------------------------------------
# hooks
# --------------------------------------
$ajax_handler.methods_collection:
    -
        method: registerHandler
        args:
            id: Ling.Light_Realist
            handler:
                instance: Ling\Light_Realist\AjaxHandler\LightRealistAjaxHandler




```



Related
==========

- [Light_Realform](https://github.com/lingtalfi/Light_Realform): a tool to create any form
- [Light_RealGenerator](https://github.com/lingtalfi/Light_RealGenerator): a tool to generate configuration files for realist and realform


History Log
=============

- 2.1.3 -- 2021-07-30

    - fix unwanted dependency to Ling.Light_Kit_Admin 

- 2.1.2 -- 2021-06-01

    - add RequestDeclarationHelper::unregisterRequestDeclarationsByDirectory method

- 2.1.1 -- 2021-06-01

    - add RequestDeclarationHelper::registerRequestDeclarationsByDirectory method
  
- 2.1.0 -- 2021-06-01

    - replaced nugget system with open registration system (to fetch request declarations)

- 2.0.18 -- 2021-05-31

    - Removing trailing plus in lpi-deps file (to work with Light_PlanetInstaller:2.0.0 api

- 2.0.17 -- 2021-05-11

    - Update deps (by CommitWizard).

- 2.0.16 -- 2021-04-09
  
    - update realist tag transfer documentation
    - fix open-admin-table-helper.js not resetting pagination correctly in some cases
  
- 2.0.15 -- 2021-03-23

    - update api, change Light_Realist identifiers to Ling.Light_Realist
  
- 2.0.14 -- 2021-03-15

    - update planet to adapt Ling.Light:0.70.0

- 2.0.13 -- 2021-03-05

    - update README.md, add install alternative

- 2.0.12 -- 2021-02-22

    - clean assets/map dir for real (2)
  
- 2.0.11 -- 2021-02-22

    - clean assets/map dir for real
  
- 2.0.10 -- 2021-02-22

    - clean assets/map dir
  
- 2.0.9 -- 2021-02-22

    - adapt to new light universe assets organization
  
- 2.0.8 -- 2021-02-19

    - upgrade dependencies

- 2.0.7 -- 2020-12-08

    - Fix lpi-deps not using natsort.

- 2.0.6 -- 2020-12-04

    - Add lpi-deps.byml file

- 2.0.5 -- 2020-11-17

    - update BaseRealistListItemRenderer's Light_Realist.hub_link action now accepts icon option
    
- 2.0.4 -- 2020-11-16

    - update GenericActionItemHandlerTrait, add getPlanetIdByRequestId method
    
- 2.0.3 -- 2020-11-13

    - fix typo in documentation
    
- 2.0.2 -- 2020-11-13

    - fix service->getSqlColumnsByRequestDeclaration not returning expected array
    
- 2.0.1 -- 2020-11-13

    - fix typo in documentation
    
- 2.0.0 -- 2020-09-03

    - new api
    
- 1.41.0 -- 2020-08-21

    - update service to work with micro-permission3
    
- 1.40.0 -- 2020-08-20

    - update duelist, add developer_variables setting
    
- 1.39.0 -- 2020-08-20

    - update duelist, developer injection concept

- 1.38.0 -- 2020-08-20

    - update duelist, add base_where_sep setting
    
- 1.37.2 -- 2020-08-20

    - add faq developer document
    
- 1.37.1 -- 2020-08-07

    - rename LightRealistCustomServiceInterface->registerByRequestId to registerRealistByRequestId

- 1.37.0 -- 2020-08-07

    - add LightRealistCustomServiceInterface
    
- 1.36.1 -- 2020-08-06

    - fix LightRealistAjaxHandler->handle code oversight
    
- 1.36.0 -- 2020-08-06

    - add LightRealistCustomManager class
    
- 1.35.0 -- 2020-08-06

    - add LightRealistCustomServiceInterface
    
- 1.34.2 -- 2020-07-21

    - fix BaseRealistRowsRenderer->render passing potentially null value to renderColumnContent method, where string was expected
    
- 1.34.1 -- 2020-07-06

    - typo in commit date
    
- 1.34.0 -- 2020-07-06

    - change action and checkbox dynamic column names to _action and _checkbox
    
- 1.33.0 -- 2020-06-04

    - update for Light_AjaxHandler v2

- 1.32.0 -- 2020-03-06

    - update revisited the row restriction system implementation
    
- 1.31.0 -- 2020-03-05

    - update, now takes into account the row restriction system
    
- 1.30.2 -- 2019-20-28

    - fix LightRealistService->getSqlColumnsByRequestDeclaration not taking into account possible aliases
    
- 1.30.1 -- 2019-12-20

    - add explicit dependency to Light_MicroPermission service 
    
- 1.30.0 -- 2019-12-18

    - update to accommodate Light_MicroPermission 2.0
    
- 1.29.0 -- 2019-11-27

    - use of csrf_session service replaces csrf_simple

- 1.28.0 -- 2019-11-20

    - update BaseRealistRowsRenderer->renderColumnContent add Light_Realist.mixer list action
    
- 1.27.1 -- 2019-11-19

    - update BaseRealistRowsRenderer->getCsrfSimpleTokenValue add simple cache
    
- 1.27.0 -- 2019-11-19

    - add BaseRealistRowsRenderer->getCsrfSimpleTokenValue 
    
- 1.26.0 -- 2019-11-19

    - add BaseRealistRowsRenderer->getAjaxHandlerServiceUrl 
    
- 1.25.2 -- 2019-11-19

    - update BaseRealistRowsRenderer->getControllerHubRoute now becomes protected (instead of private) 

- 1.25.1 -- 2019-11-19

    - update plugin to accommodate renamed Light_ReverseRouter service (forgot one instance) 
    
- 1.25.0 -- 2019-11-19

    - update plugin to accommodate renamed Light_ReverseRouter service 

- 1.24.1 -- 2019-11-13

    - add cross column concept
    
- 1.24.0 -- 2019-11-13

    - add Light_Realist.hub_link rows_renderer type
    
- 1.23.0 -- 2019-11-12

    - update LightRealistService->getSqlColumnsByRequestDeclaration
    
- 1.22.1 -- 2019-11-12

    - update realist conception notes
    
- 1.22.0 -- 2019-11-12

    - add RealistHelper
    
- 1.21.0 -- 2019-11-12

    - implemented hiddenColumns concept
    
- 1.20.0 -- 2019-11-12

    - update RealistRowsRendererInterface->addDynamicColumn, removed label argument
    
- 1.19.0 -- 2019-11-11

    - update $column variable, now can be replaced with colExpression from an alias column expression
    
- 1.18.2 -- 2019-11-11

    - add precision to duelist documentation page

- 1.18.1 -- 2019-11-11

    - fix OpenAdminTableBaseRealistListRenderer->prepareByRequestDeclaration not updated with Light_CsrfSimple
    
- 1.18.0 -- 2019-11-07

    - switch from Light_Csrf to Light_CsrfSimple
    
- 1.17.3 -- 2019-11-06

    - add documentation precision in realist conception notes
    
- 1.17.2 -- 2019-11-05

    - fix console.log remaining in the open-admin-table-helper.js file
    
- 1.17.1 -- 2019-11-01

    - fix plugin erroneously depending from Light_Kit_Admin
    
- 1.17.0 -- 2019-10-30

    - update LightRealistService->executeRequestById, now checks for micro-permission  
    
- 1.16.1 -- 2019-10-28

    - fix OpenAdminTableBaseRealistListRenderer->renderTitle returning the title instead of printing it
    
- 1.16.0 -- 2019-10-28

    - add RealistListRendererInterface->renderTitle
    
- 1.15.0 -- 2019-10-28

    - add related_links section

- 1.14.2 -- 2019-10-24

    - add link in README.md
    
- 1.14.1 -- 2019-10-21

    - added related section in README.md

- 1.14.0 -- 2019-10-11

    - added special or, and, open_parenthesis and close_parenthesis to open admin tags
    - Revisit open admin table helper implementation
    
- 1.13.3 -- 2019-10-09

    - add GenericActionItemHandlerTrait->decorateGenericActionItemByAssets jsActionName and generateAjaxParams options, removed generate_ajax_params option
    
- 1.13.2 -- 2019-10-07

    - fix list actions nested items not handled properly
    
- 1.13.1 -- 2019-10-01

    - fix potential csrf token name conflict with multiple realist instances on the same page
    
- 1.13.0 -- 2019-10-01

    - add GenericActionItemHandlerTrait->decorateGenericActionItemByAssets modalVariables option
    
- 1.12.2 -- 2019-10-01

    - update doc
    
- 1.12.1 -- 2019-10-01

    - update doc
    
- 1.12.0 -- 2019-10-01

    - allow more compact notation for rendering.list_action_groups and rendering.list_general_actions
    
- 1.11.0 -- 2019-09-27

    - removed LightRealistListActionHandlerInterface->getHandledIds
    - removed LightRealistListGeneralActionHandlerInterface->getHandledIds
    - fix open admin table helper js pagination inconsistencies when changing nipp selector
    
- 1.10.3 -- 2019-09-26

    - fix problem with un-synchronized DocBuilder not generating doc
    
- 1.10.2 -- 2019-09-26

    - fix modal implementation forgotten part 2

- 1.10.1 -- 2019-09-26

    - fix modal implementation forgotten
    
- 1.10.0 -- 2019-09-26

    - add modal support for toolbar item and list general action items
    - fix wrong location for list general action handler class
     
- 1.9.0 -- 2019-09-25

    - update list action handler, removed the execute and getButton methods  
    - add realist-registry   
    - add general_actions concept

- 1.8.0 -- 2019-09-25

    - update open admin table helper main implementation, now uses ajax handler 
    - implemented basic csrf protection 
    - added list action handler params and right properties 
    - add ContainerAwareRealistDynamicInjectionHandler 
    - implemented list action: print 
    
- 1.7.0 -- 2019-09-19

    - add dynamic injection concept and (careless) implementation
    
- 1.6.5 -- 2019-09-18

    - fix doc links page -> pages
    
- 1.6.4 -- 2019-09-18

    - fix RealistListRendererInterface->render method not existing
    - fix doc links
    
- 1.6.3 -- 2019-09-16

    - update documentation link to susco
    
- 1.6.2 -- 2019-09-12

    - update documentation link to ric
    
- 1.6.1 -- 2019-09-05

    - fix typo in duelist, "all" special value is now recognized as a page_length (i.e. not page)
    
- 1.6.0 -- 2019-09-05

    - update duelist, "all" special value is now recognized as a page

- 1.5.0 -- 2019-09-05

    - add .oath-row-select-checkbox recommendation in the open admin table helper implementation notes
    
- 1.4.1 -- 2019-09-05

    - fix link to api
    
- 1.4.0 -- 2019-09-05

    - add the number_of_items_per_row widget in open admin protocol implementation one
    
- 1.3.0 -- 2019-09-05

    - add BaseRealistRowsRenderer->getUrlByRoute and extractRic methods. 
    
- 1.2.0 -- 2019-09-04

    - update OpenAdminTableBaseRealistListRenderer->prepareByRequestDeclaration 
    
- 1.1.1 -- 2019-09-04

    - testing new repository name sync
    
- 1.1.0 -- 2019-09-04

    - removed OpenAdminTableRendererInterface
    - added RealistListRendererInterface
    
- 1.0.1 -- 2019-09-03

    - fix doc link
    
- 1.0.0 -- 2019-09-03

    - initial commit