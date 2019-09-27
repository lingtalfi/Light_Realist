<?php


namespace Ling\Light_Realist\Service;


use Ling\BabyYaml\BabyYamlUtil;
use Ling\Bat\SmartCodeTool;
use Ling\Light\ServiceContainer\LightServiceContainerAwareInterface;
use Ling\Light\ServiceContainer\LightServiceContainerInterface;
use Ling\Light_Csrf\Service\LightCsrfService;
use Ling\Light_Database\LightDatabasePdoWrapper;
use Ling\Light_Realist\ActionHandler\LightRealistActionHandlerInterface;
use Ling\Light_Realist\DynamicInjection\RealistDynamicInjectionHandlerInterface;
use Ling\Light_Realist\Exception\LightRealistException;
use Ling\Light_Realist\ListActionHandler\LightRealistListActionHandlerInterface;
use Ling\Light_Realist\ListGeneralActionHandler\LightRealistListGeneralActionHandlerInterface;
use Ling\Light_Realist\Rendering\RealistListRendererInterface;
use Ling\Light_Realist\Rendering\RealistRowsRendererInterface;
use Ling\Light_Realist\Tool\LightRealistTool;
use Ling\Light_User\LightUserInterface;
use Ling\ParametrizedSqlQuery\ParametrizedSqlQueryUtil;

/**
 * The LightRealistService class.
 *
 * More information in the @page(realist conception notes).
 *
 *
 * This class uses babyYaml files as the main storage.
 * Note: if you need another storage, you might want to extend this class, or create a similar service.
 *
 *
 *
 *
 *
 * Conception notes
 * ------------------
 *
 * So basically, I plan to implement three different methods to call sql requests.
 * This service could be the only service you use for handling ALL the sql requests of your app,
 * if so you wanted (at least that's my goal to provide such a tool with this service).
 *
 *
 * The three methods will be distribute amongst two php methods:
 *
 * - executeRequestById
 * - executeRequest
 *
 * The executeRequest is for common and/or free requests.
 * The executeRequestById splits internally in two different methods:
 *
 * - one to execute parametrized requests stored in the babyYaml files. This is the main use of this method.
 * - the other will let us go even more dynamic (more php code), in case babyYaml static style isn't enough to handle
 *      every situations.
 *
 * Now at the moment you're reading this the class might just a work in progress.
 * I like to implement the features only when there is a concrete need for it, and so for I didn't need
 * the dynamic side, nor the free requests.
 *
 *
 *
 *
 *
 */
class LightRealistService
{


    /**
     * This property holds the container for this instance.
     * @var LightServiceContainerInterface
     */
    protected $container;


    /**
     * This property holds the base directory for this instance.
     * It should be set to the application directory.
     * @var string
     */
    protected $baseDir;

    /**
     * This property holds the parametrizedSqlQuery for this instance.
     * @var ParametrizedSqlQueryUtil
     */
    protected $parametrizedSqlQuery;

    /**
     * This property holds the array of realistRowsRenderer instances.
     * It's an array of str:identifier => instance:realistRowsRenderer.
     *
     *
     * @var RealistRowsRendererInterface[]
     */
    protected $realistRowsRenderers;


    /**
     * This property holds the (ric/ajax) actionHandlers for this instance.
     * It's an array of LightRealistActionHandlerInterface instances.
     *
     * @var LightRealistActionHandlerInterface[]
     */
    protected $actionHandlers;


    /**
     * This property holds the listActionHandlers for this instance.
     * It's an array of LightRealistListActionHandlerInterface instances.
     *
     * @var LightRealistListActionHandlerInterface[]
     */
    protected $listActionHandlers;

    /**
     * This property holds the listGeneralActionHandlers for this instance.
     * @var LightRealistListGeneralActionHandlerInterface[]
     */
    protected $listGeneralActionHandlers;


    /**
     * This property holds the listRenderers for this instance.
     * It's an array of identifier => RealistListRendererInterface instance
     *
     * @var RealistListRendererInterface[]
     */
    protected $listRenderers;

    /**
     * This property holds the dynamicInjectionHandlers for this instance.
     * It's an array of identifier => RealistDynamicInjectionHandlerInterface
     *
     * Usually the identifier is a plugin name.
     *
     * @var RealistDynamicInjectionHandlerInterface[]
     */
    protected $dynamicInjectionHandlers;

    /**
     * This property holds the _requestDeclarationCache for this instance.
     * An array of requestId => requestDeclaration array
     * @var array
     */
    private $_requestDeclarationCache;


    /**
     * Builds the LightRealistService instance.
     */
    public function __construct()
    {
        $this->container = null;
        $this->baseDir = "/tmp";
        $this->parametrizedSqlQuery = new ParametrizedSqlQueryUtil();
        $this->realistRowsRenderers = [];
        $this->actionHandlers = [];
        $this->listActionHandlers = [];
        $this->listGeneralActionHandlers = [];
        $this->listRenderers = [];
        $this->dynamicInjectionHandlers = [];
        $this->_requestDeclarationCache = [];
    }


    /**
     *
     * Executes the realist identified by the given requestId, and returns an array with the following
     * properties (see @page(the realist conception notes) for more details):
     *
     *
     * - nb_total_rows: int, the total number of rows without "where" filtering
     * - nb_rows: int, the number of returned rows (i.e. WITH the "where" filtering)
     * - rows: array, the raw rows returned by the sql query
     * - rows_html: string, the html of the rows, as shaped by the realist configuration
     * - sql_query: string, the executed sql query (intend: debug)
     * - markers: array, the markers used along with the executed sql query (intend: debug)
     *
     *
     *
     *
     * The requestId is a string with the following structure:
     *
     * - requestId: fileId:queryId
     *
     * With:
     *
     * - fileId: the relative path (relative to the baseDir) to the babyYaml file storing the data, without
     *      the .byml extension.
     * - queryId: the request declaration identifier used inside the babyYaml file.
     *
     * Params an array containing the following:
     *
     * - ?tags: the tags to use with the request. (see @page(the realist tag transfer protocol) for more details).
     * - ?csrf_token: string|null. the value of the csrf token to check against. If not passed or null, no csrf checking will be performed.
     * - ?csrf_token_pass: bool. If true, will bypass the csrf_token validation.
     *          Usually, you only want to use this if you've already checked for another csrf token earlier (i.e. you
     *          already trust that the user is who she claimed she is).
     *
     *
     * If the sql query is not valid, an exception will be thrown.
     *
     *
     *
     *
     * @param string $requestId
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function executeRequestById(string $requestId, array $params = []): array
    {
        $requestDeclaration = $this->getConfigurationArrayByRequestId($requestId);


        //--------------------------------------------
        // CHECKING CSRF TOKEN
        //--------------------------------------------
        $csrfTokenPass = $params['csrf_token_pass'] ?? false;
        $csrfToken = $requestDeclaration['csrf_token'] ?? null;
        if (false === $csrfTokenPass) {
            if (null !== $csrfToken) {
                $csrfTokenName = $csrfToken['name'] ?? "realist-request";
                $this->checkCsrfToken($csrfTokenName, $params);
            }
        }


        $tags = $params['tags'] ?? [];


//        $this->parametrizedSqlQuery->setLogger($this->container->get('logger'));
        $sqlQuery = $this->parametrizedSqlQuery->getSqlQuery($requestDeclaration, $tags);
        $markers = $sqlQuery->getMarkers();

        $stmt = $sqlQuery->getSqlQuery();
        $countStmt = $sqlQuery->getCountSqlQuery();


        /**
         * @var $db LightDatabasePdoWrapper
         */
        $db = $this->container->get("database");

        try {

            $rows = $db->fetchAll($stmt, $markers);
            $countRow = $db->fetch($countStmt, $markers);
        } catch (\Exception $e) {
            // sometimes it's easier to have the stmt displayed too, when debugging
            $debugMsg = "<ul>
<li><b>Query</b>: $stmt</li>
<li><b>Error</b>: {$e->getMessage()}</li>
</ul>
";
            throw new LightRealistException($debugMsg);
        }

        //--------------------------------------------
        // RENDERING THE ROWS
        //--------------------------------------------
        $rendering = $requestDeclaration['rendering'] ?? [];
        $rowsRenderer = $rendering['rows_renderer'] ?? [];
        $rowsRendererInstance = null;

        if (array_key_exists('class', $rowsRenderer)) {
            $rowsRendererInstance = new $rowsRenderer['class'];
        } else {
            if (array_key_exists("identifier", $rowsRenderer)) {
                $identifier = $rowsRenderer['identifier'];
                $rowsRendererInstance = $this->realistRowsRenderers[$identifier] ?? null;
            } else {
                $this->error("Bad configuration error. For the \"rendering.rows_renderer\" setting, either the \"class\" or the \"identifier\" must be set.");
            }
        }

        if ($rowsRendererInstance instanceof RealistRowsRendererInterface) {


            if ($rowsRendererInstance instanceof LightServiceContainerAwareInterface) {
                $rowsRendererInstance->setContainer($this->container);
            }

            $ric = $requestDeclaration['ric'] ?? [];
            $rowsRendererInstance->setRic($ric);


            // adding regular types
            $types = $rowsRenderer['types'] ?? [];
            foreach ($types as $columnName => $type) {
                if (false === is_array($type)) {
                    $type = [$type, []];
                }
                $theType = array_shift($type);
                $theOptions = $type;
                $rowsRendererInstance->setColumnType($columnName, $theType, $theOptions);
            }

            // adding special checkbox column
            if (array_key_exists('checkbox_column', $rowsRenderer)) {
                $conf = $rowsRenderer['checkbox_column'];
                $name = $conf['name'] ?? 'checkbox';
                $label = $conf['label'] ?? '#';
                $rowsRendererInstance->addDynamicColumn($name, $label, 'pre');
            }


            // adding special action column
            if (array_key_exists('action_column', $rowsRenderer)) {
                $conf = $rowsRenderer['action_column'];
                $name = $conf['name'] ?? 'action';
                $label = $conf['label'] ?? 'Actions';
                $rowsRendererInstance->addDynamicColumn($name, $label, 'post');
            }

            $rowsHtml = $rowsRendererInstance->render($rows);

        } else {
            $type = gettype($rowsRendererInstance);
            $this->error("The rowsRenderer is not an instance of RealistRowsRendererInterface ($type given).");
        }


        // adding extra info to the output
        $limit = $sqlQuery->getLimit();
        $nbTotalRows = (int)current($countRow);
        $currentPageFirst = 0;
        $currentPageLast = $nbTotalRows;
        $nbItemsPerPage = $nbTotalRows;
        $nbPagesTotal = 1;
        $page = 1;

        if (null !== $limit) {
            list($currentPageFirst, $nbItemsPerPage) = $limit;
            $currentPageLast = $currentPageFirst + $nbItemsPerPage;
            if ($currentPageLast > $nbTotalRows) {
                $currentPageLast = $nbTotalRows;
            }
            $nbPagesTotal = ceil($nbTotalRows / $nbItemsPerPage);
            if (0 !== $nbTotalRows) {
                $page = (int)(($currentPageFirst * $nbPagesTotal) / $nbTotalRows + 1);
            } else {
                $page = 1;
            }
        }
        if (0 === (int)$nbPagesTotal) {
            $nbPagesTotal = 1;
        }


        return [
            'nb_total_rows' => $nbTotalRows,
            'current_page_first' => $currentPageFirst,
            'current_page_last' => $currentPageLast,
            'nb_pages' => $nbPagesTotal,
            'nb_items_per_page' => $nbItemsPerPage,
            'page' => $page,
            'rows' => $rows, //
            'rows_html' => $rowsHtml,
            'sql_query' => $stmt,
            'markers' => $markers,
        ];
    }


//    public function executeRequest(string $request, array $params = [])
//    {
//
//    }


    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * Sets the container.
     *
     * @param LightServiceContainerInterface $container
     */
    public function setContainer(LightServiceContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Sets the baseDir.
     *
     * @param string $baseDir
     */
    public function setBaseDir(string $baseDir)
    {
        $this->baseDir = $baseDir;
    }


    /**
     * Registers a duelistRowsRenderer.
     *
     * @param string $identifier
     * @param RealistRowsRendererInterface $realistRowsRenderer
     */
    public function registerRealistRowsRenderer(string $identifier, RealistRowsRendererInterface $realistRowsRenderer)
    {
        $this->realistRowsRenderers[$identifier] = $realistRowsRenderer;
    }

    /**
     * Registers a list renderer.
     *
     * @param string $identifier
     * @param RealistListRendererInterface $renderer
     */
    public function registerListRenderer(string $identifier, RealistListRendererInterface $renderer)
    {
        $this->listRenderers[$identifier] = $renderer;
    }

    /**
     * Registers an action handler.
     *
     * @param LightRealistActionHandlerInterface $handler
     */
    public function registerActionHandler(LightRealistActionHandlerInterface $handler)
    {
        $ids = $handler->getHandledIds();
        foreach ($ids as $id) {
            $this->actionHandlers[$id] = $handler;
        }
    }


    /**
     * Registers a list action handler.
     * List action ids should be formatted like this:
     *
     * - list action id: {pluginName}.{listActionName}
     *
     *
     * @param string $pluginName
     * @param LightRealistListActionHandlerInterface $handler
     */
    public function registerListActionHandler(string $pluginName, LightRealistListActionHandlerInterface $handler)
    {
        $this->listActionHandlers[$pluginName] = $handler;
    }


    /**
     * Registers a list general action handler.
     * List general action ids should be formatted like this:
     *
     * - list general action id: {pluginName}.{listGeneralActionName}
     *
     * @param string $pluginName
     * @param LightRealistListGeneralActionHandlerInterface $handler
     */
    public function registerListGeneralActionHandler(string $pluginName, LightRealistListGeneralActionHandlerInterface $handler)
    {
        $this->listGeneralActionHandlers[$pluginName] = $handler;
    }


    /**
     * Registers a @page(dynamic injection handler).
     * @param string $identifier
     * @param RealistDynamicInjectionHandlerInterface $handler
     */
    public function registerDynamicInjectionHandler(string $identifier, RealistDynamicInjectionHandlerInterface $handler)
    {
        $this->dynamicInjectionHandlers[$identifier] = $handler;
    }


    /**
     * Returns the action handler identified by the given id.
     *
     * @param string $id
     * @return LightRealistActionHandlerInterface
     * @throws \Exception
     */
    public function getActionHandler(string $id): LightRealistActionHandlerInterface
    {
        if (array_key_exists($id, $this->actionHandlers)) {
            return $this->actionHandlers[$id];
        }
        throw new LightRealistException("No action handler found with id $id.");
    }


    /**
     * Returns the list action handler identified by the given id.
     *
     * @param string $id
     * @return LightRealistListActionHandlerInterface
     * @throws \Exception
     */
    public function getListActionHandler(string $id): LightRealistListActionHandlerInterface
    {
        $pluginName = explode(".", $id)[0];
        if (array_key_exists($pluginName, $this->listActionHandlers)) {
            return $this->listActionHandlers[$pluginName];
        }
        throw new LightRealistException("List action handler not found with id $id.");
    }

    /**
     * Returns the list general action handler identified by the given id.
     *
     * @param string $id
     * @return LightRealistListGeneralActionHandlerInterface
     * @throws \Exception
     */
    public function getListGeneralActionHandler(string $id): LightRealistListGeneralActionHandlerInterface
    {
        $pluginName = explode(".", $id)[0];
        if (array_key_exists($pluginName, $this->listGeneralActionHandlers)) {
            return $this->listGeneralActionHandlers[$pluginName];
        }
        throw new LightRealistException("List general action handler not found with id $id.");
    }


    /**
     * Returns a configured list renderer.
     *
     *
     * @param string $requestId
     * @return RealistListRendererInterface
     * @throws \Exception
     */
    public function getListRendererByRequestId(string $requestId): RealistListRendererInterface
    {
        $requestDeclaration = $this->getConfigurationArrayByRequestId($requestId);
        $rendering = $requestDeclaration['rendering'] ?? [];
        $listRendererConf = $rendering['list_renderer'] ?? [];
        $listRendererId = $listRendererConf['identifier'] ?? null;
        if (null === $listRendererId) {
            $this->error("The list renderer id was not defined (requestId=$requestId).");
        }
        if (false === array_key_exists($listRendererId, $this->listRenderers)) {
            $this->error("List renderer not found with identifier $listRendererId (requestId=$requestId).");
        }


        $listRenderer = $this->listRenderers[$listRendererId];

        // a list renderer should be able to prepare itself.
        // Note: some might need the service container?, we could pass it to them if that happened,
        // but for now we try to be conservative and pass only one argument as long as possible.
        $listRenderer->prepareByRequestDeclaration($requestId, $requestDeclaration, $this->container);
        return $listRenderer;
    }


    /**
     * Prepares the given list action group array.
     *
     * This method is mainly used to translate an action id string from
     * the request declaration into actual javascript code, with the help of
     * the ListActionHandler objects.
     *
     * It also removes the actions which the user doesn't have the permission for.
     *
     * The given groups array structure is an array of @page(toolbar items).
     *
     * @param array $groups
     * @throws \Exception
     */
    public function prepareListActionGroups(array &$groups)
    {

        $user = null;

        foreach ($groups as $k => $group) {
            // handling recursion
            if (array_key_exists("items", $group)) {
                $groupItems = $group['items'];
                $this->prepareListActionGroups($groupItems);
                $group['items'] = $groupItems;
            } else {

                //--------------------------------------------
                // PERMISSION FILTERING
                //--------------------------------------------
                if (array_key_exists("right", $group)) {
                    $right = $group['right'];
                    if (null === $user) {
                        /**
                         * @var $user LightUserInterface
                         */
                        $user = $this->container->get("user_manager")->getUser();
                    }
                    if (false === $user->hasRight($right)) {
                        unset($groups[$k]);
                        continue;
                    }
                }
                if (array_key_exists("micro_permission", $group)) {
                    $mp = $group['micro_permission'];
                    if (false === $this->container->get("micro_permission")->hasMicroPermission($mp)) {
                        unset($groups[$k]);
                        continue;
                    }
                }


                //--------------------------------------------
                // JS CODE TRANSLATION
                //--------------------------------------------
                if (array_key_exists('action_id', $group)) {
                    $actionId = $group['action_id'];

                    $handler = $this->getListActionHandler($actionId);
                    $rawCallable = $handler->getJsActionCode($actionId);
                    $groups[$k]['js_code'] = $rawCallable;

                    $modal = $handler->getModalCode($actionId);
                    if (null !== $modal) {
                        $this->container->get('html_page_copilot')->addModal($modal);
                    }

                } else {
                    // assuming this is a parent, we can ignore it
                }
            }
        }
    }


    /**
     * Prepares the given action group array.
     *
     * This method is mainly used to translate an action id string from
     * the request declaration into actual javascript code, with the help of
     * the ListGeneralActionHandler objects.
     *
     * It also removes the actions which the user doesn't have the permission for.
     *
     * See the @page(list general actions) for more details.
     *
     * @param array $generalActions
     * @throws \Exception
     */
    public function prepareListGeneralActions(array &$generalActions)
    {

        $user = null;

        foreach ($generalActions as $k => $item) {

            //--------------------------------------------
            // JS CODE TRANSLATION
            //--------------------------------------------
            if (array_key_exists('action_id', $item)) {
                $actionId = $item['action_id'];
                $handler = $this->getListGeneralActionHandler($actionId);
                $rawCallable = $handler->getJsActionCode($actionId);

                $generalActions[$k]['js_code'] = $rawCallable;

                $modal = $handler->getModalCode($actionId);
                if (null !== $modal) {
                    $this->container->get('html_page_copilot')->addModal($modal);
                }


            } else {
                // assuming this is a parent, we can ignore it
            }

        }
    }


    /**
     * Returns the configuration array corresponding to the given request id.
     *
     * See the @page(request id) page for more info about the request id.
     *
     * @param string $requestId
     * @return array
     * @throws \Exception
     */
    public function getConfigurationArrayByRequestId(string $requestId): array
    {

        if (array_key_exists($requestId, $this->_requestDeclarationCache)) {
            return $this->_requestDeclarationCache[$requestId];
        }

        if ('not implemented yet' === "requestIdHandlerInterface") {
            $ret = [];
        } else {

            //--------------------------------------------
            // FALLBACK MECHANISM
            //--------------------------------------------
            $p = explode(":", $requestId, 3);
            $n = count($p);
            if (3 === $n) {
                list($pluginName, $resourceId, $requestDeclarationId) = $p;
            } elseif (2 === $n) {
                list($pluginName, $resourceId) = $p;
                $requestDeclarationId = 'default';
            } else {
                $this->error("Invalid syntax for the requestId $requestId using the fallback mechanism.");
            }


            $fileId = "config/data/$pluginName/Light_Realist/$resourceId";
            $filePath = $this->baseDir . "/$fileId.byml";
            if (false === file_exists($filePath)) {
                $this->error("File not found: $filePath for requestId $requestId.");
            }

            $arr = BabyYamlUtil::readFile($filePath);
            if (false === array_key_exists($requestDeclarationId, $arr)) {
                $this->error("Query not found with request declaration id: $requestDeclarationId, in file $filePath.");
            }
            $ret = $arr[$requestDeclarationId];


            // dynamic injection phase
            SmartCodeTool::replaceSmartCodeFunction($ret, "REALIST", function ($identifier) {
                $handler = $this->getDynamicInjectionHandler($identifier);
                $args = func_get_args();
                array_shift($args);
                return $handler->handle($args);
            });
        }


        $this->_requestDeclarationCache[$requestId] = $ret;
        return $ret;
    }


    /**
     * Performs the csrf validation if necessary (i.e. if the csrf_token key is defined in the @page(generic action item) configuration),
     * and throws an exception in case of a csrf validation failure.
     *
     * The params array originates from the user (i.e. not trusted).
     *
     * @param array $item
     * @param array $params
     * @throws \Exception
     */
    public function checkCsrfTokenByGenericActionItem(array $item, array $params)
    {
        if (array_key_exists("csrf_token", $item)) {
            if (array_key_exists("csrf_token", $params)) {
                $tokenValue = $params['csrf_token'];
                LightRealistTool::checkAjaxToken($item['csrf_token'], $tokenValue, $this->container);
            } else {
                $this->error("The csrf_token entry was not provided with the post params.");
            }
        }
    }


    /**
     * Checks whether there is a permission restriction for the given @page(generic action item),
     * and if so checks whether the user is granted that permission.
     * If not, this method throws an exception.
     *
     * Note: both the @page(permission) and @page(micro permissions) systems are checked.
     *
     *
     *
     *
     * @param array $item
     * @throws \Exception
     */
    public function checkPermissionByGenericActionItem(array $item)
    {
        if (array_key_exists("right", $item)) {
            $right = $item['right'];

            /**
             * @var $user LightUserInterface
             */
            $user = $this->container->get("user_manager")->getUser();
            if (false === $user->hasRight($right)) {
                $this->error("Permission denied, missing the permission: $right.");
            }
        }
        if (array_key_exists("micro_permission", $item)) {
            $mp = $item['micro_permission'];

            /**
             * @var $user LightUserInterface
             */
            if (false === $this->container->get("micro_permission")->hasMicroPermission($mp)) {
                $this->error("Permission denied, missing the micro-permission: $mp.");
            }
        }
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * Throws the given error message as an exception.
     *
     *
     * @param string $message
     * @throws LightRealistException
     */
    protected function error(string $message)
    {
        throw new LightRealistException($message);
    }


    /**
     * Returns the realist dynamic injection handler associated with the given identifier,
     * or throws an exception if it's not there.
     *
     * @param string $identifier
     * @return RealistDynamicInjectionHandlerInterface
     * @throws \Exception
     */
    protected function getDynamicInjectionHandler(string $identifier): RealistDynamicInjectionHandlerInterface
    {
        if (array_key_exists($identifier, $this->dynamicInjectionHandlers)) {
            $handler = $this->dynamicInjectionHandlers[$identifier];
            if ($handler instanceof LightServiceContainerAwareInterface) {
                $handler->setContainer($this->container);
            }
            return $handler;
        }
        throw new LightRealistException("Dynamic injection handler not found with identifier $identifier.");
    }


    /**
     * Checks whether the csrf token is valid, throws an exception if that's not the case.
     *
     * @param string $tokenName
     * @param array $params
     * @throws \Exception
     */
    protected function checkCsrfToken(string $tokenName, array $params)
    {
        if (array_key_exists("csrf_token", $params)) {
            /**
             * @var $csrf LightCsrfService
             */
            $csrf = $this->container->get("csrf");
            if (true === $csrf->isValid($tokenName, $params['csrf_token'], true)) {
                return;
            }
            $this->error("Invalid csrf token value provided for token $tokenName.");
        }
        $this->error("The \"csrf_token\" key was not provided with the payload.");

    }
}