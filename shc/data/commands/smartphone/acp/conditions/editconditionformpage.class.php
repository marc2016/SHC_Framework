<?php

namespace SHC\Command\Smartphone;

//Imports
use RWF\Core\RWF;
use RWF\Request\Commands\PageCommand;
use RWF\Request\Request;
use RWF\Util\DataTypeUtil;
use RWF\Util\Message;
use SHC\Condition\ConditionEditor;
use SHC\Condition\Conditions\DateCondition;
use SHC\Condition\Conditions\DayOfWeekCondition;
use SHC\Condition\Conditions\FileExistsCondition;
use SHC\Condition\Conditions\HumidityGreaterThanCondition;
use SHC\Condition\Conditions\HumidityLowerThanCondition;
use SHC\Condition\Conditions\LightIntensityGreaterThanCondition;
use SHC\Condition\Conditions\LightIntensityLowerThanCondition;
use SHC\Condition\Conditions\MoistureGreaterThanCondition;
use SHC\Condition\Conditions\MoistureLowerThanCondition;
use SHC\Condition\Conditions\NobodyAtHomeCondition;
use SHC\Condition\Conditions\SunriseSunsetCondition;
use SHC\Condition\Conditions\SunsetSunriseCondition;
use SHC\Condition\Conditions\TemperatureGreaterThanCondition;
use SHC\Condition\Conditions\TemperatureLowerThanCondition;
use SHC\Condition\Conditions\TimeOfDayCondition;
use SHC\Condition\Conditions\UserAtHomeCondition;
use SHC\Core\SHC;
use SHC\Form\Forms\DateConditionForm;
use SHC\Form\Forms\DayOfWeekConditionForm;
use SHC\Form\Forms\FileExistsConditionForm;
use SHC\Form\Forms\HumidityConditionForm;
use SHC\Form\Forms\LightIntensityConditionForm;
use SHC\Form\Forms\MoistureConditionForm;
use SHC\Form\Forms\NobodyAtHomeConditionForm;
use SHC\Form\Forms\SunriseSunsetConditionForm;
use SHC\Form\Forms\SunsetSunriseConditionForm;
use SHC\Form\Forms\TemperatureConditionForm;
use SHC\Form\Forms\TimeOfDayConditionForm;
use SHC\Form\Forms\UserAtHomeConditionForm;
use SHC\Form\Forms\UserForm;

/**
 * bearbeitet einen Schaltserver
 *
 * @author     Oliver Kleditzsch
 * @copyright  Copyright (c) 2014, Oliver Kleditzsch
 * @license    http://opensource.org/licenses/gpl-license.php GNU Public License
 * @since      2.0.0-0
 * @version    2.0.0-0
 */
class EditConditionFormPage extends PageCommand {

    protected $template = 'editconditionform.html';

    protected $premission = 'shc.acp.conditionsManagement';

    /**
     * Sprachpakete die geladen werden sollen
     *
     * @var Array
     */
    protected $languageModules = array('index', 'conditionmanagement', 'acpindex');

    /**
     * Daten verarbeiten
     */
    public function processData() {

        $tpl = RWF::getTemplate();

        //Headline Daten
        $tpl->assign('apps', SHC::listApps());
        $tpl->assign('acp', true);
        $tpl->assign('style', SHC::getStyle());
        $tpl->assign('user', SHC::getVisitor());
        $tpl->assign('backLink', 'index.php?app=shc&m&page=listconditions');
        $tpl->assign('device', SHC_DETECTED_DEVICE);

        //Bedingung Objekt laden
        $conditionId = RWF::getRequest()->getParam('id', Request::GET, DataTypeUtil::INTEGER);
        $condition = ConditionEditor::getInstance()->getConditionByID($conditionId);

        //Formulare je nach Objekttyp erstellen
        if ($condition instanceof HumidityGreaterThanCondition) {

            //Luftfeuchte groeßer als
            $conditionForm = new HumidityConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $sensors = $conditionForm->getElementByName('sensors')->getValues();
                $humidity = $conditionForm->getElementByName('humidity')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editHumidityGreaterThanCondition($conditionId, $name, $sensors, $humidity, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof HumidityLowerThanCondition) {

            //Luftfeuchte kleiner als
            $conditionForm = new HumidityConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $sensors = $conditionForm->getElementByName('sensors')->getValues();
                $humidity = $conditionForm->getElementByName('humidity')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editHumidityLowerThanCondition($conditionId, $name, $sensors, $humidity, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof LightIntensityGreaterThanCondition) {

            //Lichtstaerke groeßer als
            $conditionForm = new LightIntensityConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $sensors = $conditionForm->getElementByName('sensors')->getValues();
                $lightIntensity = $conditionForm->getElementByName('lightIntensity')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editLightIntensityGreaterThanCondition($conditionId, $name, $sensors, $lightIntensity, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof LightIntensityLowerThanCondition) {

            //Lichtstaerke kleiner als
            $conditionForm = new LightIntensityConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $sensors = $conditionForm->getElementByName('sensors')->getValues();
                $lightIntensity = $conditionForm->getElementByName('lightIntensity')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editLightIntensityLowerThanCondition($conditionId, $name, $sensors, $lightIntensity, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof MoistureGreaterThanCondition) {

            //Feuchtigkeit groeßer als
            $conditionForm = new MoistureConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $sensors = $conditionForm->getElementByName('sensors')->getValues();
                $moisture = $conditionForm->getElementByName('moisture')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editMoistureGreaterThanCondition($conditionId, $name, $sensors, $moisture, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof MoistureLowerThanCondition) {

            //Feuchtigkeit kleiner als
            $conditionForm = new MoistureConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $sensors = $conditionForm->getElementByName('sensors')->getValues();
                $moisture = $conditionForm->getElementByName('moisture')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editMoistureLowerThanCondition($conditionId, $name, $sensors, $moisture, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof TemperatureGreaterThanCondition) {

            //Temperatur groeßer als
            $conditionForm = new TemperatureConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $sensors = $conditionForm->getElementByName('sensors')->getValues();
                $temperature = $conditionForm->getElementByName('temperature')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editTemperatureGreaterThanCondition($conditionId, $name, $sensors, $temperature, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof TemperatureLowerThanCondition) {

            //Temperatur kleiner als
            $conditionForm = new TemperatureConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $sensors = $conditionForm->getElementByName('sensors')->getValues();
                $temperature = $conditionForm->getElementByName('temperature')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editTemperatureLowerThanCondition($conditionId, $name, $sensors, $temperature, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof NobodyAtHomeCondition) {

            //Niemand zu Hause
            $conditionForm = new NobodyAtHomeConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editNobodyAtHomeCondition($conditionId, $name, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof UserAtHomeCondition) {

            //Benutzer zu Hause
            $conditionForm = new UserAtHomeConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $users = $conditionForm->getElementByName('users')->getValues();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editUserAtHomeCondition($conditionId, $name, $users, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof DateCondition) {

            //Datumsbereich
            $conditionForm = new DateConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            //Formular Validieren
            $valid = true;
            if ($conditionForm->isSubmitted()) {

                //Name
                if (!$conditionForm->validateByName('name')) {

                    $valid = false;
                }

                //Aktiviert
                if (!$conditionForm->validateByName('enabled')) {

                    $valid = false;
                }

                //Start Datum
                if (!$conditionForm->validateByName('startDate')) {

                    $valid = false;
                }
                $startDate = $conditionForm->getElementByName('startDate');
                $matches = array();
                preg_match('#(\d\d)\-(\d\d)#', $startDate->getValue(), $matches);
                if (!isset($matches[1]) || !isset($matches[2]) || (int)$matches[1] < 1 || (int)$matches[1] > 12 || (int)$matches[2] < 1 || (int)$matches[2] > 31) {

                    $conditionForm->markElementAsInvalid('startDate', RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.invalidDate', $startDate->getTitle()));
                    $valid = false;
                }

                //End Datum
                if (!$conditionForm->validateByName('startDate')) {

                    $valid = false;
                }
                $endDate = $conditionForm->getElementByName('endDate');
                $matches = array();
                preg_match('#(\d\d)\-(\d\d)#', $endDate->getValue(), $matches);
                if (!isset($matches[1]) || !isset($matches[2]) || (int)$matches[1] < 1 || (int)$matches[1] > 12 || (int)$matches[2] < 1 || (int)$matches[2] > 31) {

                    $conditionForm->markElementAsInvalid('endDate', RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.invalidDate', $endDate->getTitle()));
                    $valid = false;
                }
            }

            if ($conditionForm->isSubmitted() && $valid === true) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $startDate = $conditionForm->getElementByName('startDate')->getValue();
                $endDate = $conditionForm->getElementByName('endDate')->getValue();

                $message = new Message();
                //Speichern
                try {

                    ConditionEditor::getInstance()->editDateCondition($conditionId, $name, $startDate, $endDate, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof DayOfWeekCondition) {

            //Wochentagebereich
            $conditionForm = new DayOfWeekConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $startDay = $conditionForm->getElementByName('startDay')->getValue();
                $endDay = $conditionForm->getElementByName('endDay')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editDayOfWeekCondition($conditionId, $name, $startDay, $endDay, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof TimeOfDayCondition) {

            //Uhrzeitbereich
            $conditionForm = new TimeOfDayConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();
                $startHour = $conditionForm->getElementByName('startHour')->getValue();
                $startMinute = $conditionForm->getElementByName('startMinute')->getValue();
                $endHour = $conditionForm->getElementByName('endHour')->getValue();
                $endMinute = $conditionForm->getElementByName('endMinute')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editTimeOfDayCondition($conditionId, $name, $startHour . ':' . $startMinute, $endHour . ':' . $endMinute, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof SunriseSunsetCondition) {

            //Uhrzeitbereich
            $conditionForm = new SunriseSunsetConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editSunriseSunsetCondition($conditionId, $name, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';;
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof SunsetSunriseCondition) {

            //Uhrzeitbereich
            $conditionForm = new SunsetSunriseConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editSunsetSunriseCondition($conditionId, $name, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } elseif ($condition instanceof FileExistsCondition) {

            //Datei vorhanden
            $conditionForm = new FileExistsConditionForm($condition);
            $conditionForm->setView(UserForm::SMARTPHONE_VIEW);
            $conditionForm->setAction('index.php?app=shc&m&page=editconditionform&id=' . $condition->getId());
            $conditionForm->addId('shc-view-form-editCondition');

            if ($conditionForm->isSubmitted() && $conditionForm->validate()) {

                //Werte vorbereiten
                $name = $conditionForm->getElementByName('name')->getValue();
                $path = $conditionForm->getElementByName('path')->getValue();
                $wait = $conditionForm->getElementByName('wait')->getValue();
                $delete = $conditionForm->getElementByName('delete')->getValue();
                $invert = $conditionForm->getElementByName('invert')->getValue();
                $enabled = $conditionForm->getElementByName('enabled')->getValue();

                //Speichern
                $message = new Message();
                try {

                    ConditionEditor::getInstance()->editFileExistsCondition($conditionId, $name, $path, $invert, $wait, $delete, $enabled);
                    $message->setType(Message::SUCCESSFULLY);
                    $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.success'));
                } catch (\Exception $e) {

                    if ($e->getCode() == 1502) {

                        //Name schon vergeben
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1502'));
                    } elseif ($e->getCode() == 1102) {

                        //fehlende Schreibrechte
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.1102'));
                    } else {

                        //Allgemeiner Fehler
                        $message->setType(Message::ERROR);
                        $message->setMessage(RWF::getLanguage()->get('acp.conditionManagement.form.condition.error'));
                    }
                }
                RWF::getSession()->setMessage($message);

                //Umleiten
                $this->response->addLocationHeader('index.php?app=shc&m&page=listconditions');
                $this->response->setBody('');
                $this->template = '';
            } else {

                $tpl->assign('conditionForm', $conditionForm);
            }
        } else {

            //Ungueltige ID
            $tpl->assign('message', new Message(Message::ERROR, RWF::getLanguage()->get('acp.conditionManagement.form.condition.error.id')));
            return;
        }
    }

}