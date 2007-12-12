<?php
/**
 * This helper provides MediaWiki-like diffing tools.
 * $Id: DiffHelper.php 1161 2007-08-02 19:31:00Z alex $
 */

function show_documents_diff($document1, $document2, $fields)
{
    if (get_class($document1) != get_class($document2))
    {
        return;
    }

    $haveDiff = false;

    foreach ($fields as $field)
    {
        $diff = show_texts_diff($document1->getRaw($field), $document2->getRaw($field));
        if ($diff)
        {
            if (!$haveDiff)
            {
                $haveDiff = true;
            }
            echo '<table class="diff">' . "\n";
            echo '<caption>' . __($field) . '</caption>' . "\n";
            echo '<colgroup class="diff-symbol"></colgroup><colgroup class="diff-content"></colgroup>';
            echo '<colgroup class="diff-symbol"></colgroup><colgroup class="diff-content"></colgroup>';
            echo "$diff\n";
            echo '</table>' . "\n";
        }
    }

    if (!$haveDiff)
    {
        echo '<p>' . __('No difference') . '</p>' . "\n";
    }
}

function show_texts_diff($text1, $text2)
{
    if (is_null($text1))
    {
        $text1 = '';
    }
    
    if (is_null($text2))
    {
        $text2 = '';
    }

    if ($text1 == $text2 || !is_scalar($text1) || !is_scalar($text2))
    {
        // arguments are not scalars or are identical => do nothing
        return '';
    }

    $lines1 = explode("\n", $text1);
    $lines2 = explode("\n", $text2);
    $diffs = new Diff($lines1, $lines2);
    $formatter = new TableDiffFormatter();
    return $formatter->format($diffs);
}
