<?php
/**
 * @package ZT Visitor Counter module
 * @author Hiepvu
 * @copyright(C) 2013 - ZooTemplate.com
 * @license PHP files are GNU/GPL
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html');
jimport('joomla.access.access');
jimport('joomla.form.formfield');
class JFormFieldWidthtype extends JFormField
{

    protected $type = 'widthtype';

    function getLabel()
    {
        return parent::getLabel();
    }

    protected function getInput()
    {

        $class = $this->element['class'] ? ' class="' . (string)$this->element['class'] . '"' : '';
        $size = $this->element['size'] ? ' size="' . (int)$this->element['size'] . '"' : 1;
        $default = $this->element['default'] ? $this->element['default'] : '';

        $options = array();

        $options[] = JHtml::_('select.option', '%', JText::_('%'));
        $options[] = JHtml::_('select.option', 'px', JText::_('px'));

        $value1 = '';
        $value2 = '';
        if (is_array($this->value)) {
            $value1 = $this->value[0];
            $value2 = $this->value[1];
        } else {
            $value1 = $default;
            $value2 = '%';
        }

        return '<input type="text" name="' . $this->name . '[]" id="' . $this->id . '-input"' . ' value="' . $value1 . '"' . $class . ' style="float:left;"/>
            ' . JHtml::_('select.genericlist', $options, $this->name . '[]', '' . $size . '', 'value', 'text', $value2, $this->id . '-type');
    }
}  