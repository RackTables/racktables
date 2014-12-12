Documentation of the template engine 
====================================

### @autors Alexander Kastisus and Jakob Frick

The template engine allows to create templates for html web pages (files ending with `.tpl.php`). The template engine is written in php only and implemented in `template.php`.

## Basic use of the template engine ##

To load a template you get the current instance of the template manager and then generate a template

    $tplm = TemplateManager::getInstance();
    $mod = $tplm->generateModule(*template_filename*);

You can generate modules, which renders a template into html. For a basic example of how a template is structured and written see `dummy.tpl.php` or `index.tpl.php`.

Modules themself consits out of the html text form the template and placeholders. A placeholder looks like this:

    <?php $this->get("*placeholder_name*")?>

or with syntactial sugar

    <?php $this->*placeholder_name* ?>

Placeholder values are set in the php code and can either be just string values or modules themself. Placeholder can be set or added. If values are added all of them are renderd in the template filen in the order they were set in the code:

- setting a placeholder
	`$mod->setOutput("*placeholder_name*", *placeholder_value*)`
- adding a placeholder
	`$mod->addOutput("*placeholder_name*", *placeholder_value*)`

To generate modules at a placeholder, you generate submodules. The submodules are rendered before the parent modules and then the generated html code is inserted at the placeholder:
	
    $mod = $tplm->generateSubmodule("*placeholder_name*",*template_filename*);

## Advanced features of the template engine ##

- For very simple templates (like single line templates) you can use generate inmemory templates, which are defined in the [global.itpl.php]. Also when generating the template you have to set the corresponding flag. Example of an inmemory template:
	`$this->setInMemoryTemplate("NoObjectLogFound","<center><h2>No logs exist</h2></center>");`
- For high frequently used functions which generate output, you can define so-called helper functions. They can be called directly in the template and are defined in [helpers.php]. The functions in helper should be the same as the corresponding ones `interface.php` or `interface-lib.php`:

<br/><br/>In the template:
<br/><pre><code><?php $this->getH("*helper_name*", array(*helper_parameters*); ?></code></pre>
If you only have one paramter, you can leave array.
<br/><br/>In `helpers.php`:
<br/><pre><code>class TemplateHelper*helper_name* extends TemplateHelperAbstract
{
        protected function generate($params)
        {
            *helper_code*
        }	
}</code></pre>

+	To enable simple logic in the template you can do if-statements in the templates themself, to check if a placeholder has a certain value. If you give no value to check for you can simply check if the placeholder exists.
<br/><pre><code><?php if ($this->is("*placeholder_name*",*placeholder_value*)) { ?>
...
<?php } ?></code></pre>

+	To add javascript or css into the templates you can use the **addJSS()** or the **addCSS()** functions in the template. They work like the ones in the backend-code but have to be called in the templates. Also there are the addRequierement functions, which add the js or css code to a template and are used by addJS and addCSS internal. You can give the function either a filename or direct the code. If you give it inline code, the second paramter has to true.
<br/><pre><code><?php $this->addJS("*filename*") ?> 
<?php $this->addJS("*inline_code*", true) ?> </code></pre>

+ 	For loops there is the option to print multiple loops. To do so you can use the loop or the refLoop function in the templates. In the backend code you have to set the corresponding placeholder to an array of arrays, which hold the values for each loop run. As alternative you can decelare so-called pseudo templates in the backend-code, which work like the code in the loop would be the code of a submodule. This should be used if it's likley that your loop code is out sourced into another submodule.

## Example:
In the backend code
	
    *$allRecordsOut = array();
    foreach ($values as $record)
    {
        $singleRow = array('I' => $i, 'Record_ID' => $record['id'], 'Record_Name' => $record['name']);
    	...
        $allRecordsOut[] = $singleRow;
    }
    $mod->addOutput('AllRecords', $allRecordsOut);*

In the template:

    <?php while($this->loop('AllRecords')) : ?>
    ...
    <?php endwhile ?>

or

    <?php while($this->refLoop('AllRecords')) : ?>
    ...
    <?php endwhile ?>

Also there is the function `startLoop()` (with the corresponding `endLoop()`), but this loop construct doesn't support loops in loops or helper in loops and is therefore deprecated and should not be used anymore. 

**For further information see the doxygen doku of the template class.**
