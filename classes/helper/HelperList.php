<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use PrestaShop\PrestaShop\Adapter\Routing\AdminLinkBuilder;
use PrestaShop\PrestaShop\Adapter\Routing\LegacyHelperLinkBuilder;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Routing\EntityLinkBuilderFactory;
use PrestaShop\PrestaShop\Core\Routing\Exception\BuilderNotFoundException;

/**
 * @since 1.5.0
 */
class HelperListCore extends Helper
{

    public $languages;
    public $default_form_language;

    /** @var int size which is used for lists image thumbnail generation. */
    public const LIST_THUMBNAIL_SIZE = 45;

    /** @var array Cache for query results */
    protected $_list = [];

    /** @var int Number of results in list */
    public $listTotal = 0;

    /** @var array WHERE clause determined by filter fields */
    protected $_filter;

    /** @var array Number of results in list per page (used in select field) */
    public $_pagination = [20, 50, 100, 300, 1000];

    /** @var int Default number of results in list per page */
    public $_default_pagination = 50;

    /** @var string|null ORDER BY clause determined by field/arrows in list header */
    public $orderBy;

    /** @var bool|string Default ORDER BY clause when `$orderBy` is not defined */
    public $_defaultOrderBy = false;

    /** @var array : list of vars for button delete */
    public $tpl_delete_link_vars = [];

    /** @var string|null Order way (ASC, DESC) determined by arrows in list header */
    public $orderWay;

    public $identifier;

    protected $deleted = 0;

    /** @var array use to cache texts in current language */
    public static $cache_lang = [];

    public $is_cms = false;

    public $position_identifier;

    public $table_id;

    /** @var string */
    public $title_icon = null;

    /**
     * @var array Customize list display
     *
     * align  : determine value alignment
     * prefix : displayed before value
     * suffix : displayed after value
     * image  : object image
     * icon   : icon determined by values
     * active : allow to toggle status
     */
    protected $fields_list;

    /** @var bool Content line is clickable if true */
    public $no_link = false;

    /** @var Smarty_Internal_Template|string */
    protected $header_tpl = 'list_header.tpl';

    /** @var Smarty_Internal_Template|string */
    protected $content_tpl = 'list_content.tpl';

    /** @var Smarty_Internal_Template|string */
    protected $footer_tpl = 'list_footer.tpl';

    /** @var array list of required actions for each list row */
    public $actions = [];

    /** @var array list of row ids associated with a given action for witch this action have to not be available */
    public $list_skip_actions = [];

    public $bulk_actions = false;
    public $force_show_bulk_actions = false;
    public $force_hide_bulk_actions_btn = false;
    public $specificConfirmDelete = null;
    public $colorOnBackground;

    /** @var bool If true, activates color on hover */
    public $row_hover = true;

    /** @var string|null If not null, a title will be added on that list */
    public $title = null;

    /** @var bool ask for simple header : no filters, no paginations and no sorting */
    public $simple_header = false;

    public $ajax_params = [];

    public $page;

    /** @var EntityLinkBuilderFactory */
    private $linkBuilderFactory;

    /**
     * @var string
     */
    public $shopLinkType;

    /**
     * @var string Image type
     */
    public $imageType;

    /**
     * @var string|null
     */
    public $list_id;

    /**
     * Property introduced for horizontal migration.
     * It ensures the list form filters and sorting can use framework index url instead of legacy one
     *
     * @var string|null
     */
    public $frameworkIndexUrl;

    /**
     * Raw sql query string of a list
     *
     * @var string|null
     */
    public $sql;

    /**
     * You can use $controllerMapping to add entity/controller mapping in order to have migrated links
     * in a legacy list (this requires to have correctly set the _legacy_link in the routing of course)
     *
     * exemple: $helper = new HelperList(['cart' => 'AdminCarts']);
     *
     * @param array $controllerMapping
     */
    public function __construct(array $controllerMapping = [])
    {
        $this->base_folder = 'helpers/list/';
        $this->base_tpl = 'list.tpl';

        $controllerMapping = array_merge([
            'customer' => 'AdminCustomers',
            'product' => 'AdminProducts',
            'order' => 'AdminOrders',
            'cart' => 'AdminCarts',
        ], $controllerMapping);
        $adminLinkBuilder = new AdminLinkBuilder(Context::getContext()->link, $controllerMapping);
        $this->linkBuilderFactory = new EntityLinkBuilderFactory([
            $adminLinkBuilder,
            new LegacyHelperLinkBuilder(),
        ]);

        parent::__construct();
    }

    /**
     * Return an html list given the data to fill it up.
     *
     * @param array|bool $list entries to display (rows)
     * @param array $fields_display fields (cols)
     *
     * @return string|bool
     */
    public function generateList($list, $fields_display)
    {
        // Append when we get a syntax error in SQL query
        if ($list === false) {
            $this->context->controller->warnings[] = Context::getContext()->getTranslator()->trans('Bad SQL query', [], 'Admin.Notifications.Error');

            return false;
        }

        $this->tpl = $this->createTemplate($this->base_tpl);
        $this->header_tpl = $this->createTemplate($this->header_tpl);
        $this->content_tpl = $this->createTemplate($this->content_tpl);
        $this->footer_tpl = $this->createTemplate($this->footer_tpl);

        $this->_list = $list;
        $this->fields_list = $fields_display;

        $patternsOrderBy = [
            '/^([a-z _]*!)/Ui',     // remove a. for example
            '/^([a-z _]*\.)/Ui',    // remove a! for example
            '/`/',                  // remove ` char
        ];
        if ($this->orderBy !== null) {
            $this->orderBy = preg_replace($patternsOrderBy, '', $this->orderBy);
        }

        if ($this->orderWay !== null) {
            $this->orderWay = preg_replace('/^([a-z _]*!)/Ui', '', $this->orderWay);
        }

        $this->tpl->assign([
            'header' => $this->displayListHeader(), // Display list header (filtering, pagination and column names)
            'content' => $this->displayListContent(), // Show the content of the table
            'footer' => $this->displayListFooter(), // Close list table and submit button
        ]);

        return parent::generate();
    }

    /**
     * Fetch the template for action enable.
     *
     * @param string $token
     * @param string $id
     * @param int $value state enabled or not
     * @param string $active status
     * @param int $id_category
     * @param int $id_product
     *
     * @return string
     */
    public function displayEnableLink($token, $id, $value, $active, $id_category = null, $id_product = null, $ajax = false)
    {
        $tpl_enable = $this->createTemplate('list_action_enable.tpl');
        $tpl_enable->assign([
            'ajax' => $ajax,
            'enabled' => (bool) $value,
            'url_enable' => $this->currentIndex . '&' . $this->identifier . '=' . $id . '&' . $active . $this->table . ($ajax ? '&action=' . $active . $this->table . '&ajax=' . (int) $ajax : '') .
                ((int) $id_category && (int) $id_product ? '&id_category=' . (int) $id_category : '') . ($this->page && $this->page > 1 ? '&page=' . (int) $this->page : '') . '&token=' . ($token != null ? $token : $this->token),
        ]);

        return $tpl_enable->fetch();
    }

    public function displayListContent()
    {
        if (isset($this->fields_list['position'])) {
            if ($this->position_identifier) {
                if (isset($this->position_group_identifier)) {
                    $position_group_identifier = Tools::getIsset($this->position_group_identifier) ? (int) Tools::getValue($this->position_group_identifier) : $this->position_group_identifier;
                } else {
                    $position_group_identifier = (int) Tools::getValue('id_' . ($this->is_cms ? 'cms_' : '') . 'category', ($this->is_cms ? '1' : Category::getRootCategory()->id));
                }
            } else {
                $position_group_identifier = Category::getRootCategory()->id;
            }

            $positions = array_map(function ($elem) { return (int) ($elem['position']); }, $this->_list);
            sort($positions);
        }

        // key_to_get is used to display the correct product category or cms category after a position change
        $identifier = in_array($this->identifier, ['id_category', 'id_cms_category']) ? '_parent' : '';
        if ($identifier) {
            $key_to_get = 'id_' . ($this->is_cms ? 'cms_' : '') . 'category' . $identifier;
        }

        foreach ($this->_list as $index => $tr) {
            $id = null;
            if (isset($tr[$this->identifier])) {
                $id = $tr[$this->identifier];
            }
            $name = isset($tr['name']) ? $tr['name'] : null;

            if ($this->shopLinkType) {
                $this->_list[$index]['short_shop_name'] = Tools::strlen($tr['shop_name']) > 15 ? Tools::substr($tr['shop_name'], 0, 15) . '...' : $tr['shop_name'];
            }

            $is_first = true;
            // Check all available actions to add to the current list row
            foreach ($this->actions as $action) {
                //Check if the action is available for the current row
                if (!array_key_exists($action, $this->list_skip_actions) || !in_array($id, $this->list_skip_actions[$action])) {
                    $method_name = 'display' . ucfirst($action) . 'Link';

                    if (method_exists($this->context->controller, $method_name)) {
                        $this->_list[$index][$action] = $this->context->controller->$method_name($this->token, $id, $name);
                    } elseif ($this->module instanceof Module && method_exists($this->module, $method_name)) {
                        $this->_list[$index][$action] = $this->module->$method_name($this->token, $id, $name);
                    } elseif (method_exists($this, $method_name)) {
                        $this->_list[$index][$action] = $this->$method_name($this->token, $id, $name);
                    }
                }

                if ($is_first && isset($this->_list[$index][$action])) {
                    $is_first = false;

                    if (!preg_match('/a\s*.*class/', $this->_list[$index][$action])) {
                        $this->_list[$index][$action] = preg_replace(
                            '/href\s*=\s*\"([^\"]*)\"/',
                            'href="$1" class="btn btn-default"',
                            $this->_list[$index][$action]
                        );
                    } elseif (!preg_match('/a\s*.*class\s*=\s*\".*btn.*\"/', $this->_list[$index][$action])) {
                        $this->_list[$index][$action] = preg_replace(
                            '/a(\s*.*)class\s*=\s*\"(.*)\"/',
                            'a $1 class="$2 btn btn-default"',
                            $this->_list[$index][$action]
                        );
                    }
                }
            }

            $this->_list[$index]['link'] = in_array('view', $this->actions) ? $this->getViewLink($this->token, $id) : $this->getEditLink($this->token, $id);

            // @todo skip action for bulk actions
            // $this->_list[$index]['has_bulk_actions'] = true;
            foreach ($this->fields_list as $key => $params) {
                $tmp = explode('!', $key);
                $key = isset($tmp[1]) ? $tmp[1] : $tmp[0];

                if (isset($params['active'])) {
                    // If method is defined in calling controller, use it instead of the Helper method
                    if (method_exists($this->context->controller, 'displayEnableLink')) {
                        $calling_obj = $this->context->controller;
                    } elseif (isset($this->module) && method_exists($this->module, 'displayEnableLink')) {
                        $calling_obj = $this->module;
                    } else {
                        $calling_obj = $this;
                    }

                    if (!isset($params['ajax'])) {
                        $params['ajax'] = false;
                    }
                    $this->_list[$index][$key] = $calling_obj->displayEnableLink(
                        $this->token,
                        $id,
                        $tr[$key],
                        $params['active'],
                        Tools::getValue('id_category'),
                        Tools::getValue('id_product'),
                        $params['ajax']
                    );
                } elseif (isset($params['activeVisu'])) {
                    $this->_list[$index][$key] = (bool) $tr[$key];
                } elseif (isset($params['position'])) {
                    $this->_list[$index][$key] = [
                        'position' => $tr[$key],
                        'position_url_down' => $this->currentIndex .
                            (isset($key_to_get) ? '&' . $key_to_get . '=' . (int) ($position_group_identifier ?? 0) : '') .
                            '&' . $this->position_identifier . '=' . $id .
                            '&way=1&position=' . ((int) $tr['position'] + 1) . '&token=' . $this->token,
                        'position_url_up' => $this->currentIndex .
                            (isset($key_to_get) ? '&' . $key_to_get . '=' . (int) ($position_group_identifier ?? 0) : '') .
                            '&' . $this->position_identifier . '=' . $id .
                            '&way=0&position=' . ((int) $tr['position'] - 1) . '&token=' . $this->token,
                    ];
                } elseif (isset($params['image'])) {
                    // item_id is the product id in a product image context, else it is the image id.
                    $item_id = isset($params['image_id']) ? $tr[$params['image_id']] : $id;
                    if ($params['image'] != 'p' || Configuration::get('PS_LEGACY_IMAGES')) {
                        $path_to_image = _PS_IMG_DIR_ . $params['image'] . '/' . $item_id . (isset($tr['id_image']) ? '-' . (int) $tr['id_image'] : '') . '.' . $this->imageType;
                    } else {
                        $path_to_image = _PS_IMG_DIR_ . $params['image'] . '/' . Image::getImgFolderStatic($tr['id_image']) . (int) $tr['id_image'] . '.' . $this->imageType;
                    }
                    $this->_list[$index][$key] = ImageManager::thumbnail($path_to_image, $this->table . '_mini_' . $item_id . '_' . $this->context->shop->id . '.' . $this->imageType, self::LIST_THUMBNAIL_SIZE, $this->imageType);
                } elseif (isset($params['icon'], $tr[$key]) && (isset($params['icon'][$tr[$key]]) || isset($params['icon']['default']))) {
                    if (!$this->bootstrap) {
                        if (isset($params['icon'][$tr[$key]]) && is_array($params['icon'][$tr[$key]])) {
                            $this->_list[$index][$key] = [
                                'src' => $params['icon'][$tr[$key]]['src'],
                                'alt' => $params['icon'][$tr[$key]]['alt'],
                            ];
                        } else {
                            $this->_list[$index][$key] = [
                                'src' => isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'],
                                'alt' => isset($params['icon'][$tr[$key]]) ? $params['icon'][$tr[$key]] : $params['icon']['default'],
                            ];
                        }
                    } elseif (isset($params['icon'][$tr[$key]])) {
                        $this->_list[$index][$key] = $params['icon'][$tr[$key]];
                    }
                } elseif (isset($params['type']) && $params['type'] == 'float') {
                    $this->_list[$index][$key] = rtrim(rtrim($tr[$key], '0'), '.');
                } elseif (array_key_exists($key, $tr)) {
                    $echo = $tr[$key];
                    if (isset($params['callback'])) {
                        $callback_obj = (isset($params['callback_object'])) ? $params['callback_object'] : $this->context->controller;
                        $this->_list[$index][$key] = call_user_func_array([$callback_obj, $params['callback']], [$echo, $tr]);
                    } else {
                        $this->_list[$index][$key] = $echo;
                    }
                }
            }
        }

        $showShopColumn = $this->shopLinkType;

        $isMultiShopActive = Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');

        $this->content_tpl->assign(array_merge($this->tpl_vars, [
            'shop_link_type' => $showShopColumn,
            'multishop_active' => $isMultiShopActive,
            'name' => $name ?? null,
            'position_identifier' => $this->position_identifier,
            'identifier' => $this->identifier,
            'table' => $this->table,
            'token' => $this->token,
            'color_on_bg' => $this->colorOnBackground,
            'position_group_identifier' => $position_group_identifier ?? false,
            'bulk_actions' => $this->bulk_actions,
            'positions' => $positions ?? null,
            'order_by' => $this->orderBy,
            'order_way' => $this->orderWay,
            'is_cms' => $this->is_cms,
            'fields_display' => $this->fields_list,
            'list' => $this->_list,
            'actions' => $this->actions,
            'no_link' => $this->no_link,
            'current_index' => $this->currentIndex,
            'view' => in_array('view', $this->actions),
            'edit' => in_array('edit', $this->actions),
            'has_actions' => !empty($this->actions),
            'list_skip_actions' => $this->list_skip_actions,
            'row_hover' => $this->row_hover,
            'list_id' => $this->list_id ?? $this->table,
            'checked_boxes' => Tools::getValue(($this->list_id ?? $this->table) . 'Box'),
        ]));

        return $this->content_tpl->fetch();
    }

    /**
     * Display duplicate action link.
     */
    public function displayDuplicateLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('list_action_duplicate.tpl');
        if (!array_key_exists('Bad SQL query', self::$cache_lang)) {
            self::$cache_lang['Duplicate'] = Context::getContext()->getTranslator()->trans('Duplicate', [], 'Admin.Actions');
        }

        if (!array_key_exists('Copy images too?', self::$cache_lang)) {
            $translator = Context::getContext()->getTranslator();
            self::$cache_lang['Copy images too?'] = $translator->trans(
                'This will copy the images too. If you wish to proceed, click "%yes_label%". If not, click "%no_label%".',
                [
                    '%yes_label%' => $translator->trans('Yes', [], 'Admin.Global'),
                    '%no_label%' => $translator->trans('No', [], 'Admin.Global'),
                ],
                'Admin.Catalog.Notification'
            );
        }

        $duplicate = $this->currentIndex . '&' . $this->identifier . '=' . $id . '&duplicate' . $this->table;

        $confirm = self::$cache_lang['Copy images too?'];

        if (($this->table == 'product') && !Image::hasImages($this->context->language->id, (int) $id)) {
            $confirm = '';
        }

        $tpl->assign([
            'href' => $this->currentIndex . '&' . $this->identifier . '=' . $id . '&view' . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'action' => self::$cache_lang['Duplicate'],
            'confirm' => $confirm,
            'location_ok' => $duplicate . '&token=' . ($token != null ? $token : $this->token),
            'location_ko' => $duplicate . '&noimage=1&token=' . ($token ? $token : $this->token),
        ]);

        return $tpl->fetch();
    }

    /**
     * Display action show details of a table row
     * This action need an ajax request with a return like this:
     *   {
     *     use_parent_structure: true // If false, data need to be an html
     *     data:
     *       [
     *         {field_name: 'value'}
     *       ],
     *     fields_display: // attribute $fields_list of the admin controller
     *   }
     * or somethins like this:
     *   {
     *     use_parent_structure: false // If false, data need to be an html
     *     data:
     *       '<p>My html content</p>',
     *     fields_display: // attribute $fields_list of the admin controller
     *   }.
     */
    public function displayDetailsLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('list_action_details.tpl');
        if (!array_key_exists('Details', self::$cache_lang)) {
            self::$cache_lang['Details'] = Context::getContext()->getTranslator()->trans('Details', [], 'Admin.Global');
        }

        $ajax_params = $this->ajax_params;
        if (!is_array($ajax_params) || !isset($ajax_params['action'])) {
            $ajax_params['action'] = 'details';
        }

        $tpl->assign([
            'id' => $id,
            'href' => $this->currentIndex . '&' . $this->identifier . '=' . $id . '&details' . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'controller' => str_replace('Controller', '', get_class($this->context->controller)),
            'token' => $token != null ? $token : $this->token,
            'action' => self::$cache_lang['Details'],
            'params' => $ajax_params,
            'json_params' => json_encode($ajax_params),
        ]);

        return $tpl->fetch();
    }

    /**
     * Display view action link.
     */
    public function displayViewLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('list_action_view.tpl');
        if (!array_key_exists('View', self::$cache_lang)) {
            self::$cache_lang['View'] = Context::getContext()->getTranslator()->trans('View', [], 'Admin.Actions');
        }

        $href = $this->getViewLink($token, $id);
        $tpl->assign([
            'href' => $href,
            'action' => self::$cache_lang['View'],
        ]);

        return $tpl->fetch();
    }

    /**
     * Display edit action link.
     */
    public function displayEditLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('list_action_edit.tpl');
        if (!array_key_exists('Edit', self::$cache_lang)) {
            self::$cache_lang['Edit'] = Context::getContext()->getTranslator()->trans('Edit', [], 'Admin.Actions');
        }

        $href = $this->getEditLink($token, $id);
        $tpl->assign([
            'href' => $href,
            'action' => self::$cache_lang['Edit'],
            'id' => $id,
        ]);

        return $tpl->fetch();
    }

    /**
     * Display delete action link.
     */
    public function displayDeleteLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('list_action_delete.tpl');

        if (!array_key_exists('Delete', self::$cache_lang)) {
            self::$cache_lang['Delete'] = Context::getContext()->getTranslator()->trans('Delete', [], 'Admin.Actions');
        }

        if (!array_key_exists('DeleteItem', self::$cache_lang)) {
            self::$cache_lang['DeleteItem'] = Context::getContext()->getTranslator()->trans('Delete selected item?', [], 'Admin.Notifications.Info');
        }

        if (!array_key_exists('Name', self::$cache_lang)) {
            self::$cache_lang['Name'] = Context::getContext()->getTranslator()->trans('Name:', [], 'Admin.Global');
        }

        if (null !== $name) {
            $name = addcslashes('\n\n' . self::$cache_lang['Name'] . ' ' . $name, '\'');
        }

        $href = $this->currentIndex . '&' . $this->identifier . '=' . $id . '&delete' . $this->table . '&token=' . ($token != null ? $token : $this->token);

        switch ($this->currentIndex) {
            case 'index.php?controller=AdminProducts':
            case 'index.php?tab=AdminProducts':
                // New architecture modification: temporary behavior to switch between old and new controllers.
                $pagePreference = SymfonyContainer::getInstance()->get('prestashop.core.admin.page_preference_interface');
                $redirectLegacy = $pagePreference->getTemporaryShouldUseLegacyPage('product');
                if (!$redirectLegacy && $this->identifier == 'id_product') {
                    $href = Context::getContext()->link->getAdminLink('AdminProducts', true, ['id_product' => $id, 'deleteproduct' => 1]);
                }

                break;
            default:
        }

        $data = [
            $this->identifier => $id,
            'href' => $href,
            'action' => self::$cache_lang['Delete'],
            'confirm' => Tools::safeOutput(self::$cache_lang['DeleteItem'] . $name),
        ];

        if (!empty($this->specificConfirmDelete)) {
            $data['confirm'] = '\r' . $this->specificConfirmDelete;
        }

        $tpl->assign(array_merge($this->tpl_delete_link_vars, $data));

        return $tpl->fetch();
    }

    /**
     * Display default action link.
     */
    public function displayDefaultLink($token, $id, $name = null)
    {
        $tpl = $this->createTemplate('list_action_default.tpl');
        if (!array_key_exists('Default', self::$cache_lang)) {
            self::$cache_lang['Default'] = Context::getContext()->getTranslator()->trans('Default', [], 'Admin.Global');
        }

        $tpl->assign([
            'href' => $this->currentIndex . '&' . $this->identifier . '=' . (int) $id . '&default' . $this->table . '&token=' . ($token != null ? $token : $this->token),
            'action' => self::$cache_lang['Default'],
            'name' => $name,
        ]);

        return $tpl->fetch();
    }

    /**
     * Display list header (filtering, pagination and column names).
     */
    public function displayListHeader()
    {
        if (!isset($this->list_id)) {
            $this->list_id = $this->table;
        }

        $id_cat = (int) Tools::getValue('id_' . ($this->is_cms ? 'cms_' : '') . 'category');

        /* Determine total page number */
        $pagination = $this->_default_pagination;
        if (in_array((int) Tools::getValue($this->list_id . '_pagination'), $this->_pagination)) {
            $pagination = (int) Tools::getValue($this->list_id . '_pagination');
        } elseif (isset($this->context->cookie->{$this->list_id . '_pagination'}) && $this->context->cookie->{$this->list_id . '_pagination'}) {
            $pagination = $this->context->cookie->{$this->list_id . '_pagination'};
        }

        $total_pages = max(1, ceil($this->listTotal / $pagination));

        $identifier = Tools::getIsset($this->identifier) ? '&' . $this->identifier . '=' . (int) Tools::getValue($this->identifier) : '';

        if ($this->frameworkIndexUrl) {
            $action = $this->frameworkIndexUrl;
        } else {
            $action = $this->currentIndex . $identifier . '&token=' . $this->token . '#' . $this->list_id;
        }

        /* Determine current page number */
        $page = (int) Tools::getValue('submitFilter' . $this->list_id);

        if (!$page) {
            $page = 1;
        }

        if ($page > $total_pages) {
            $page = $total_pages;
        }

        $this->page = (int) $page;

        /* Choose number of results per page */
        $selected_pagination = Tools::getValue(
            $this->list_id . '_pagination',
            isset($this->context->cookie->{$this->list_id . '_pagination'}) ? $this->context->cookie->{$this->list_id . '_pagination'} : $this->_default_pagination
        );

        if (!isset($this->table_id) && $this->position_identifier && (int) Tools::getValue($this->position_identifier, 1)) {
            $this->table_id = substr($this->identifier, 3, strlen($this->identifier));
        }

        if ($this->position_identifier && ($this->orderBy == 'position' && $this->orderWay != 'DESC')) {
            $table_dnd = true;
        }

        $prefix = str_replace(['admin', 'controller'], '', Tools::strtolower($this->controller_name));
        $ajax = false;
        foreach ($this->fields_list as $key => $params) {
            if (!isset($params['type'])) {
                $params['type'] = 'text';
            }

            $value_key = $prefix . $this->list_id . 'Filter_' . (array_key_exists('filter_key', $params) ? $params['filter_key'] : $key);
            if ($key == 'active' && strpos($key, '!') !== false) {
                $keys = explode('!', $params['filter_key']);
                $value_key = $keys[1];
            }
            $value = Context::getContext()->cookie->{$value_key};
            if (!$value && Tools::getIsset($value_key)) {
                $value = Tools::getValue($value_key);
            }

            switch ($params['type']) {
                case 'bool':
                    if (isset($params['ajax']) && $params['ajax']) {
                        $ajax = true;
                    }

                    break;

                case 'date':
                case 'datetime':
                    if (is_string($value)) {
                        $value = json_decode($value, true);
                    }

                    if (!isset($value[0]) || !isset($value[1]) || !Validate::isCleanHtml($value[0]) || !Validate::isCleanHtml($value[1])) {
                        $value = '';
                    }

                    $name = $this->list_id . 'Filter_' . (isset($params['filter_key']) ? $params['filter_key'] : $key);
                    $name_id = str_replace('!', '__', $name);

                    $params['id_date'] = $name_id;
                    $params['name_date'] = $name;

                    $this->context->controller->addJqueryUI('ui.datepicker');

                    break;

                case 'select':
                    foreach ($params['list'] as $option_value => $option_display) {
                        if (isset(Context::getContext()->cookie->{$prefix . $this->list_id . 'Filter_' . $params['filter_key']})
                            && Context::getContext()->cookie->{$prefix . $this->list_id . 'Filter_' . $params['filter_key']} == $option_value
                            && Context::getContext()->cookie->{$prefix . $this->list_id . 'Filter_' . $params['filter_key']} != '') {
                            $this->fields_list[$key]['select'][$option_value]['selected'] = 'selected';
                        }
                    }

                    break;

                case 'text':
                    if (!Validate::isCleanHtml($value)) {
                        $value = '';
                    }
            }

            $params['value'] = $value;
            $this->fields_list[$key] = $params;
        }

        $has_value = false;
        $has_search_field = false;

        foreach ($this->fields_list as $key => $field) {
            if (isset($field['value']) && $field['value'] !== false && $field['value'] !== '') {
                if (is_array($field['value']) && trim(implode('', $field['value'])) == '') {
                    continue;
                }

                $has_value = true;

                break;
            }
            if (!(isset($field['search']) && $field['search'] === false)) {
                $has_search_field = true;
            }
        }

        Context::getContext()->smarty->assign([
            'page' => $page,
            'simple_header' => $this->simple_header,
            'total_pages' => $total_pages,
            'selected_pagination' => $selected_pagination,
            'pagination' => $this->_pagination,
            'list_total' => $this->listTotal,
            'sql' => isset($this->sql) && $this->sql ? str_replace('\n', ' ', str_replace('\r', '', $this->sql)) : false,
            'token' => $this->token,
            'table' => $this->table,
            'bulk_actions' => $this->bulk_actions,
            'show_toolbar' => $this->show_toolbar,
            'toolbar_scroll' => $this->toolbar_scroll,
            'toolbar_btn' => $this->toolbar_btn,
            'has_bulk_actions' => $this->hasBulkActions($has_value),
            'hide_bulk_actions_btn' => $this->force_hide_bulk_actions_btn,
            'filters_has_value' => (bool) $has_value,
        ]);

        if (null !== $this->title_icon) {
            Context::getContext()->smarty->assign(['icon' => $this->title_icon]);
        }

        $isMultiShopActive = Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');

        $this->header_tpl->assign(array_merge([
            'ajax' => $ajax,
            'title' => array_key_exists('title', $this->tpl_vars) ? $this->tpl_vars['title'] : $this->title,
            'show_filters' => ((count($this->_list) > 1 && $has_search_field) || $has_value),
            'currentIndex' => $this->currentIndex,
            'frameworkIndexUrl' => $this->frameworkIndexUrl,
            'action' => $action,
            'is_order_position' => $this->position_identifier && $this->orderBy == 'position',
            'order_way' => $this->orderWay,
            'order_by' => $this->orderBy,
            'fields_display' => $this->fields_list,
            'delete' => in_array('delete', $this->actions),
            'identifier' => $this->identifier,
            'id_cat' => $id_cat,
            'shop_link_type' => $this->shopLinkType,
            'multishop_active' => $isMultiShopActive,
            'has_actions' => !empty($this->actions),
            'table_id' => isset($this->table_id) ? $this->table_id : null,
            'table_dnd' => isset($table_dnd) ? $table_dnd : null,
            'name' => isset($name) ? $name : null,
            'name_id' => isset($name_id) ? $name_id : null,
            'row_hover' => $this->row_hover,
            'js_dir' => _PS_JS_DIR_,
            'list_id' => isset($this->list_id) ? $this->list_id : $this->table,
            'controller_name' => $this->controller_name,
        ], $this->tpl_vars));

        return $this->header_tpl->fetch();
    }

    public function hasBulkActions($has_value = false)
    {
        if ($this->force_show_bulk_actions) {
            return true;
        }

        if (count($this->_list) <= 1 && !$has_value) {
            return false;
        }

        if (count($this->list_skip_actions)
            && isset($this->bulk_actions) && is_array($this->bulk_actions) && count($this->bulk_actions)) {
            foreach ($this->bulk_actions as $action => $data) {
                if (array_key_exists($action, $this->list_skip_actions)) {
                    foreach ($this->_list as $key => $row) {
                        if (!in_array($row[$this->identifier], $this->list_skip_actions[$action])) {
                            return true;
                        }
                    }

                    return false;
                }
            }
        }

        return !empty($this->bulk_actions);
    }

    /**
     * Close list table and submit button.
     */
    public function displayListFooter()
    {
        if (!isset($this->list_id)) {
            $this->list_id = $this->table;
        }

        $this->footer_tpl->assign(array_merge($this->tpl_vars, [
            'current' => $this->currentIndex,
            'list_id' => $this->list_id,
        ]));

        return $this->footer_tpl->fetch();
    }

    /**
     * @param string|null $token
     * @param int $id
     *
     * @return string
     *
     * @throws BuilderNotFoundException
     * @throws PrestaShopException
     */
    protected function getViewLink($token, $id)
    {
        $linkBuilder = $this->linkBuilderFactory->getBuilderFor($this->table);
        $parameters = $this->buildLinkParameters($token, $id);

        return $linkBuilder->getViewLink($this->table, $parameters);
    }

    /**
     * @param string|null $token
     * @param int $id
     *
     * @return string
     *
     * @throws BuilderNotFoundException
     * @throws PrestaShopException
     */
    protected function getEditLink($token, $id)
    {
        $linkBuilder = $this->linkBuilderFactory->getBuilderFor($this->table);
        $parameters = $this->buildLinkParameters($token, $id);

        return $linkBuilder->getEditLink($this->table, $parameters);
    }

    /**
     * @param string|null $token
     * @param int $id
     *
     * @return array
     */
    protected function buildLinkParameters($token, $id)
    {
        $parameters = [
            $this->identifier => $id,
            'current_index' => $this->currentIndex,
            'token' => $token != null ? $token : $this->token,
        ];
        if ($this->page && $this->page > 1) {
            $parameters['page'] = $this->page;
        }

        return $parameters;
    }

    public function setActions(array $actions): void
    {
        $this->actions = $actions;
    }
}
