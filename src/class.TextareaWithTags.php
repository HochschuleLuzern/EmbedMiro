<?php
use ILIAS\UI\Component as C;
use ILIAS\UI\Implementation\Component\Input\Field\Textarea;

class TextareaWithTags extends Textarea implements C\Input\Field\Textarea {
    public function __construct(ILIAS\Data\Factory $data_factory,
        \ILIAS\Refinery\Factory $refinery,
        $label,
        $byline) {
        ILIAS\UI\Implementation\Component\Input\Field\Input::__construct(
            $data_factory,
            $refinery,
            $label,
            $byline);
    }
}