<?php
/**
 * Whmsonic Module
 *
 * @package blesta
 * @subpackage blesta.components.modules.whmsonic
 * @copyright Copyright (c) 2010, Phillips Data, Inc.
 * @license http://www.blesta.com/license/ The Blesta License Agreement
 * @link http://www.blesta.com/ Blesta
 */
class Whmsonic extends Module
{

    /**
     * @var string The version of this module
     */
    private static $version = "1.0.0";
    /**
     * @var string The authors of this module
     */
    private static $authors = array(array('name' => "Phillips Data, Inc.", 'url' => "http://www.blesta.com"));

    /**
     * Initializes the module
     */
    public function __construct()
    {
        // Load components required by this module
        Loader::loadComponents($this, array("Input", "Net"));
        $this->Http = $this->Net->create("Http");

        // Load the language required by this module
        Language::loadLang("whmsonic", null, dirname(__FILE__) . DS . "language" . DS);
    }

    /**
     * Returns the name of this module
     *
     * @return string The common name of this module
     */
    public function getName()
    {
        return Language::_("Whmsonic.name", true);
    }

    /**
     * Returns the version of this gateway
     *
     * @return string The current version of this gateway
     */
    public function getVersion()
    {
        return self::$version;
    }

    /**
     * Returns the name and url of the authors of this module
     *
     * @return array The name and url of the authors of this module
     */
    public function getAuthors()
    {
        return self::$authors;
    }

    /**
     * Returns all tabs to display to an admin when managing a service whose
     * package uses this module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
     */
    public function getAdminTabs($package)
    {
        return array();
    }

    /**
     * Returns all tabs to display to a client when managing a service whose
     * package uses this module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
     */
    public function getClientTabs($package)
    {
        return array();
    }

    /**
     * Returns a noun used to refer to a module row (e.g. "Server")
     *
     * @return string The noun used to refer to a module row
     */
    public function moduleRowName()
    {
        return Language::_("Whmsonic.module_row", true);
    }

    /**
     * Returns a noun used to refer to a module row in plural form (e.g. "Servers", "VPSs", "Reseller Accounts", etc.)
     *
     * @return string The noun used to refer to a module row in plural form
     */
    public function moduleRowNamePlural()
    {
        return Language::_("Whmsonic.module_row_plural", true);
    }

    /**
     * Returns a noun used to refer to a module group (e.g. "Server Group")
     *
     * @return string The noun used to refer to a module group
     */
    public function moduleGroupName()
    {
        return Language::_("Whmsonic.module_group", true);
    }

    /**
     * Returns the key used to identify the primary field from the set of module row meta fields.
     *
     * @return string The key used to identify the primary field from the set of module row meta fields
     */
    public function moduleRowMetaKey()
    {
        return "server_name";
    }

    /**
     * Returns an array of available service deligation order methods. The module
     * will determine how each method is defined. For example, the method "first"
     * may be implemented such that it returns the module row with the least number
     * of services assigned to it.
     *
     * @return array An array of order methods in key/value paris where the key is the type to be stored for the group and value is the name for that option
     * @see Module::selectModuleRow()
     */
    public function getGroupOrderOptions()
    {
        return array('first' => Language::_("Whmsonic.order_options.first", true));
    }

    /**
     * Determines which module row should be attempted when a service is provisioned
     * for the given group based upon the order method set for that group.
     *
     * @return int The module row ID to attempt to add the service with
     * @see Module::getGroupOrderOptions()
     */
    public function selectModuleRow($module_group_id)
    {
        if (!isset($this->ModuleManager))
            Loader::loadModels($this, array("ModuleManager"));

        $group = $this->ModuleManager->getGroup($module_group_id);

        if ($group) {
            switch ($group->add_order) {
                default:
                case "first":

                    foreach ($group->rows as $row) {
                        if ($row->meta->account_limit > (isset($row->meta->account_count) ? $row->meta->account_count : 0))
                            return $row->id;
                    }

                    break;
            }
        }
        return 0;
    }

    /**
     * Returns all fields used when adding/editing a package, including any
     * javascript to execute when the page is rendered with these fields.
     *
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containing the fields to render as well as any additional HTML markup to include
     */
    public function getPackageFields($vars = null)
    {
        Loader::loadHelpers($this, array("Html"));

        $fields = new ModuleFields();

        // Fetch all packages available for the given server or server group
        $module_row = null;
        if (isset($vars->module_group) && $vars->module_group == "") {
            if (isset($vars->module_row) && $vars->module_row > 0) {
                $module_row = $this->getModuleRow($vars->module_row);
            } else {
                $rows = $this->getModuleRows();
                if (isset($rows[0]))
                    $module_row = $rows[0];
                unset($rows);
            }
        } else {
            // Fetch the 1st server from the list of servers in the selected group
            $rows = $this->getModuleRows($vars->module_group);

            if (isset($rows[0]))
                $module_row = $rows[0];
            unset($rows);
        }


        $client_type = $fields->label(Language::_("Whmsonic.package_fields.client_type", true), "client_type");
        $client_type->attach($fields->fieldSelect("meta[client_type]", array(
            "External" => Language::_("Whmsonic.package_fields.client_type.external", true),
            "internal" => Language::_("Whmsonic.package_fields.client_type.internal", true),
        ), $this->Html->ifSet($vars->meta['client_type'])), array('id' => "client_type"));
        $fields->setField($client_type);

        $bitrate = $fields->label(Language::_("Whmsonic.package_fields.bitrate", true), "bitrate");
        $bitrate->attach($fields->fieldSelect("meta[bitrate]", array(
            "32" => "32",
            "64" => "64",
            "128" => "128",
        ), $this->Html->ifSet($vars->meta['bitrate'])), array('id' => "bitrate"));
        $fields->setField($bitrate);

        $hspace = $fields->label(Language::_("Whmsonic.package_fields.hspace", true), "hspace");
        $hspace->attach($fields->fieldText("meta[hspace]", $this->Html->ifSet($vars->meta['hspace'])), array('id' => "hspace"));
        $fields->setField($hspace);

        $bandwidth = $fields->label(Language::_("Whmsonic.package_fields.bandwidth", true), "bandwidth");
        $bandwidth->attach($fields->fieldText("meta[bandwidth]", $this->Html->ifSet($vars->meta['bandwidth'])), array('id' => "bandwidth"));
        $fields->setField($bandwidth);

        $listeners = $fields->label(Language::_("Whmsonic.package_fields.listeners", true), "listeners");
        $listeners->attach($fields->fieldText("meta[listeners]", $this->Html->ifSet($vars->meta['listeners'])), array('id' => "listeners"));
        $fields->setField($listeners);

        $autodj = $fields->label(Language::_("Whmsonic.package_fields.autodj", true), "autodj");
        $autodj->attach($fields->fieldSelect("meta[autodj]", array(
            "yes" => Language::_("Whmsonic.package_fields.autodj.yes", true),
            "no" => Language::_("Whmsonic.package_fields.autodj.no", true),
        ), $this->Html->ifSet($vars->meta['autodj'])), array('id' => "autodj"));
        $fields->setField($autodj);

        return $fields;
    }

    /**
     * Returns an array of key values for fields stored for a module, package,
     * and service under this module, used to substitute those keys with their
     * actual module, package, or service meta values in related emails.
     *
     * @return array A multi-dimensional array of key/value pairs where each key is one of 'module', 'package', or 'service' and each value is a numerically indexed array of key values that match meta fields under that category.
     * @see Modules::addModuleRow()
     * @see Modules::editModuleRow()
     * @see Modules::addPackage()
     * @see Modules::editPackage()
     * @see Modules::addService()
     * @see Modules::editService()
     */
    public function getEmailTags()
    {
        return array(
            'module' => array('ip_address'),
            'package' => array('client_type', 'bitrate', 'hspace', 'bandwidth', 'listeners', 'autodj'),
            'service' => array('username', "password")
        );
    }

    /**
     * Validates input data when attempting to add a package, returns the meta
     * data to save when adding a package. Performs any action required to add
     * the package on the remote server. Sets Input errors on failure,
     * preventing the package from being added.
     *
     * @param array An array of key/value pairs used to add the package
     * @return array A numerically indexed array of meta fields to be stored for this package containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function addPackage(array $vars = null)
    {

        // Set rules to validate input data
        $this->Input->setRules($this->getPackageRules($vars));

        // Build meta data to return
        $meta = array();
        if ($this->Input->validates($vars)) {


            // Return all package meta fields
            foreach ($vars['meta'] as $key => $value) {
                $meta[] = array(
                    'key' => $key,
                    'value' => $value,
                    'encrypted' => 0
                );
            }
        }
        return $meta;
    }

    /**
     * Validates input data when attempting to edit a package, returns the meta
     * data to save when editing a package. Performs any action required to edit
     * the package on the remote server. Sets Input errors on failure,
     * preventing the package from being edited.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array An array of key/value pairs used to edit the package
     * @return array A numerically indexed array of meta fields to be stored for this package containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function editPackage($package, array $vars = null)
    {

        // Set rules to validate input data
        $this->Input->setRules($this->getPackageRules($vars));

        // Build meta data to return
        $meta = array();
        if ($this->Input->validates($vars)) {

            // Return all package meta fields
            foreach ($vars['meta'] as $key => $value) {
                $meta[] = array(
                    'key' => $key,
                    'value' => $value,
                    'encrypted' => 0
                );
            }
        }
        return $meta;
    }

    /**
     * Returns the rendered view of the manage module page
     *
     * @param mixed $module A stdClass object representing the module and its rows
     * @param array $vars An array of post data submitted to or on the manager module page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the manager module page
     */
    public function manageModule($module, array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View("manage", "default");
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView("components" . DS . "modules" . DS . "whmsonic" . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, array("Form", "Html", "Widget"));

        $this->view->set("module", $module);

        return $this->view->fetch();
    }

    /**
     * Returns the rendered view of the add module row page
     *
     * @param array $vars An array of post data submitted to or on the add module row page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the add module row page
     */
    public function manageAddRow(array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View("add_row", "default");
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView("components" . DS . "modules" . DS . "whmsonic" . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, array("Form", "Html", "Widget"));

        // Set unspecified checkboxes
        if (!empty($vars)) {
            if (empty($vars['use_ssl']))
                $vars['use_ssl'] = "false";
        }

        $this->view->set("vars", (object)$vars);
        return $this->view->fetch();
    }

    /**
     * Returns the rendered view of the edit module row page
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     * @param array $vars An array of post data submitted to or on the edit module row page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the edit module row page
     */
    public function manageEditRow($module_row, array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View("edit_row", "default");
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView("components" . DS . "modules" . DS . "whmsonic" . DS);
        $this->Input->setRules($this->getRowRules($vars));

        // Load the helpers required for this view
        Loader::loadHelpers($this, array("Form", "Html", "Widget"));


        if (empty($vars))
            $vars = $module_row->meta;
        else {
            // Set unspecified checkboxes
            if (empty($vars['use_ssl']))
                $vars['use_ssl'] = "false";
        }

        $this->view->set("vars", (object)$vars);
        return $this->view->fetch();
    }

    /**
     * Adds the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being added. Returns a set of data, which may be
     * a subset of $vars, that is stored for this module row
     *
     * @param array $vars An array of module info to add
     * @return array A numerically indexed array of meta fields for the module row containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     */
    public function addModuleRow(array &$vars)
    {
        $meta_fields = array("server_name", "ip_address", "use_ssl", "password");
        $encrypted_fields = array("password");

        // Set unspecified checkboxes
        if (empty($vars['use_ssl']))
            $vars['use_ssl'] = "false";

        $this->Input->setRules($this->getRowRules($vars));

        // Validate module row
        if ($this->Input->validates($vars)) {

            // Build the meta data for this row
            $meta = array();
            foreach ($vars as $key => $value) {

                if (in_array($key, $meta_fields)) {
                    $meta[] = array(
                        'key' => $key,
                        'value' => $value,
                        'encrypted' => in_array($key, $encrypted_fields) ? 1 : 0
                    );
                }
            }

            return $meta;
        }
    }

    /**
     * Edits the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being updated. Returns a set of data, which may be
     * a subset of $vars, that is stored for this module row
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     * @param array $vars An array of module info to update
     * @return array A numerically indexed array of meta fields for the module row containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     */
    public function editModuleRow($module_row, array &$vars)
    {
        $meta_fields = array("server_name", "ip_address", "use_ssl", "password");
        $encrypted_fields = array("password");

        // Set unspecified checkboxes
        if (empty($vars['use_ssl']))
            $vars['use_ssl'] = "false";

        // Validate module row
        if ($this->Input->validates($vars)) {

            // Build the meta data for this row
            $meta = array();
            foreach ($vars as $key => $value) {

                if (in_array($key, $meta_fields)) {
                    $meta[] = array(
                        'key' => $key,
                        'value' => $value,
                        'encrypted' => in_array($key, $encrypted_fields) ? 1 : 0
                    );
                }
            }

            return $meta;
        }
    }

    /**
     * Deletes the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being deleted.
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     */
    public function deleteModuleRow($module_row)
    {

    }

    /**
     * Returns the value used to identify a particular service
     *
     * @param stdClass $service A stdClass object representing the service
     * @return string A value used to identify this service amongst other similar services
     */
    public function getServiceName($service)
    {
        foreach ($service->fields as $field) {
            if ($field->key == "username")
                return $field->value;
        }
        return null;
    }

    /**
     * Returns the value used to identify a particular package service which has
     * not yet been made into a service. This may be used to uniquely identify
     * an uncreated services of the same package (i.e. in an order form checkout)
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @return string The value used to identify this package service
     * @see Module::getServiceName()
     */
    public function getPackageServiceName($package, array $vars = null)
    {
        if (isset($vars['username']))
            return $vars['username'];
        return null;
    }

    /**
     * Returns all fields to display to an admin attempting to add a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
     */
    public function getAdminAddFields($package, $vars = null)
    {
        Loader::loadHelpers($this, array("Html"));

        $fields = new ModuleFields();

        if ($package->meta->client_type == "internal") {
            $username = $fields->label(Language::_("Whmsonic.service_field.cpanel_username", true), "username");
            $username->attach($fields->fieldText("username", $this->Html->ifSet($vars->username, $this->Html->ifSet($vars->username)), array('id' => "username")));
            $fields->setField($username);
        }

        return $fields;
    }

    /**
     * Returns all fields to display to a client attempting to add a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
     */
    public function getClientAddFields($package, $vars = null)
    {
        Loader::loadHelpers($this, array("Html"));

        $fields = new ModuleFields();

        if ($package->meta->client_type == "internal") {
            $username = $fields->label(Language::_("Whmsonic.service_field.cpanel_username", true), "username");
            $username->attach($fields->fieldText("username", $this->Html->ifSet($vars->username, $this->Html->ifSet($vars->username)), array('id' => "username")));
            $fields->setField($username);
        }

        return $fields;
    }

    /**
     * Returns all fields to display to an admin attempting to edit a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
     */
    public function getAdminEditFields($package, $vars = null)
    {
        Loader::loadHelpers($this, array("Html"));

        $fields = new ModuleFields();

        $username = $fields->label(Language::_("Whmsonic.service_field.username", true), "username");
        $username->attach($fields->fieldText("username", $this->Html->ifSet($vars->username, $this->Html->ifSet($vars->username)), array('id' => "username")));
        $username->attach($fields->tooltip(Language::_("Whmsonic.service_field.username.tooltip", true)));
        $fields->setField($username);

        $password = $fields->label(Language::_("Whmsonic.service_field.password", true), "password");
        $password->attach($fields->fieldPassword("password", array('id' => "password")));
        $password->attach($fields->tooltip(Language::_("Whmsonic.service_field.password.tooltip", true)));
        $fields->setField($password);

        return $fields;
    }

    /**
     * Attempts to validate service info. This is the top-level error checking method. Sets Input errors on failure.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @param boolean $edit True if this is an edit, false otherwise
     * @return boolean True if the service validates, false otherwise. Sets Input errors when false.
     */
    public function ServiceValidation($package, array $vars = null, $edit = false)
    {
        $rules = array(
            'username' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_("Whmsonic.!error.user_name.empty", true)
                )
            ),
        );


        // Set the values that may be empty
        if ($edit) {

            if (!array_key_exists('username', $vars) || $vars['username'] == "")
                unset($rules['username']);

        }
        $this->Input->setRules($rules);
        return $this->Input->validates($vars);
    }

    /**
     * Adds the service to the remote server. Sets Input errors on failure,
     * preventing the service from being added.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being added (if the current service is an addon service service and parent service has already been provisioned)
     * @param string $status The status of the service being added. These include:
     *    - active
     *    - canceled
     *    - pending
     *    - suspended
     * @return array A numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function addService($package, array $vars = null, $parent_package = null, $parent_service = null, $status = "pending")
    {
        $row = $this->getModuleRow();
        $params = array();
        if (!$row) {
            $this->Input->setErrors(array('module_row' => array('missing' => Language::_("Whmsonic.!error.module_row.missing", true))));
            return;
        }

        Loader::loadModels($this, array("Clients"));

        if (isset($vars['client_id']) && ($client = $this->Clients->get($vars['client_id'], false))) {
            $params["client_email"] = $client->email;
            $params["client_name"] = $client->first_name . " " . $client->last_name;
        }

        if ($package->meta->client_type == "internal") {
            $params["username"] = isset($vars["username"]) && !empty($vars["username"]) ? $vars["username"] : null;
        } else {
            $params["username"] = $this->generateUsername($client->first_name . $client->last_name);
            $params["pass"] = $this->generatePassword(10, 14);
        }

        $params["bitrate"] = $package->meta->bitrate;
        $params["hspace"] = $package->meta->hspace;
        $params["autodj"] = $package->meta->autodj;
        $params["bandwidth"] = $package->meta->bandwidth;
        $params["listeners"] = $package->meta->listeners;


        $this->ServiceValidation($package, $params);


        if ($this->Input->errors())
            return;

        // Only provision the service if 'use_module' is true
        if ($vars['use_module'] == "true") {

            $api = $this->getApi($row->meta->password, $row->meta->ip_address, $row->meta->use_ssl);

            $response = $api->createRadio($params, $package->meta->client_type);
            $this->log($row->meta->ip_address . "|create", serialize($response), "input", $response["status"]);

            // If fails then set an error
            if ($response["status"] == false) {
                $this->Input->setErrors(array('api_response' => array('missing' => Language::_("Whmsonic.!error.api.internal", true))));
            }


            if ($this->Input->errors())
                return;
        }

        // Return service fields
        return array(
            array(
                'key' => "username",
                'value' => $params["username"],
                'encrypted' => 0
            ),
            array(
                'key' => "password",
                'value' => $params["pass"],
                'encrypted' => 1
            ),
        );
    }

    /**
     * Edits the service on the remote server. Sets Input errors on failure,
     * preventing the service from being edited.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $vars An array of user supplied info to satisfy the request
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being edited (if the current service is an addon service)
     * @return array A numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function editService($package, $service, array $vars = null, $parent_package = null, $parent_service = null)
    {
        $this->ServiceValidation($package, $vars, true);

        if ($this->Input->errors())
            return;

        $service_fields = $this->serviceFieldsToObject($service->fields);

        if (empty($vars["username"]) || $service_fields->username == $vars["username"]) {
            $vars["username"] = $service_fields->username;
        }
        if (empty($vars["password"]) || $service_fields->user_password == $vars["password"]) {
            $vars["password"] = $service_fields->password;
        }

        // Return service fields
        return array(
            array(
                'key' => "username",
                'value' => $vars["username"],
                'encrypted' => 0
            ),
            array(
                'key' => "password",
                'value' => $vars["password"],
                'encrypted' => 1
            ),
        );
    }

    /**
     * Suspends the service on the remote server. Sets Input errors on failure,
     * preventing the service from being suspended.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being suspended (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function suspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        if (($row = $this->getModuleRow())) {

            $service_fields = $this->serviceFieldsToObject($service->fields);

            $api = $this->getApi($row->meta->password, $row->meta->ip_address, $row->meta->use_ssl);

            $response = $api->suspendRadio($service_fields->username);
            $this->log($row->meta->ip_address . "|suspend", serialize($response), "input", $response["status"]);

            // if fails then set an error
            if ($response["status"] == false) {
                $this->Input->setErrors(array('api_response' => array('missing' => Language::_("Whmsonic.!error.api.internal", true))));
                return;
            }


        }
        return null;
    }

    /**
     * Unsuspends the service on the remote server. Sets Input errors on failure,
     * preventing the service from being unsuspended.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being unsuspended (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function unsuspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        if (($row = $this->getModuleRow())) {

            $service_fields = $this->serviceFieldsToObject($service->fields);

            $api = $this->getApi($row->meta->password, $row->meta->ip_address, $row->meta->use_ssl);

            $response = $api->unSuspendRadio($service_fields->username);
            $this->log($row->meta->ip_address . "|unsuspend", serialize($response), "input", $response["status"]);

            // if fails then set an error
            if ($response["status"] == false) {
                $this->Input->setErrors(array('api_response' => array('missing' => Language::_("Whmsonic.!error.api.internal", true))));
                return;
            }

        }
        return null;
    }

    /**
     * Cancels the service on the remote server. Sets Input errors on failure,
     * preventing the service from being canceled.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being canceled (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function cancelService($package, $service, $parent_package = null, $parent_service = null)
    {

        if (($row = $this->getModuleRow())) {

            $service_fields = $this->serviceFieldsToObject($service->fields);

            $api = $this->getApi($row->meta->password, $row->meta->ip_address, $row->meta->use_ssl);

            $response = $api->terminateRadio($service_fields->username);
            $this->log($row->meta->ip_address . "|terminate", serialize($response), "input", $response["status"]);

            // if fails then set an error
            if ($response["status"] == false) {
                $this->Input->setErrors(array('api_response' => array('missing' => Language::_("Whmsonic.!error.api.internal", true))));
                return;
            }

        }
        return null;
    }

    /**
     * Updates the package for the service on the remote server. Sets Input
     * errors on failure, preventing the service's package from being changed.
     *
     * @param stdClass $package_from A stdClass object representing the current package
     * @param stdClass $package_to A stdClass object representing the new package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being changed (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function changeServicePackage($package_from, $package_to, $service, $parent_package = null, $parent_service = null)
    {
        return null;
    }

    /**
     * Fetches the HTML content to display when viewing the service info in the
     * admin interface.
     *
     * @param stdClass $service A stdClass object representing the service
     * @param stdClass $package A stdClass object representing the service's package
     * @return string HTML content containing information to display when viewing the service info
     */
    public function getAdminServiceInfo($service, $package)
    {
        $row = $this->getModuleRow();

        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View("admin_service_info", "default");
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView("components" . DS . "modules" . DS . "whmsonic" . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, array("Form", "Html"));

        $this->view->set("module_row", $row);
        $this->view->set("package", $package);
        $this->view->set("service", $service);
        $this->view->set("service_fields", $this->serviceFieldsToObject($service->fields));

        return $this->view->fetch();
    }

    /**
     * Fetches the HTML content to display when viewing the service info in the
     * client interface.
     *
     * @param stdClass $service A stdClass object representing the service
     * @param stdClass $package A stdClass object representing the service's package
     * @return string HTML content containing information to display when viewing the service info
     */
    public function getClientServiceInfo($service, $package)
    {
        $row = $this->getModuleRow();

        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View("client_service_info", "default");
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView("components" . DS . "modules" . DS . "whmsonic" . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, array("Form", "Html"));

        $this->view->set("module_row", $row);
        $this->view->set("package", $package);
        $this->view->set("service", $service);
        $this->view->set("service_fields", $this->serviceFieldsToObject($service->fields));

        return $this->view->fetch();
    }

    /**
     * Validates that the given hostname is valid
     *
     * @param string $host_name The host name to validate
     * @return boolean True if the hostname is valid, false otherwise
     */
    public function validateHostName($host_name)
    {
        if (strlen($host_name) > 255)
            return false;

        return $this->Input->matches($host_name, "/^([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9])(\.([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9]))+$/");
    }

    /**
     * Generates a password
     *
     * @param int $min_length The minimum character length for the password (5 or larger)
     * @param int $max_length The maximum character length for the password (14 or fewer)
     * @return string The generated password
     */
    private function generatePassword($min_length = 10, $max_length = 14)
    {
        $pool = "abcdefghijklmnopqrstuvwxyz0123456789";
        $pool_size = strlen($pool);
        $length = mt_rand(max($min_length, 5), min($max_length, 14));
        $password = "";

        for ($i = 0; $i < $length; $i++) {
            $password .= substr($pool, mt_rand(0, $pool_size - 1), 1);
        }

        return $password;
    }

    /**
     * Generates random username
     *
     * @return string The username generated from the given hostname
     */
    private function generateUsername($name)
    {
        $pool = "abcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()";
        srand((double)microtime() * 1000000);
        $username = substr(str_replace(' ', '', strtolower($name)), 0, 2) . $pool[(rand(0, 35))] . $pool[(rand(0, 35))];
        return "sc_" . $username;
    }

    /**
     * Initialize the API library
     *
     * @param string $password The whmsonic password
     * @param string $ip_address The ip address of the server
     * @param boolean $use_ssl Whether to use https or http
     * @return Whmsonicapi the Whmsonicapi instance, or false if the loader fails to load the file
     */
    private function getApi($password, $ip_address, $use_ssl)
    {
        Loader::load(dirname(__FILE__) . DS . "api" . DS . "whmcsonic_api.php");
        return new WhmsonicApi($password, $ip_address, $use_ssl);
    }


    /**
     * Builds and returns the rules required to add/edit a module row (e.g. server)
     *
     * @param array $vars An array of key/value data pairs
     * @return array An array of Input rules suitable for Input::setRules()
     */
    private function getRowRules(&$vars)
    {
        $rules = array(
            'server_name' => array(
                'valid' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_("Whmsonic.!error.server_name_valid", true)
                )
            ),
            'ip_address' => array(
                'valid' => array(
                    'rule' => array(array($this, "validateHostName")),
                    'message' => Language::_("Whmsonic.!error.host_name_valid", true)
                )
            ),
            'password' => array(
                'valid' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_("Whmsonic.!error.password_valid", true)
                )
            ),
        );

        return $rules;
    }

    /**
     * Builds and returns rules required to be validated when adding/editing a package
     *
     * @param array $vars An array of key/value data pairs
     * @return array An array of Input rules suitable for Input::setRules()
     */
    private function getPackageRules($vars)
    {
        $rules = array(
            'meta[client_type]' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_("Whmsonic.!error.meta[client_type].empty", true)
                )
            ),
            'meta[bitrate]' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_("Whmsonic.!error.meta[bitrate].empty", true)
                )
            ),
            'meta[hspace]' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_("Whmsonic.!error.meta[hspace].empty", true)
                )
            ),
            'meta[bandwidth]' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_("Whmsonic.!error.meta[bandwidth].empty", true)
                )
            ),
            'meta[listeners]' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_("Whmsonic.!error.meta[listeners].empty", true)
                )
            ),
        );

        return $rules;
    }
}

?>