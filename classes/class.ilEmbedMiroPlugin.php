<?php
 
/**
 * EmbedMiro Plugin for Page Editor
 *
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 *
 */
class ilEmbedMiroPlugin extends ilPageComponentPlugin {
        public function getPluginName(): string {
            return 'EmbedMiro';
        }
        
        
        public function isValidParentType($a_parent_type) : bool{
            return true;
        }
}
?>
