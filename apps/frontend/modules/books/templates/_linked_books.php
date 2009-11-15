<?php
use_helper('AutoComplete');

if (count($associated_books) == 0 && !$needs_add_display): ?>
    <p><?php echo __('No linked book') ?></p>
<?php
else : 
    $doc_id = $document->get('id');
    $strict = (int)$strict; // cast so that false is 0 and true is 1.
    if (count($associated_books) > 0):
    $extra_book = false;
    ?>
    <ul class="children_docs">
    <?php foreach ($associated_books as $book):
            $book_id = $book->get('id');
            $idstring = $type . '_' . $book_id;
            $class = 'child_book';
            if (!$extra_book && isset($book['parent_id']))
            {
                $class .= ' separator';
                $extra_book = true;
            }
            echo '<li class="' . $class . '" id="' . $idstring .'">';
            echo link_to($book->get('name'), '@document_by_id?module=books&id=' . $book_id);
            if (isset($book['author']) && trim($book['author']) != '')
            {
                echo ' - ' . $book['author'];
            }
            if (!isset($book['parent_id']) && $sf_user->hasCredential('moderator'))
            {
                $idstring = $type . '_' . $book_id;
                echo c2c_link_to_delete_element($type, $book_id, $doc_id, false, $strict);
            }
            ?>
            </li>
    <?php endforeach; ?>
    </ul> <?php
    endif;
endif;
