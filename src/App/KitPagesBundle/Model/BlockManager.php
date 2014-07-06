<?php
namespace App\KitPagesBundle\Model;

use Kitpages\CmsBundle\Entity\BlockPublish;

use Kitpages\CmsBundle\Model\BlockManager as Manager;

class BlockManager extends Manager
{
    public function deletePublished(BlockPublish $blockPublish)
    {
        $data = $blockPublish->getData();
        //$this->getCmsFileManager()->unpublishFileList($data['media']);
        $em = $this->getDoctrine()->getManager();
        $em->remove($blockPublish);
    }

    ////
    //  Validator
    ////
    public function stripTagText($text)
    {
        return $this->getUtil()->stripTags(
            array(
                //'allowTags' => array("span","div","li","ul","ol","u","i","em", "strong", "strike","b","p","br","hr", "a"),
                'allowTags' => array("img","span","div","li","ul","ol","u","i","em", "strong", "strike","b","p","br","hr", "a", "pre", "h3", "h4", "h2", "table", "tr", "th", "td", "thead", "tbody"),
                'allowAttribs' => array("class", "href", "target", "style", "src", "width", "height")
            ),
            $text
        );
    }

}
