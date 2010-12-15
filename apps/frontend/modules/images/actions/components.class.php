<?php
class imagesComponents extends sfComponents
{
    public function executeForm_fields_image_type()
    {
        // who has the right to change the license:
        // * only moderators have all right
        //  - but they can switch to copyright license only if the image is associated to a book
        // * the creator can switch from personal to collaborative
        // * other users cannot

        $creator = $this->document->getCreator();
        $image_type = $this->document->get('image_type');
        // check if the image is already associated to a book (required to decide whether "copyright" is allowed or not)
        $id = $this->document['id'];
        $associations = Association::findAllAssociations($id, 'bi');

        $this->hide_image_type_edit = (!$this->moderator && $image_type != 2)
                             || (!$this->moderator && $this->getContext()->getUser()->getId() != $creator['id']);

        // allow copyright license only for images associated to books, and only to moderators
        $this->allow_copyright = $this->moderator && (count($associations) > 0 || $image_type == 3);
    }
}
