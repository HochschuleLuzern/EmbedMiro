<?php
use ILIAS\UI\Component as C;
use ILIAS\UI\Implementation\Component\Input\Field\Textarea;
use ILIAS\Refinery\String\StripTags;

class TextareaWithTags extends Textarea implements C\Input\Field\Textarea {
    public function __construct(ILIAS\Data\Factory $data_factory,
        \ILIAS\Refinery\Factory $refinery,
        $label,
        $byline) {
        parent::__construct($data_factory, $refinery, $label, $byline);
        // now remove the StripTags transformation
        $ops = [];
        foreach ($this->getOperations() as $op) {
            if(!($op instanceof StripTags)) {
                $ops[] = $op;
            }
        }
        $this->operations = $ops;
    }
}
