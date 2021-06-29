<?php
declare(strict_types = 1);
include "Customizing/global/plugins/Services/COPage/PageComponent/EmbedMiro/src/class.TextareaWithTags.php";

use ILIAS\UI\Factory;
use ILIAS\UI\Renderer;
use ILIAS\UI\Component\Input\Container\Form\Form;
use ILIAS\Refinery;
use Psr\Http\Message\RequestInterface;
use ILIAS\GlobalScreen\Scope\Layout\MetaContent\MetaContent;

/**
 * Embed Miro plugin for Page Editor
 *
 * 
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 * @ilCtrl_isCalledBy ilEmbedMiroPluginGUI: ilPCPluggedGUI
 */
class ilEmbedMiroPluginGUI extends ilPageComponentPluginGUI {
		private ilCtrl $ctrl;
        private Factory $ui_factory;
        private Renderer $ui_renderer;
        private RequestInterface $request;
        private Refinery\Factory $refinery;
        private ilGlobalPageTemplate $tpl;
        private MetaContent $gs_meta;
		
		function __construct() {
			global $DIC;
			$this->ctrl = $DIC->ctrl();
			$this->ui_factory = $DIC->ui()->factory();
			$this->ui_renderer = $DIC->ui()->renderer();
			$this->request = $DIC->http()->request();
			$this->refinery = $DIC->refinery();
			$this->tpl = $DIC->ui()->mainTemplate();
			$this->gs_meta = $DIC->globalScreen()->layout()->meta();
			
			parent::__construct();
		}
		
        public function executeCommand() {
            $cmd = $this->ctrl->getCmd();
            switch ($cmd) {
                case "create":
                case "update":
                    $this->create();
                    break;
                case "cancel":
                case "edit":
                    $this->$cmd;
                    break;
                default:
                    $this->cancel();
               
            }
        }
        
        public function insert() : void {
            $form = $this->initForm();
            $this->tpl->setContent($this->ui_renderer->render($form));
        }
        
        public function create() : void {
            $form = $this->initForm();
            $form = $form->withRequest($this->request);
            $data = $form->getData();
            $data_flat = $data["section_embed_code"]["miro_embed_code"];
            $data_flat['miro_orientation'] = $data["section_embed_code"]["miro_orientation"];
            if ($this->ctrl->getCmd() == 'create') {
                $created = $this->createElement($data_flat);
            } else {
                $created = $this->updateElement($data_flat);
            }
            
            if ($created) {
                ilUtil::sendSuccess($this->lng->txt("msg_obj_modified"), true);
            }
            
            $this->tpl->setContent($this->ui_renderer->render($form));
        }
        
        public function edit() : void{
			$form = $this->initForm();
            $this->tpl->setContent($this->ui_renderer->render($form));                
        }
        
        public function initForm() : Form {
            $action = 'create';
            
            $parse_attributes_trafo = $this->refinery->custom()->transformation(function (string $v) : array {
                $miro = [];
                preg_match('/width=[\"\']([0-9]{1,5})[\"\']/', $v, $miro['width']);
                preg_match('/height=[\"\']([0-9]{1,5})[\"\']/', $v, $miro['height']);
                preg_match('/\?moveToViewport=(-?[0-9]{1,5},-?[0-9]{1,5},-?[0-9]{1,5},-?[0-9]{1,5})/', $v, $miro['viewport']);
                preg_match('/live-embed\/([^\/]{1,20})\//', $v, $miro['hash']);
                
                array_walk($miro, function (array &$value, string $key) {
                    $value = $value[1];
                });
                $miro['hash'] = $this->refinery->string()->stripTags()->transform($miro['hash']);
                
                return $miro;
            });
            $miro_embed_code = new TextareaWithTags(
                    new ILIAS\Data\Factory(),
                    $this->refinery,
                    $this->plugin->txt('insert_miro_embed_code_label'), 
                    $this->plugin->txt('insert_miro_embed_code_byline'));
            $miro_embed_code = $miro_embed_code->withRequired(true)
                ->withAdditionalTransformation($parse_attributes_trafo);
            
            $options = [
                'left' => $this->lng->txt('cont_left'),
                'center' => $this->lng->txt('cont_center'),
                'right' => $this->lng->txt('cont_right'),
                'left_float' => $this->lng->txt('cont_left_float'),
                'right_float' => $this->lng->txt('cont_right_float')
            ];
            $create_style_trafo = $this->refinery->custom()->transformation(function ($v) use ($options) {
                if (array_key_exists($v, $options)) {
                    return $v;
                }
                return array_key_first($options);
            });
            $miro_orientation = $this->ui_factory->input()->field()->select($this->lng->txt('cont_align'), $options)
                ->withRequired(true)
                ->withAdditionalTransformation($create_style_trafo);
            
            if (count($properties = $this->getProperties())) {
                $miro_embed_code = $miro_embed_code->withValue($this->getElementHTML('for_form', $properties, $this->plugin->getVersion()));
                $miro_orientation = $miro_orientation->withValue($properties['miro_orientation']);
                $action = 'update';
            }
                
            $section = $this->ui_factory->input()->field()->section(
                ['miro_embed_code' => $miro_embed_code, 'miro_orientation' => $miro_orientation], 
                $this->plugin->txt('section_title_embed_code'));
            
            $form_actions = $this->ctrl->getFormActionByClass('ilEmbedMiroPluginGUI', $action);
            return $this->ui_factory->input()->container()->form()->standard($form_actions, ['section_embed_code' => $section]);
        }

        public function cancel() : void {
            $this->returnToParent();
        }

        public function getElementHTML($a_mode, array $properties, $a_plugin_version) : string {
        	$tpl_file = 'xmiro_for_display';
        	$display = 'none;';
        	if ($a_mode == 'edit') {
                $display = 'block';
        	}
        	
        	if ($a_mode == 'for_form') {
        	    $tpl_file = 'xmiro_for_form';
        	}
        	
        	$tpl = $this->plugin->getTemplate('default/tpl.'.$tpl_file.'.html');
        	
        	if ($a_mode != 'for_form') {
        	    $tpl->setVariable('miro_orientation', $this->getClassStringForOrientation($properties['miro_orientation']));
        	    $tpl->setVariable('display', $display);
        	}
        	
        	unset($properties['miro_orientation']);
        	
        	$this->gs_meta->addCss($this->plugin->getDirectory().'/templates/default/xmiro.css');
        	
        	foreach ($properties as $property => $value) {
        	   $tpl->setVariable($property, $value);
        	}
        	return $tpl->get();
        }
        
        private function getClassStringForOrientation(string $setting) {
            return 'xmiro_orientation_'.$setting;
        }
}
?>
