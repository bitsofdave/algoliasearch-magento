<?php

/**
 * Algolia custom sort order field
 */
class Algolia_Algoliasearch_Block_System_Config_Form_Field_Customsortorderproduct extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $selectFields = array();

    /**
     * Creates and populates a select block to represent each column in the configuration property.
     *
     * @param $columnId String The name of the column defined in addColumn
     * @return Algolia_Algoliasearch_Block_System_Config_Form_Field_Select
     * @throws Exception
     */
    protected function getRenderer($columnId) {
        if (!array_key_exists($columnId, $this->selectFields) || !$this->selectFields[$columnId]) {
            $aOptions = array();
            switch($columnId) {
                case 'attribute': // Populate the attribute column with a list of searchable attributes
                    $searchableAttributes = Mage::helper('algoliasearch')->getAllProductAttributes();

                    foreach ($searchableAttributes as $key => $label) {
                        $aOptions[$key] = $key ? $key : $label;
                    }

                    break;
                case 'searchable':
                    $aOptions = array(
                        '1' => 'Yes',
                        '0' => 'No',
                    );
                    break;
                case 'retrievable':
                    $aOptions = array(
                        '1' => 'Yes',
                        '0' => 'No',
                    );
                    break;
                case 'order':
                    $aOptions = array(
                        'ordered' => 'Ordered',
                        'unordered' => 'Unordered',
                    );
                    break;
                default:
                    throw new Exception('Unknown attribute id ' . $columnId);
            }

            $selectField = Mage::app()->getLayout()->createBlock('algoliasearch/system_config_form_field_select')->setIsRenderToJsTemplate(true);
            $selectField->setOptions($aOptions);
            $selectField->setExtraParams('style="width:160px;"');
            $this->selectFields[$columnId] = $selectField;
        }
        return $this->selectFields[$columnId];
    }

    public function __construct()
    {
        $this->addColumn('attribute', array(
            'label' => Mage::helper('adminhtml')->__('Attribute'),
            'renderer'=> $this->getRenderer('attribute'),
        ));
        $this->addColumn('searchable', array(
            'label' => Mage::helper('adminhtml')->__('Searchable'),
            'renderer'=> $this->getRenderer('searchable'),
        ));
        $this->addColumn('retrievable', array(
            'label' => Mage::helper('adminhtml')->__('Retrievable'),
            'renderer'=> $this->getRenderer('retrievable'),
        ));
        $this->addColumn('order', array(
            'label' => Mage::helper('adminhtml')->__('Ordered / Unordered'),
            'renderer'=> $this->getRenderer('order'),
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Attribute');
        parent::__construct();
    }

    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->getRenderer('attribute')->calcOptionHash(
                $row->getAttribute()),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->getRenderer('searchable')->calcOptionHash(
                $row->getSearchable()),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->getRenderer('retrievable')->calcOptionHash(
                $row->getRetrievable()),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->getRenderer('order')->calcOptionHash(
                $row->getOrder()),
            'selected="selected"'
        );
    }
}
