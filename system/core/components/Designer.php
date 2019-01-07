<?php

namespace VoidEngine;

/**
 * @package VoidDesigner
 * 
 * ! Разработан для работы в рамках VoidStudio
 * ! Для портирования в свой проект - измените обозначеный ниже код
 * 
 */

class VoidDesigner extends Component
{
    protected $form;
    protected $control;
    protected $host;
    protected $selService;

    public function __construct (Control $parent, $propertyGrid)
    {
        if ($propertyGrid instanceof PropertyGrid || $propertyGrid instanceof PropertyGridEx)
        {
            $this->form = new Form;
            $this->form->caption = 'Form Caption';

            $this->componentSelector = VoidEngine::createObject (new WFObject ('WinForms_PHP.FormDesigner', false, true), $this->form->selector, 'object', 'form', 'string');
            Components::addComponent ($this->componentSelector, $this);

            $this->control    = $this->callMethod ('GetControl', 'object');
            $this->host       = $this->callMethod ('GetHost', 'object');
            $this->selService = $this->callMethod ('GetSelService', 'object');

            /**
             * * Удаление объектов или формы по нажатию кнопки "Del"
             * Изменить:
             * 
             * @var menu
             * @var objects
             * 
             * А так же систему парсинга названий объектов форм ($GLOBALS["forms"]...)
             */
            VoidEngine::setObjectEvent ($this->control, 'KeyDown', '
                namespace VoidEngine;

                $args = new KeyEventArgs ($args);

                if ($args->keycode == 46)
                {
                    $objects     = VoidEngine::callMethod ('. $this->componentSelector .', "GetSelectedComponents", "object");
                    $firstObject = VoidEngine::getArrayValue ($objects, 0, "object");
                    $className   = substr (explode (".", explode (",", $content)[0])[3], 0, -1);
                    $component   = Components::getComponent ($firstObject);

                    $menu    = VoidStudioAPI::getObjects ("main")["TopMenu"];
                    $form    = VoidEngine::getProperty ($menu->selectedTab, "Text");
                    $objects = VoidStudioAPI::getObjects ("main")["Objects"];

                    if ($component instanceof Form)
                    {
                        if ($menu->tabPages->count > 1)
                        {
                            $objects->items->clear ();

                            if (is_array ($GLOBALS["forms"][$form]))
                                foreach ($GLOBALS["forms"][$form] as $id => $name)
                                {
                                    $obj = substr ($name, 1, strpos ($name, "]") - 1);

                                    if ($obj = Components::getComponent ($obj))
                                        $obj->dispose ();

                                    else VoidEngine::callMethod ($obj, "Dispose");
                                }

                            unset ($GLOBALS["forms"][$form]);

                            foreach ($menu->tabPages->list as $id => $selector)
                                if (VoidEngine::getProperty ($selector, "Text") == $form)
                                {
                                    VoidEngine::callMethod ($selector, "Dispose");
                                    
                                    break;
                                }

                            // VoidStudioAPI::$objects["main"]["{$form}Designer"]->dispose ();
                        }
                        
                        else pre (text ("Нельзя удалить единственную форму проекта"));
                    }

                    else
                    {
                        $cmp = "[$firstObject] $className";

                        $objects->selectedItem = $cmp;
                        $objects->items->remove ($objects->selectedIndex);

                        $component->dispose ();

                        foreach ($GLOBALS["forms"][$form] as $id => $name)
                            if ($name == $cmp)
                            {
                                unset ($GLOBALS["forms"][$form][$id]);

                                break;
                            }
                    }
                }
            ');

            VoidEngine::setProperty ($this->control, 'Parent', $parent->selector, 'object');

            /**
             * * Выделение компонентов на форме
             * Изменить последнюю строку с указанием выделенного объекта
             */
            VoidEngine::setObjectEvent ($this->selService, 'SelectionChanged', '
                namespace VoidEngine;

                $objects = VoidEngine::callMethod ('. $this->componentSelector .', "GetSelectedComponents", "object");

                VoidEngine::setProperty ('. $propertyGrid->selector .', "SelectedObjects", $objects, "object");

                $firstObject = VoidEngine::getArrayValue ($objects, 0, "object");
                $content     = VoidEngine::callMethod ($firstObject, "ToString");
                $className   = substr (explode (".", explode (",", $content)[0])[3], 0, -1);

                VoidStudioAPI::getObjects ("main")["Objects"]->selectedItem = "[$firstObject] $className";
                VoidStudioAPI::loadObjectEvents (Components::getComponent ($firstObject), VoidStudioAPI::getObjects ("main")["LeftMenu__EventsList"]);
            ');
        }

        else throw new \Exception ('$propertyGrid must be instance of "VoidEngine\PropertyGrid" or "VoidEngine\PropertyGridEx"');
    }

    public function updateHost ()
    {
        $this->callMethod ('UpdateHost');
    }

    public function focus ()
    {
        $this->form->focus ();
    }

    public function addComponent (Control $component, string $name)
    {
        VoidEngine::callMethod ($this->host, 'Add', '', $component->selector, 'object', $name, 'string');
        // VoidEngine::setProperty ($component->selector, 'Parent', $this->form->selector, 'object');
        
        $this->focus ();
        $this->callMethod ('SetSelectedComponents', $component->selector, 'object');
    }
}

?>
