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

    /**
     * Send Info Message to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static
     *
     */
    public static function sendInfo($a_info = "", $a_keep = false)
    {
        global $DIC;

        if(isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("info", $a_info, $a_keep);
        }
    }

    /**
     * Send Failure Message to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static
     *
     */
    public static function sendFailure($a_info = "", $a_keep = false)
    {
        global $DIC;

        if (isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("failure", $a_info, $a_keep);
        }
    }

    /**
     * Send Question to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static	*/
    public static function sendQuestion($a_info = "", $a_keep = false)
    {
        global $DIC;

        if(isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("question", $a_info, $a_keep);
        }
    }

    /**
     * Send Success Message to Screen.
     *
     * @param	string	message
     * @param	boolean	if true message is kept in session
     * @static
     *
     */
    public static function sendSuccess($a_info = "", $a_keep = false)
    {
        global $DIC;

        /** @var ilTemplate $tpl */
        if(isset($DIC["tpl"])) {
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage("success", $a_info, $a_keep);
        }
    }
}
?>
