<?php

namespace gsmParameters;

use models\GsmLanguage as GsmLanguage;
use DomDocument;

class SL60_parameters_script_gsm
{

    public function __construct()
    {
//        $Script3D = new GsmLanguage();
//        $this->start3dScript($Script3D);
//        $this->functions3dScript($Script3D);
//        $Script3D->outputText($Script3D->expression);
//        $this->openXmlFile('nanawall_SL60_door_gsm.xml', $Script3D->gsm_text);
//
//        file_put_contents(get_class($this) . '.txt', $Script3D->gsm_text);
        //echo "<pre>" . $Script3D->gsm_text . "</pre>";
    }

    public function openXmlFile($file, $data)
    {
        $xml = new DomDocument("1.0", "UTF-8");
        $xml->load($file);
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;

        $Script_3D = $xml->getElementsByTagName("Script_3D")->item(0);
        $newText = $xml->createCDATASection($data);
        $Script_3D->replaceChild($newText, $Script_3D->childNodes->item(0));

        $xml->save($file);
    }


}
