<?php
 
/**
 * EmbedMiro Plugin for Page Editor
 *
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 *
 */
class ilEmbedMiroPlugin extends ilPageComponentPlugin {
        public function getPluginName() {
            return 'EmbedMiro';
        }
        
        
        public function isValidParentType($a_parent_type) {
            return true;
        }
}
?>
