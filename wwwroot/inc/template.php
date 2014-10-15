<?php
//Use this to test wether a template was called within the main module or without it.
//define("TPL_DEBUG",true);
define("RS_TPL",true);

/**
 * Class used by the other TemplateClass to throw exceptions.
 * @author Alexander Kastius
 */
class TemplateException extends Exception {};

/**
 * Class that manages the main module and the global template.
 * 
 * Use this to create the main module and manage all submodules.
 * 
 * @author Alexander Kastius
 */
class TemplateManager
{
	
	//Singleton Implementation
	private static $instance = null;
	
	private static $default_template = 0;
	/**
	 * Returns the TemplateManager instance
	 * 
	 * @return TemplateManager
	 */
	public static function getInstance()
	{
		if (self::$instance == null)
		{
			self::$instance = new TemplateManager();
		}
		return self::$instance;
	}
	//End Singleton Implementation
	
	/**
	 * Default function to initialize template logic.
	 * Uses $_SESSION to store the currently used template.
	 */
	public static function initalizeTemplate($mainmod = 'index')
	{
		$tpl_to_use = '';

		$template_list = self::getOrderedTemplateList();
		//TODO Messy code fixit
		$flipped = array_flip($template_list);


		if (!array_key_exists('RacktablesTemplate', $_COOKIE))
		{
			setcookie('RacktablesTemplate',$flipped['vanilla']);
			$tpl_to_use = $flipped['vanilla'];
			//$_COOKIE['RacktablesTemplate'] = getConfigVar('default_template');
		}
		else
		{
			$tpl_to_use = $_COOKIE['RacktablesTemplate'];
		}
		
		if (!array_key_exists($_COOKIE['RacktablesTemplate'], $template_list))
		{
			if (!array_search('vanilla', $template_list))
			{
				// Do not throw exception anymore but use default value
				setcookie('RacktablesTemplate',$flipped[0]);
				$tpl_to_use = $flipped[0];
			}
			else 
			{
				setcookie('RacktablesTemplate',$flipped['vanilla']);
				$tpl_to_use = $flipped['vanilla'];
				//$_COOKIE['RacktablesTemplate'] = $flipped['vanilla']; //@TODO might be nessessary for instant use.
			}
		}
		
		$inst = self::getInstance();
		
		//@XXX escape string!!
		$inst->setTemplate($template_list[$tpl_to_use]);
		$inst->createMainModule($mainmod);
	}
	
	/**
	 * Sets the current template directory as new static dir.
	 */
	public static function changeStaticDir()
	{
		$inst = self::getInstance();
		
		global $local_staticdir ;
		
		$local_staticdir = $inst->getTemplate();
	}

	/**
	 * Returns a list of all available templates.
	 * @return multitype:string
	 */
	public static function getOrderedTemplateList()
	{
		static $arr = array('vanilla', '');

//		$arr = glob('../tpl/*' , GLOB_ONLYDIR); //@TODO Make it work

		sort($arr);

		return $arr;
	}
	
	/**
	 * Contains the main template.
	 * 
	 * @var string
	 */
	protected $tpl = "";
	
	/**
	 * Contains the main module if created.
	 * 
	 * @var object
	 */
	protected $mainmod = null;
	
	/**
	 * Contains the placeholders available in every module.
	 * 
	 * @var array
	 */
	protected $gout = array();
	
	/**
	 * A list of inmemory templates for short but oftenly used modules
	 * 
	 * @var array
	 */
	protected $inmemory_templates = array();
	
	/**
	 * Contains all loaded helpers.
	 * 
	 * @var 
	 */
	protected $helpers = array();
	
	/**
	 * Contains all added requirements to check wether they already got added.
	 * 
	 * @var array
	 */
	protected $requirements = array();
	
	/**
	 * Checks wether an given inmemory template is already available.
	 * 
	 * @param string $name
	 * @return boolean
	 */
	public function inMemoryExists($name)
	{
		return array_key_exists($name,$this->inmemory_templates);
	}
	
	/**
	 * Tries to load an unknown in-memory template.
	 * $name might consist of a path without file ending: "thisisanexample/template"
	 * Checks:
	 * thisisanexample/template.itpl.php
	 * globals.itpl.php
	 * thisisanexample/globals.itpl.php
	 * 
	 * @param unknown $name
	 * @param string $template
	 * @throws TemplateException
	 * @return boolean
	 */
	public function loadInMemoryTemplate($name,$template="")
	{
		if ($this->tpl == "" && $template== "")
		{
			throw new TemplateException("TplErr: The template has to be set before loading a in-memory Template.");
		}	
		if ($template == "")
		{
			$template = $this->tpl;
		}
		if (key_exists($name, $this->inmemory_templates))
		{
			return true;
		}
		if (file_exists(dirname(__FILE__).'/../tpl/'. $template . '/' . $name . '.itpl.php'))
		{
			require_once(dirname(__FILE__).'/../tpl/'. $template . '/' . $name . '.itpl.php'); //Check for the template in its own file
		}
		if (strpos($name,'/') !== false)
		{
			$arr = explode('/',$name,-1);
			$dir = implode('/',$arr);
			if (file_exists('./tpl/'. $template . '/' . $dir . '/global.itpl.php'))
			{
				require_once('./tpl/'. $template . '/' . $dir . '/global.itpl.php'); //Check for the template in the global file
			}				
		}
		if (file_exists(dirname(__FILE__).'/../tpl/'. $template . '/global.itpl.php'))
		{	
			require_once(dirname(__FILE__).'/../tpl/'. $template . '/global.itpl.php'); //Check for the template in the global file
		}
		
		if (!key_exists($name, $this->inmemory_templates))
		{
			return false; //Still not existing? Return false.
		}
		else
		{
			return true;
		}
	}
	
	/**
	 * Sets an in-memory template.
	 * Use {{placeholder}} for placeholders.
	 * 
	 * @param string $name
	 * @param string $code
	 */
	public function setInMemoryTemplate($name,$code)
	{
		$this->inmemory_templates[$name] = $code;
	}
	
	/**
	 * Returns an in-memory template.
	 * 
	 * @param string $name
	 * @return string
	 */
	public function getInMemoryTemplate($name)
	{
		if (key_exists($name, $this->inmemory_templates))
		{
			return $this->inmemory_templates[$name];
		}
		return "";
	}
	
	/**
	 * Set the main template
	 * 
	 * @param string $name
	 */
	public function setTemplate($name)
	{
		self::log("Setting template: " . $name);
		$this->tpl = $name;
	}
	
	/**
	 * Returns the main template
	 * 
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->tpl;
	}
	
	/**
	 * Set the global output variables
	 * 
	 * @param array $out
	 */
	public function setGlobalOutput($out) {
		$this->gout = $out;
	}
	
	/**
	 * Set a single global output variable
	 * 
	 * @param string $name
	 * @param string $value
	 */
	public function setGlobalOutputVariable($name,$value) {
		self::log("Setting global out: " . $name . "=>", $value);
		$this->gout[$name] = $value;
	}
	
	/**
	 * Automatically create the main module, using the template set in the manager.
	 * Returns null if no template is set.
	 * 
	 * @param string $name
	 * @return NULL|TemplateModule
	 */
	public function createMainModule($name = "index")
	{
		/**if ($this->tpl == "")
		 **{
		 ** 	return null;
		 **}
			*/
		if ($this->mainmod == null) {
			$this->mainmod = new TemplateModule($this->tpl, $name);
		}
		return $this->mainmod;
	}
	
	/**
	 * Returns the main module.
	 * 
	 * @return TemplateModule
	 */
	public function getMainModule()
	{
		return $this->mainmod;
	}
	
	/**
	 * Run all template-modules.
	 * If you set echo on true, the result will be sent instead of returning it.
	 * 
	 * @param boolean $echo
	 * @throws TemplateException
	 * @return string
	 */
	public function run($echo=false,$tpl="")
	{
		if ($this->tpl == "" && $tpl != "") {
			$this->tpl = $tpl ;
		}
		if ($this->mainmod == null||$this->tpl == "") {
			throw new TemplateException("TplErr: Mainmodule and template need be created/set before running the TplManager.");
			return "";
		}
		
		global $pageno, $tabno;
		$this->setGlobalOutputVariable("PageNo", $pageno);
		$this->setGlobalOutputVariable("TabNo", $tabno);
		
		$this->mainmod->mergeOutputArray($this->gout,true);
		$this->mainmod->setTemplate($this->tpl);
		$this->mainmod->addOutput('Debug',$this->mainmod->generateModTree());

		self::log("Running with template: " . $this->tpl . " Mainmod: " , $this->mainmod);
		$cont = $this->mainmod->run();
		if ($echo)
		{
			echo $cont;
		}
		return $cont;
	}
	
	/**
	 * Use this to automatically set the global output variables for a new TemplateModule and the template name itself,
	 * it will get added to its parent automatically. If you leave parent empty, it will get added to the main module.
	 * It might be easier to use this one instead of loading the templates on your own.
	 * 
	 * @param string $placeholder
	 * @param string $name
	 * @param TemplateModule $parent
	 * @param array $output
	 * @param number $pos
	 * @return Ambigous <TemplateModule, TemplateInMemory>
	 */
	public function generateSubmodule($placeholder,$name,TemplateModule $parent=null,$inmemory=false,$output=array())
	{
		self::log("Generating submodule: " . $name . " (Placeholder: " . $placeholder . " InMemory: " . $inmemory . "Parent and Output: " , array($parent,$output));
		if (!$inmemory)
		{
			$module = new TemplateModule($this->tpl, $name, $output);
		}
		else 
		{
			$module = New TemplateInMemory($this->tpl, $name, $output);
		}
		if ($parent == null)
		{
			if ($this->mainmod == null) {
				throw new TemplateException("TplErr: Can't add an submodule to an non-object. (Main not initialized?)");
			}
			$this->mainmod->addOutput($placeholder,$module);
		}
		else 
		{
			$parent->addOutput($placeholder,$module);
		}
		return $module;
	}
	
	/**
	 * Generates a 'pseudo'-submodule.
	 * It creates a TemplatePseudo instead of a TemplateInMemory/TemplateModule
	 * The difference between a TemplateModule and TemplatePseudo is that
	 * TemplatePseudo doesn't actually use a template file.
	 * Instead it stores the content of its local placeholder array in
	 * its parent module.
	 * 
	 * This allows it to use TemplatePseudo to create loop-arrays without using actual
	 * arrays in your code.
	 * To do so, you just create multiple pseudo-submodules on one placeholder, you can
	 * access the content of thoose modules in a loop.
	 * 
	 * @param String $placeholder
	 * @param TemplateModule $parent
	 * @param Array $output
	 * @throws TemplateException
	 * @return TemplatePseudo
	 */
	public function generatePseudoSubmodule($placeholder, TemplateModule $parent = null, $output = array()) {
		$module = new TemplatePseudo('', '', $output);
		$ret = $module;
		if ($parent == null)
		{
			if ($this->mainmod == null) {
				throw new TemplateException("TplErr: Can't add an submodule to an non-object. (Main not initialized?)");
			}
			if (!$mainmod->is($placeholder)) {
				$module = array($module);
			}
			$this->mainmod->addOutput($placeholder,$module);
		}
		else
		{
			if (!$parent->is($placeholder)) {
				$module = array($module);
			}
			$parent->addOutput($placeholder,$module);
		}
		return $ret;
	}
	
	/**
	 * Works the same way as generateSubmodule, but without adding it somewhere.
	 * 
	 * @param string $placeholder
	 * @param string $name
	 * @param boolean $inmemory
	 * @param array $output
	 * @return Ambigous <TemplateModule, TemplateInMemory>
	 */
	public function generateModule($name,$inmemory=false,$output=array())
	{
		self::log("Generating module: ".$name . " (InMemory: " . $inmemory . " Output: " , $output);
		if (!$inmemory)
		{
			$module = new TemplateModule($this->tpl, $name, $output);
		}
		else
		{
			$module = New TemplateInMemory($this->tpl, $name, $output);
		}
		return $module;		
	}
		
	/**
	 * Adds a sub-module to the main module,
	 * this can be used to include certain css 
	 * code or other special code somewhere else
	 * then in the calling module itself.
	 * 
	 * @param string $placeholder
	 * @param string $name
	 * @param string $inmemory
	 * @param Multitype $cont
	 * @param string $namespace
	 */
	public function addRequirement($placeholder,$name,$namespace="",$inmemory=false,$cont=array() )
	{
		$test = $placeholder . $name . $namespace . $inmemory . implode('', $cont) . implode('',array_keys($cont)) ;
		foreach ($this->requirements as $req)
		{
			if ($req == $test)
			{
				self::log("Adding requirement failed: " . $name . " -> " . $placeholder);
				return false;
			}
		}
		$this->requirements[] = $test;
		self::log("Adding requirement: ".$placeholder . " on " . $name . " Namespace: " . $namespace . " InMemory: " . $inmemory . " Cont: ", $cont);
		//$mod = $this->generateSubmodule($placeholder, $name, $this->getMainModule(), $inmemory, $cont);
		$mod = $this->generateModule($name, $inmemory, $cont);
		$mod->setNamespace($namespace);
		$mod->setLock(true);
		
		$this->getMainModule()->addLocalRequirement($placeholder, $mod->run());
		return true;
	}
	
	/**
	 * Adds an global template, called Helper, that will be available in every Module
	 * You can add helpers on your own, they have to implement the function run() (Interface is TemplateInterface)
	 * and they have to echo the content, based on the passed params.
	 * In templates they can be accessed via: $this->getH("Name",$param or array of params)
	 * 
	 * @param string $helper
	 * @param TemplateHelper $object
	 */
	public function addHelper($helper,TemplateHelper $object = null)
	{
		self::log("Adding helper: ".$helper , $object);
		$classname = "TemplateHelper" . $helper;
		if ($object != null)
		{
			$this->helpers[$helper] = $object;
			return true;
		}
		else
		{
			if ($this->tpl != "")
			{
				if (file_exists(dirname(__FILE__).'/../tpl/' . $this->tpl . '/helpers.php'))
					require_once dirname(__FILE__).'/../tpl/' . $this->tpl . "/helpers.php";
			}
			if (!class_exists($classname))
			{
				return false;
			}
			$object = new $classname() ;
			$this->helpers[$helper] = $object;
			return true;
		}
	}
	
	/**
	 * Returns an helper, tries to load it if it is still unknown.
	 * 
	 * @param string $helper
	 * @return NULL|TemplateHelper:
	 */
	public function getHelper($helper)
	{
		self::log("Loading helper:" . $helper);
		if(!array_key_exists($helper, $this->helpers))
		{
			if (!$this->addHelper($helper))
			{
				return null;
			}	
		}
		return $this->helpers[$helper];
	}
	
	/**
	 * Used for debug logging,
	 * as long as TPL_DEBUG is set in this file,
	 * it will fill a logfile with informations.
	 * 
	 * @param String $text
	 * @param mixed $var
	 */
	public static function log($text,$var=null)
	{
		if (defined('TPL_DEBUG'))
		{
			if ($var != null)
			{
				ob_start();
				var_dump($var);
				$text .= ob_get_clean();
			}
			$text .= "\r\n";
			$handle = fopen('./tpl.log','a');
			fwrite($handle,$text);
			fclose($handle);
		}
	}
}

/**
 * An interface you can use if you want to create your own helpers in the helpers.php of your template
 * Params should be an array that takes either values like "Testoutput" or "%%placeholder", the second one will search for an
 * output variable with the name "placeholder" in the module where the helper is used.
 * It might be better to use the abstract class defined below, as this one already implements a small algorithm to parse the output variables.
 * 
 * @author Alexander Kastius
 *
 */
interface TemplateHelper
{
	public function run(TemplateModule $parent,$params=array());
}

/**
 * Use this to easily create your own template helpers,
 * just extend the class and implement the generate($params) function.
 * This function should echo the content using the passed parameters, thoose will be already parsed (placeholder will be replaced with values, or with "" when the placeholder didn't exist).
 * You should make shure that your helper will output nothing, not even the surrounding tags, when launched with empty parameters.
 * 
 * @author Alexander Kastius
 *
 */
abstract class TemplateHelperAbstract
{
	abstract protected function generate($params);
	
	/**
	 * Default public run function, takes the parent, and an array of params. Params can also be a single param,
	 * it will create an array($param).
	 * 
	 * @param TemplateModule $parent
	 * @param mixed $params
	 */
	public function run(TemplateModule $parent,$params=array())
	{
		if (!is_array($params))
		{
			$params = array($params);
		}
		$ret = array();
		foreach ($params as $i => $val) {

			if(is_string($val) && substr($val,0,2)=="%%")
			{
				$ret[$i] = $parent->get(substr($val,2),true);
			}
			else
			{
				$ret[$i] = $val;
			}
		}
		
		echo $this->generate($ret);
	}
}

/**
 * A single template module.
 * It's better to use the generate template function instead of creating thoose on your own.
 * 
 * @author Alexander Kastius
 *
 */
class TemplateModule
{
	/**
	 * Contains the template used in this module.
	 * @var string
	 */
	protected $tpl = "";
	
	/**
	 * Contains the module name.
	 * @var string
	 */
	protected $module = "";
	
	/**
	 * Contains all submodules of this module.
	 * @var string
	 */
	protected $localRequirements = array();
	
	/**
	 * Defines a path where the module will search for it's template (for example: Namespace="rackspace" and name="RackspaceOverview"
	 * will result in "rackspace/RackspaceOverview.tpl.php as path for template"
	 * Warning!: This will also result in rackspace/global.itpl.php in case you need a globals file.
	 * 
	 * @var string
	 */
	protected $namespace = "";
	
	/**
	 * Wether the namespace is global (also used in submodules)
	 * 
	 * @var boolean
	 */
	protected $namespace_global = false;
	
	/**
	 * For internal use, to check wether we currently parse a loop or not.
	 * @var integer
	 * 
	 */
	protected $loop = 0;	
	
	/**
	 * For internal use, stores the placeholder that contains the array that contains the values for the loop
	 * @var string
	 */
	protected $loopplaceholder = "";
	
	/**
	 * For internal use, stores the counting-variable
	 * @var integer
	 */
	protected $loopi = 0;
	
	/**
	 * Contains the whole output array with all submodules and strings.
	 * @var array
	 */
	protected $output = array();
	
	/**
	 * Wether the module can still change its namespace. Can be usefull when using submodules that shouldnt
	 * use their parents namespace.
	 * @var boolean
	 */
	protected $locked = false;
	
	/**
	 * Used to store a reference to the current output, instead of using the global output array
	 * @var unknown
	 */
	protected $output_reference = array();

	/**
	 * The current level inside of nested loops.
	 * @var unknown
	 */
	protected $reference_level = -1;
	
	/**
	 * Used to store the next key to use with loop() or refLoop()
	 * @var unknown
	 */
	protected $reference_next = array();
	
	/**
	 * Stores the origin of the current loop content (used within loop() and refLoop())
	 * @var unknown
	 */
	protected $reference_origin = array();
	
	/**
	 * Constructor
	 * 
	 * Set the template, the module-placeholder for the parent (doesn'T matter if you create the main module),
	 * the module name itself and the module output (can be changed later on).
	 * 
	 * @param string $tpl
	 * @param string $placeholder
	 * @param string $module
	 * @param array $mout
	 */
	public function __construct($tpl,$module,$mout = array())
	{
		$this->tpl = $tpl;
		$this->module = $module;
		if (!is_array($mout))
		{
			throw new TemplateException("TplErr: Mout has to be an array! (Most likely you confused generateModule and generateSubmodule");
		}
		$this->output = $mout;
	}
	
	/**
	 * Returns the current template
	 * @return string
	 */
	function getTemplate()
	{
		return $this->tpl;
	}
	
	/**
	 * Sets the template, if you set global on true, all submodules will also reset their template.
	 * @param string $tpl
	 * @param boolean $global
	 */
	function setTemplate($tpl,$global=false)
	{
		$this->tpl = $tpl;
		if ($global)
		{
			foreach ($this->getAllModules($this->output) as $mod) {
				$mod->setTemplate($tpl,true);
			}
		}
	}
	
	/**
	 * Merge the existing output array with the one passed to this function.
	 * 
	 * If you set subs on true, this will also be done with all submodules.
	 * @param array $output
	 * @param boolean $subs
	 */
	public function mergeOutputArray($output,$subs=false)
	{
		$this->output = array_merge($this->output,$output);
		if ($subs)
		{
			foreach ($this->getAllModules($this->output) as $mod)
			{
				$mod->mergeOutputArray($output,$subs);
			}
		}
	}
	
	/**
	 * Returns all objects in the passed array.
	 * 
	 * @param array $arr
	 * @return array
	 */
	protected function getAllModules($arr)
	{
		$ret = array();
		foreach ($arr as $val)
		{
			if (is_array($val))
			{
				$ret = array_merge($ret,$this->getAllModules($val));
			}
			elseif (is_object($val))
			{
				$ret[] = $val;
			}
		}
		return $ret;
	}
	
	/**
	 * Set a single output variable. It overwrites the old content!
	 * @param string $name
	 * @param string $value
	 */
	public function setOutput($name,$value)
	{
		$this->output[$name] = $value;
	}
	
	/**
	 * Returns one output variable or the submodule or the array of both under this placeholder if it doesn't exist.
	 * @param string $name
	 * @return string
	 */
	public function getOutput($name)
	{
		if (key_exists($name, $this->output))
		{
			return $this->output[$name];
		}
		return "";
	}
	
	/**
	 * Runs the whole template and returns the generated content.
	 * 
	 * @throws TemplateException
	 * @return string
	 */
	public function run()
	{
		if ($this->module == ""||$this->tpl == "") {
			throw new TemplateException("TplErr: Module and template need to be set before running a template-module.");
			return "";
		}
		
		$this->output = array_merge_recursive($this->runModules($this->output),$this->localRequirements);
		
		$this->output["css"] = dirname(__FILE__) . '/../tpl/' . $this->tpl . '/css/';
		$this->output["img"] = dirname(__FILE__) . '/../tpl/' . $this->tpl . '/img/';
		$this->output["js"] = dirname(__FILE__) . '/../tpl/' . $this->tpl . '/js/';
		
		if ($this->namespace !=  "")
		{
			$this->module = $this->namespace . '/' . $this->module;
		}
		if (!file_exists(dirname(__FILE__) . '/../tpl/' . $this->tpl . '/' . $this->module . '.tpl.php'))
		{
			throw new TemplateException("TplError: Module " . $this->module . " doesn't exist in " . $this->tpl);
			return "";
		}
		
		ob_start();	
		include dirname(__FILE__) . '/../tpl/' . $this->tpl . '/' . $this->module . '.tpl.php';

		// Remove comments from js code
		$output_cont = preg_replace("/^(.*)\/\/(.*\n)/","$1", ob_get_clean());
		// Replace whitespace in the output 
		$output_cont = str_replace(array("\t\t", '    ', '  ', '   '), array("\t", ' ', ' ', ' '),$output_cont);
		return str_replace(array("\r\n", "\r", "\n\t", "\n\t\t\t", "\n\t\t"), '', $output_cont);
	}
	
	/**
	 * Wrapper for $this->get, so you can use $this->placeholder in templates.
	 * @param string $name
	 */
	public function __get($name)
	{
		if (substr($name,0,1) == '_') {
			return $this->get(substr($name,1),true);
		}
		else
		{
			$this->get($name);
			return '';
		}
	}
	
	/**
	 * For use in the templates, to echo one of the variables.
	 * 
	 * @param string $name
	 */
	public function get($name,$return = false, $enfore_noloop = false)
	{
		if ($this->loop == 1 && !$enfore_noloop)
		{
			echo "{{" . $name . "}}";
			return "";
		}
		else
		{
			if ($this->reference_level >= 0 && is_array($this->output_reference) && array_key_exists($this->reference_level, $this->output_reference) && array_key_exists($name, $this->output_reference[$this->reference_level]))
			{
				$out = "";
				if (is_array($this->output_reference[$this->reference_level][$name]) && !$return)
				{
					$out = implode($this->output_reference[$this->reference_level][$name]);
				}
				else
				{
					$out = $this->output_reference[$this->reference_level][$name];
				}
				if ($return)
				{
					return $out;
				}
				echo $out;
				return $out;
			}
			else
			{
				$out = "";
				if(array_key_exists($name, $this->output))
				{
					if (is_array($this->output[$name]) && !$return)
					{
						$out = implode($this->output[$name]);
					}
					else
					{
						$out = $this->output[$name];
					}
					if ($return)
					{
						return $out;
					}
					echo $out;
					return $out;
				}
			}
		}
		return null;
	}
	
	/**
	 * Defines a path where the module will search for it's template (for example: Namespace="rackspace" and name="RackspaceOverview"
	 * will result in "rackspace/RackspaceOverview.tpl.php as path for template"
	 * Warning!: This will also result in rackspace/global.itpl.php in case you need a globals file, but the tool will also check for global.itpl.php in the main path
	 * 
	 * @param string $name
	 */
	public function setNamespace($name,$global=false)
	{
		if ($this->locked)
		{
			return false;
		}
		$this->namespace = $name;
		$this->namespace_global = $global;
		return true;
	}
	
	/**
	 * Set the module to locked, to prevent it's parent from overriding the namespace.
	 * @param boolean $lock
	 */
	public function setLock($lock = true)
	{
		$this->locked = $lock;
	}
	
	/**
	 * Returns true when the module is locked.
	 * @return boolean
	 */
	public function isLocked()
	{
		return $this->locked;
	}
	
	/**
	 * Returns the current namespace
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}
	
	/**
	 * Adds an global module to the main module, supposed to be loaded there somewhere else,
	 * this can be used to include certain css code or other special stuff somewhere else then in the module itself.
	 * @param unknown $placeholder
	 * @param unknown $tplname
	 */
	public function addRequirement($placeholder,$name,$cont=array(),$namespace="",$inmemory=true)
	{
			$tplm = TemplateManager::getInstance();
			return $tplm->addRequirement($placeholder,$name,$namespace,$inmemory,$cont);
	}
	
	/**
	 * Wrapper for addRequirement.
	 * This functions adds either inline js code ($inline = true) or js includes.
	 * @param String $code
	 * @param Boolean $inline
	 * @return boolean
	 */
	public function addJS($code, $inline = false) {
		if ($inline) {
			return $this->addRequirement('Header', 'HeaderJsInline', array('code'=>$code));
		} else {
			return $this->addRequirement('Header', 'HeaderJsInclude', array('path'=>$code));
		}
	}
	
	/**
	 * Wrapper for addRequirement.
	 * This function adds either inline css code ($inline = true) or css includes.
	 * @param String $code
	 * @param Boolean $inline
	 * @return boolean
	 */
	public function addCSS($code, $inline = false) {
		if ($inline) {
			return $this->addRequirement('Header', 'HeaderCssInline', array('code'=>$code),'',true);
		} else {
			return $this->addRequirement('Header', 'HeaderCssInclude', array('path'=>$code),'',true);
		}
	}
	
	/**
	 * Works similar to addContent, but instead, it adds the given value
	 * to a local requirement array that gets merged with the real output array
	 * later on. This shouldn't be used directly, use addRequirement instead.
	 * 
	 * Use only strings as value, as the content of this array doesn't get parsed later on.
	 * 
	 * @param String $placeholder
	 * @param String $value
	 */
	public function addLocalRequirement($placeholder, $value)
	{
		if (array_key_exists($placeholder, $this->localRequirements))
		{
			if(is_array($this->localRequirements[$placeholder])&&!$this->isAssoc($this->localRequirements[$placeholder]))
			{
				$this->localRequirements[$placeholder][] = $value ;
			}
			else
			{
				$this->localRequirements[$placeholder] = array($this->localRequirements[$placeholder],$value);
			}
		}
		else
		{
			$this->localRequirements[$placeholder] = $value;
		}
	}
	
	/**
	 * Print helper output with the given params.
	 * Throws a TemplateException if the helper doesn't exist.
	 * 
	 * @param string $name
	 * @param array $params
	 */
	public function getH($name,$params = array())
	{
		$tplm = TemplateManager::getInstance();
		$helper = $tplm->getHelper($name);
		if ($helper == null)
		{
			throw new TemplateException('TplException: Cant find helper ' . $name);
		}
		$helper->run($this,$params);
	}

	/**
	 * Alternative version of getH
	 * @param string $name
	 * @param array $params
	 */
	public function getHelper($name,$params = array())
	{
		$this->getH($name,$params);
	}
	
	/**
	 * Don't use this anymore!
	 * 
	 * For use in templates, initializes a loop, uses the content of the given placeholder to fill the rows (has to be an array of arrays).
	 * 
	 * The following things are unsupported or show unpredictable behavior at the moment:
	 * -Helpers: 		Their output will get generated once, so you can just use them to generate
	 * 					static content, links to the loop array are impossible.
	 * -Nested Loops:	They throw an exception, might get changed later on.
	 * 
	 * Everything else should work as before.
	 * 
	 * InMemory Templates don't support loops yet.
	 * 
	 * 
	 * @deprecated
	 * @param string $placeholder
	 */
	protected function startLoop($placeholder)
	{
		if (array_key_exists($placeholder,$this->output))
		{
			$this->loop = 1;
			$this->loopplaceholder = $placeholder;
			ob_start();
		}
		//else
		//	throw new TemplateException("TplErr: Placeholder " . $placeholder . " for loop  unknown" );
	}
	
	/**
	 * Don't use this anymore!
	 * Used to end a loop started with startLoop. For use in templates.
	 * 
	 * @deprecated
	 * @throws TemplateException
	 */
	protected function endLoop()
	{
		if (array_key_exists($this->loopplaceholder, $this->output))
		{
			if ($this->loop == 0||$this->loop == 2)
			{
				throw new TemplateException("TplErr: Invalid use of endLoop in module " . $this->module);
			}
			
			$this->loop = 2;
			$cont = ob_get_clean();
			//echo $cont;
			$this->loopi = 0;
			//for ($i = 0; $i < count($this->output[$this->loopplaceholder]); $i++)
			TemplateManager::log("Loop: \n\r",$this->output[$this->loopplaceholder]);
			foreach ($this->output[$this->loopplaceholder] as $i => $row)
			{
				$ret = "";
				$this->loopi = $i;
				$ret = $cont;
				foreach ($row as $name => $value) {
					if (is_array($value))
					{
						TemplateManager::log("Putting Array together: " , $value);
						$value = implode($value);
					}
					$ret = str_replace("{{".$name."}}", $value, $ret);
				}
				$ret = preg_replace("(\{\{[^\}]*\}\})", '', $ret); //Remove all unreplaced Placeholders
				echo $ret;
			}
			TemplateManager::log("Loop-i = " . count($this->output[$this->loopplaceholder]));
			$this->loop = 0;
			$this->loopi = 0;
			$this->loopplaceholder = "";
		}
	}
	
	/**
	 * Add something that generates output. That can include: Strings, other Templates and so on.
	 * You can also add variables that won't be published, for example to have logic in your templates.
	 * 
	 * @param String $placeholder
	 * @param mixed $cont
	 */
	public function addOutput($placeholder,$cont)
	{
		if (array_key_exists($placeholder, $this->output))
		{
			if(is_array($this->output[$placeholder])&&!$this->isAssoc($this->output[$placeholder]))
			{
				$this->output[$placeholder][] = $cont ;
			}
			else
			{
				$this->output[$placeholder] = array($this->output[$placeholder],$cont);
			}
		}
		else
		{
			$this->output[$placeholder] = $cont;
		}
	}
	
	
	/**
	 * Used in addOutput to check wether an array is associative.
	 * @param Array $array
	 * @return boolean
	 */
	protected function isAssoc($array) {
  		return (bool)count(array_filter(array_keys($array), 'is_string'));
	}
	
	/**
	 * For internal use, goes through all modules stored in the array
	 * and runs them. This works recursive, so mods in an array in the array
	 * 
	 * @param array $array
	 */
	protected function runModules($array)
	{
		foreach ($array as $pos => $obj) {
			if (is_array($obj))
			{
				$array[$pos] = $this->runModules($obj);
			}
			if (is_object($obj))
			{
				if ($this->namespace_global)
				{
					$obj->setNamespace($this->namespace,true);
				}
				$array[$pos] = $obj->run();
			}
		}
		return $array;
	}
	
	/**
	 * Use this to parse an array into several submodules of the same type.
	 * The array that should be passed to this function should look like this:
	 * [["content1stuff","content2stuff"],["content1otherstuff","content2otherstuff"],....]
	 * The internalplaceholdersarray should contain the placeholders for the values in the array:
	 * ["content1","content2"]
	 * You can use this for example to show a table containing stuff, using a module for a single line of the template and the parent
	 * to define the table itself.
	 * Its supposed to be used within loops.
	 *
	 * @param string $placeholder
	 * @param array $internalplaceholders
	 * @param array $content
	 * @return unknown
	 */
	public function parseArray($placeholder,$internalplaceholders,$content)
	{
		$return = array();
		foreach ($content as $singlec) {
			$icont = array();
			foreach ($singlec as $id => $value) {
				$icont[$internalplaceholders[$id]] = $value;
			}
			$return[] = $icont;
		}
		return $return;
	}
	
	
	/**
	 * Returns true if either the placeholder is set (as long as $value == null) or the placeholder has the specified value.
	 * 
	 * @param string $placeholder
	 * @param mixed $value
	 * @return boolean
	 */
	public function is($placeholder,$value=null)
	{
		$var = $this->get($placeholder,true,true);
		if ($value !== null && $var == $value)
		{
			return true;
		}
		else
		{
			if ($value===null && $var !== null)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return false;
	}
	
	/**
	 * Sets the namespace to "" (root-dir) and locks the template. Usefull for oftenly used templates.
	 */
	public function defNamespace()
	{
		$this->setNamespace("");
		$this->setLock();
	}
	
	/**
	 * New loop function, to be used within PHP default loops (while($this->refLoop($var)).
	 * 
	 * It forces get and is to use an element of the array within output[$name], and with
	 * every call from there on, it will replace the content with the next row within that array.
	 * When there are no rows left it returns false.
	 * 
	 * It also returns false when: a) the placeholder name doesn't exist
	 * 							   b) the placeholder is no array
	 * 							   c) it can't find a 'first' key to use
	 * 
	 * The input placeholder is supposed to look like this:
	 * array(0 => array('Var1'=>'Stuff','Var2'=>'Stuff'),
	 * 		 1 => array('Var1'=>'Stuff','NoVar2Here'=>'Stuff'))
	 * 
	 * is('Var2') will return true within the first and false within the 2nd row.
	 * 
	 * The loop stops when there are missing keys, e.g. array(1 =>...,2 =>...,4 =>...).
	 * 
	 * @param string $placeholder
	 * @param boolean $forcestart
	 * @param number $startvar
	 * @return boolean
	 */
	public function loop($name, $forcestart = false, $startvar = 0) {
		$l = $this->reference_level;
		if ($l >= 0 && $this->reference_origin[$l] == $name) {
			if ($l > 0 && array_key_exists($this->reference_next[$l], $this->output_reference[$l-1][$this->reference_origin[$l]])) {
			
				$l = $this->reference_level;
				$this->output_reference[$l] = $this->output_reference[$l-1][$this->reference_origin[$l]][$this->reference_next[$l]];
				$this->reference_next[$l] = $this->reference_next[$l] + 1;
				return true;
				
			} else if ($l == 0 && array_key_exists($this->reference_next[0], $this->output[$this->reference_origin[0]])) {
			
				$this->output_reference[0] = $this->output[$this->reference_origin[0]][$this->reference_next[0]];
				$this->reference_next[0] = $this->reference_next[0] + 1;
				return true;
				
			} else {
				
				unset ($this->output_reference[$l]);
				unset ($this->reference_next[$l]);
				unset ($this->reference_origin[$l]);
				
				--$this->reference_level;
				
				return false;			
				
			}
		} else {
			if ($this->reference_level == -1 && array_key_exists($name, $this->output) && is_array($this->output[$name]) && array_key_exists($startvar, $this->output[$name])) {
				++$this->reference_level;
				$this->output_reference = array($this->output[$name][$startvar]);
				$this->reference_origin = array($name);
				$this->reference_next = array($startvar + 1);
				return true;
			} else if ($this->reference_level >= 0 && array_key_exists($name, $this->output_reference[$this->reference_level]) && is_array($this->output_reference[$this->reference_level][$name]) && array_key_exists($startvar, $this->output_reference[$this->reference_level][$name])) {
				++$this->reference_level;
				$l = $this->reference_level;
				$this->output_reference[$l] = $this->output_reference[$l - 1][$name][$startvar];
				$this->reference_origin[$l] = $name;
				$this->reference_next[$l] = $startvar + 1;
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Wrapper for $this->loop()
	 * @param String $name
	 * @param Boolean $forcestart
	 * @param Integer $startvar
	 * @return boolean
	 */
	public function refLoop($name, $forcestart = false, $startvar = 0)
	{
		return $this->loop($name, $forcestart, $startvar);
	}
	
	/**
	 * This function tests wether the given variable can be used
	 * as a loop variable without further modifications.
	 * @param mixed $arr
	 * @return boolean
	 */
	public function isLoopable($arr) {
		if (!is_array($arr)) {
			return false;
		} else {
			if (!is_array(array_first($arr))||$this->isAssoc(array_first($arr))) {
				return false;
			}
			return true;
		}
	}
	
	/**
	 * This function tries to transform any variable into a variable
	 * that can be used as a loop variable. If the variable is a object
	 * and/or a scalar the return will be an array containing another array
	 * with LoopVar as only variable. When it's impossible to modify $var, an
	 * empty array of arrays is returned.
	 * @param mixed $var
	 * @return array
	 */
	public function makeLoopable($var) {
		if (!is_array($var)) {
			return array(array('LoopVar'=>$var));
		} else {
			if (!$this->isAssoc($arr) && $this->isAssoc(array_first($var))) {
				return $var;
			}
			if ($this->isAssoc($arr)) {
				return array($arr);
			} else {
				return array(array());
			}
		}
	}
	
	/**
	 * This function moves the content of a placeholder into the output array.
	 * The variables will become available as placeholders.
	 * The function removes the origin if it exists.
	 * @param String $var
	 */
	public function shift($var) {
		if (array_key_exists($var, $this->output)) {
			if (!is_array($this->output[$var])) {
				return;
			}
			$this->output = array_merge($this->output, $this->output[$var]);
			unset($this->output[$var]);
		}
	}
	
	/**
	 * The following 3 functions allow it to change the content of a placeholder
	 * before displaying its content.
	 * 
	 * To do so, the designer provides a place to search, an array that describes
	 * the keys that will be searched.
	 * 
	 * For example:
	 * array('TableContent',0,'Name')
	 * will search for a placeholder called TableContent, that is a list of
	 * table-rows that contain a placeholder called Name and it will only select the first line (0).
	 * 
	 * Then the algorithm will replace all found occurences of $search with the
	 * value stored in $replace.
	 * 
	 * Using an empty array instead of an explicit key in the $place array will force the function
	 * to search in all subarrays of the given key.
	 * 
	 * For example:
	 * array('TableContent',[],'Name')
	 * will work similar to the first example, but will replace occurences of $search in all
	 * Names within all table rows.
	 * 
	 * It will return true if the function was able to find the specified place
	 * otherwise it will return false.
	 * 
	 * mapRegEx and mapFunction work in a similar way, but use regular expressions
	 * or a function given by the user to search/replace content.
	 * 
	 * mapFunction has only one parameter, func.
	 * func should contain the name of a callable user function with exactly one parameter.
	 * This parameter contains the old content of the placeholder, and it should return the new content.
	 * 
	 * @param array $place
	 * @param string $search
	 * @param string $replace
	 * @return boolean
	 */
	public function map($place, $search, $replace)
	{
		return $this->mapFree($this->output, $place, 'rep', $search, $replace);
	}
	
	/**
	 * Alternative to map, uses preg_replace instead of str_replace
	 * @param array $place
	 * @param string $search
	 * @param string $replace
	 * @return boolean
	 */
	public function mapPreg($place, $search, $replace)
	{
		return $this->mapFree($this->output, $place, 'preg', $search, $replace);
	}
	
	/**
	 * Alternative to map, uses a user-given-function instead of replace.
	 * For further explanation check map.
	 * 
	 * @param array $place
	 * @param string $func
	 * @return boolean
	 */
	public function mapFunction($place, $func)
	{
		return $this->mapFree($this->output, $place, 'func', $func);
	}
		
	/**
	 * The real map function
	 * 
	 * @param unknown $original_array
	 * @param unknown $path
	 * @param unknown $mode
	 * @param unknown $search
	 * @param string $new_value
	 * @throws TemplateException
	 * @return boolean
	 */
	protected function mapFree(&$original_array, $path,$mode, $search, $new_value = '') {
    	$cursor =& $original_array;
    	if (!is_array($path))
    	    return false;
    	while($path) {
    	    $index = array_shift($path);
    	    if (!is_array($cursor) || (!is_array($index) && !isset($cursor[$index])))
    	        return false;
    	    if (is_array($index)) {
    	        foreach($cursor as &$child)
    	            $this->mapFree($child, $path, $mode, $search, $new_value);
    	        return true;
    	    } else {
    	        $cursor =& $cursor[$index];
    	    }
    	}
    	switch ($mode) {
    		case 'func' : 
    			$cursor = call_user_func($search,$cursor);
    			break;
    		case 'preg' :
    			$cursor = preg_replace($search, $new_value, $cursor);
    			break;
    		case 'rep' :
    			$cursor = str_replace($search, $new_value, $cursor);
    			break;
    		default :
    			throw new TemplateException('TplErr: replace_array used with missing mode.');
    	}
    	return true;
	}
	
	/**
	 * This debug function generates a list of all submodules.
	 */
	public function generateModTree($level = 0) {
		return $this->parseModNames($this->output, $level);
	}
	
	protected function parseModNames($array, $level) {
		$ret = '';
		for ($i = 0;$i < $level * 4; $i++) {
			$ret .= '&nbsp';
		}
		$ret .= '{ <br />';
		$level++;
		$only_scalars = true;
		if (count($array) == 0) {
			for ($i = 0;$i < $level * 4; $i++) {
				$ret .= '&nbsp';
			}
			$ret .= '(NO SUBCONTENT)<br />';
			$only_scalars = false;
		} else {
			foreach ($array as $key => $mod) {
				for ($i = 0;$i < $level * 4; $i++) {
					$ret .= '&nbsp';
				}
				if (is_array($mod)) {
					$ret .= $key . '=> (ARRAY): <br />';
					$ret .= $this->parseModNames($mod, $level);
				}
				if (is_object($mod)) {
					$ret .= $key . '=>' . $mod->getModuleName() . ' (MODULE):<br />'; 
					$ret .= $mod->generateModTree($level);
				}
				if (!is_scalar($mod)) {
					$only_scalars = falsE;
				}
			} 
		}
		if ($only_scalars) {
			$ret .= ' (ONLY SCALARS)<br />';
		}
		for ($i = 0;$i < ($level-1) * 4; $i++) {
			$ret .= '&nbsp';
		}
		$ret .= '} <br />';
		return $ret;
	}
	
	/**
	 * Returns the name of this module.
	 * @return string
	 */
	public function getModuleName() {
		return $this->module;
	}
}

/**
 * Inmemory-Template Class
 * 
 * This is an template that is not loaded everytime you run it, but it's content is stored in-memory before
 * you run it the first time. This is good for small templates you need often (for example a line of a table).
 * IM-templates don't support some advanced features real templates as they don't support inline-code without using eval.
 * 
 * @author Alexander Kastius
 *
 */
class TemplateInMemory extends TemplateModule
{
	
	/**
	 * Use this to run the InMemoryTemplate, important:
	 * It will search for the inmemory template, if its not included yet.
	 * 
	 * @see TemplateModule::run()
	 */
	public function run()
	{
		$tplm = TemplateManager::getInstance();
		if ($this->namespace !=  "")
		{
			$this->module = $this->namespace . '/' . $this->module;
		}
		if (!$tplm->inMemoryExists($this->module)) {
			if(!$tplm->loadInMemoryTemplate($this->module))
			{
				throw new TemplateException("TplError: InMemory Module " . $this->module . " doesn't exist in " . $this->tpl);
				return "";
			}
		}
		$code = $tplm->getInMemoryTemplate($this->module);
		
		$this->output = array_merge_recursive($this->runModules($this->output),$this->localRequirements);
		
		$this->output["css"] = './tpl/' . $this->tpl . '/css/';
		$this->output["img"] = './tpl/' . $this->tpl . '/img/';
		
		foreach ($this->output as $name => $value) {
			if (is_array($this->output[$name]))
			{
				$value = implode($this->output[$name]);
			}
			$code = str_replace("{{".$name."}}", $value, $code);
		}
		
		$code = preg_replace('(\{\{[^\}]*\}\})', '', $code);
		
		return $code ;
	}
	
	
}

/**
 * TemplatePseudo
 * this contains a pseudo template.
 * 
 * Pseudo templates don't use any template, they
 * just add their own placeholder array to the parent module.
 * 
 * This is supposed to be used for loops. Other uses can have strange
 * side effects.
 * 
 * @author Alexander Kastius
 */
class TemplatePseudo extends TemplateModule {
	
	/**
	 * Use this to run the TemplatePseudo, important:
	 * This template doesn't use any real templates,
	 * it just returns a merged version of the executed output array and
	 * the local requirement array.
	 * 
	 * @return array
	 */
	public function run() {
		return array_merge_recursive($this->runModules($this->output),$this->localRequirements);	
	}
}
