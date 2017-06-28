<?php

namespace AppBundle\Markdown;

use cebe\markdown\Parser;

use cebe\markdown\block as block;
use cebe\markdown\inline as inline;

class CriticalMarkdown extends Parser
{
// include block element parsing using traits
    use block\CodeTrait;
    use block\HeadlineTrait;
    use block\HtmlTrait {
        parseInlineHtml as private;
    }
    use block\ListTrait {
        // Check Ul List before headline
        identifyUl as protected identifyBUl;
        consumeUl as protected consumeBUl;
    }
    use block\QuoteTrait;
    use block\RuleTrait {
        // Check Hr before checking lists
        identifyHr as protected identifyAHr;
        consumeHr as protected consumeAHr;
    }

    // include inline element parsing using traits
    use inline\CodeTrait;
    use inline\EmphStrongTrait;
    use inline\LinkTrait;
}
