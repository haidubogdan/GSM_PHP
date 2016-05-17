<?php

namespace models;

class GsmLanguage
{

    public $gsm_text = "";
    public $materialValues = "";
    public $expression = "";
    public $countModification = 0;
    public $countCutplane = 0;
    public $hotspotId = 0;
    public $alias = "";

    /**
     * 
     * @param type $condition
     * @return type
     */
    public function createIfCondition($condition)
    {
        $text_offset = str_repeat("    ", $this->countModification);
        $this->countModification++;
        $text = $text_offset . "IF " . $condition . " THEN\n";
        return $this->expression .= $text;
    }

    /**
     * 
     * @return type
     */
    public function endIfCondition()
    {
        $this->countModification--;
        $text_offset = str_repeat("    ", $this->countModification);
        $text .=$text_offset . "ENDIF\n";
        return $this->expression .= $text;
    }

    public function outputText($text)
    {
        return $this->gsm_text.=$text;
    }

    public function operation($operation, $value)
    {
        $text = str_repeat("    ", $this->countModification) . $operation . " " . $value . "\n";
        $this->countModification ++;
        return $this->expression .= $text;
    }

    /**
     * 
     * @param type $expr
     * @return type
     */
    public function addToExpression($expr)
    {
        $text = str_repeat("    ", $this->countModification) . $expr . "\n";
        return $this->expression .= $text;
    }

    public function addDelete($count = 1)
    {
        if ($count <= $this->countModification)
        {
            $limit = $this->countModification - $count;
            $start = $this->countModification;
            for ($i = $start; $i > $limit; $i--)
            {
                $text = str_repeat("    ", $this->countModification - 1) . "del 1\n";
                $this->expression .= $text;
                $this->countModification--;
            }
        }
    }

    public function addDeleteAll($offset = 0)
    {
        $start = $this->countModification;
        for ($i = $start; $i > $offset; $i--)
        {
            $text = str_repeat("    ", $this->countModification - 1) . "del 1\n";
            $this->expression .= $text;
            $this->countModification--;
        }
    }

    public function createCylind($height, $radius)
    {
        $text = str_repeat("    ", $this->countModification) . "CYLIND $height, $radius \n";
        return $this->expression .= $text;
    }

    public function createBrick($length, $depth, $height)
    {
        $text = str_repeat("    ", $this->countModification) . "BRICK $length, $depth, $height \n";
        return $this->expression .= $text;
    }

    public function createMobileHotspot($param, $status1 = 128)
    {
        $text_offset = str_repeat("    ", $this->countModification);
        $text = $text_offset . "unID=" . $this->hotspotId . "\n";
        $text .= $text_offset . "unID=unID+1\n";
        $text .= $text_offset . "hotspot 0,0,0,unID,$param, 1+$status1\n";
        $text .= $text_offset . "unID=unID+1\n";
        $text .= $text_offset . "hotspot $param,0,0,unID,$param, 2\n";
        $text .= $text_offset . "unID=unID+1\n";
        $text .= $text_offset . "hotspot -1,0,0,unID,$param, 3\n";
        $this->hotspotId + 3;
        return $this->expression .= $text;
    }

    public function createForLoop($alias, $start, $limit, $step = 1)
    {
        $this->alias = $alias;
        $text_offset = str_repeat("    ", $this->countModification);
        $this->countModification++;
        $text = $text_offset . "FOR $alias = $start TO $limit STEP $step\n";
        return $this->expression .= $text;
    }

    public function endForLoop($alias = "")
    {
        if ($alias == "")
        {
            $alias = $this->alias;
        }
        $this->countModification--;
        $text_offset = str_repeat("    ", $this->countModification);
        return $this->expression .= $text_offset . "NEXT $alias\n";
    }

    public function setSimpleMaterialValues($settings = array())
    {
        $materialsValues = $settings["type"] . ",\n"
                . $settings["red"] . ", "
                . $settings["green"] . ", "
                . $settings["blue"] . " \n";
        $this->materialValues = $materialsValues;
    }

    public function defineSimpleMaterial($name)
    {
        $text = "define material \"$name\" ";
        $text.= $this->materialValues;
        return $this->expression .=$text;
    }

    public function defineGlassMaterial($name)
    {
        $text = "define material \"$name\" 0,\n";
        $text .="0.4, 0.7, 0.8, !Surface RGB
            0.95, 0.70, !ambient, diffuse
            0.50, 0.60,!specular,ADDparent
            50, !shining
            0, !ADDparency attenuation
            1, 1, 1,!Specular RGB
            0.0, 0.0, 0.0, !Emission RGB
            0.0 !Emission attenuation\n";

        return $this->expression .=$text;
    }

    public function defineAluMaterial($name)
    {
        $text = "define material \"$name\" 0,\n";
        $text .="1, 0.95, 0.75, !Surface RGB
            0.80, 0.10, !ambient, diffuse
            0.70, 0.0,!specular,ADDparent
            49, !shining
            0, !ADDparency attenuation
            1, 1, 1,!Specular RGB
            0.0, 0.0, 0.0, !Emission RGB
            0.0 !Emission attenuation\n";

        return $this->expression .=$text;
    }

    public function gosub($name, $type = '"')
    {
        $text_offset = str_repeat("    ", $this->countModification);
        return $this->expression .=$text_offset . "GOSUB $type" . $name . "$type\n";
    }

    public function showArray($name, $array)
    {
        $text = "";
        foreach ($array as $key => $value)
        {
            $text.= $name . "[" . ($key + 1) . "]=$value\n";
        }
        return $this->expression .=$text;
    }

    public function addCutplane($value = 0)
    {
        $text_offset = str_repeat("    ", $this->countModification);
        $this->countCutplane++;
        $text = "cutplane $value\n";
        return $this->expression .=$text_offset . $text;
    }

    public function cutend($value = 1)
    {
        $text_offset = str_repeat("    ", $this->countModification);
        $text = "";
        if ($value <= $this->countCutplane)
        {
            for ($i = 1; $i <= $value; $i++)
            {
                $this->countCutplane--;
                $text .= "cutend\n";
            }
        }
        return $this->expression .=$text_offset . $text;
    }

    public function cutendAll()
    {
        $text_offset = str_repeat("    ", $this->countModification);
        $text = "";
        $limit = $this->countCutplane;
        for ($i = 1; $i <= $limit; $i++)
        {
            $this->countCutplane--;
            $text .= "cutend\n";
        }
        return $this->expression .=$text_offset . $text;
    }

    public function addParameters($array = array())
    {
        $text_offset = str_repeat("    ", $this->countModification);
        $text = "";
        foreach ($array as $key => $value)
        {
            $text .= $text_offset . "$key=$value\n";
        }
        return $this->expression .=$text;
    }

    public function putParameters($array = array(), $number = 3)
    {
        $text_offset = str_repeat("    ", $this->countModification);
        $text = $text_offset . "PUT ";
        foreach ($array as $key => $value)
        {
            $text .= preg_replace("/\s+/", "", $value);
            if ($key < count($array) - 1)
            {
                $text .= ",";
            }
            if (($key + 1) % $number == 0 && $key >= ($number - 1))
            {
                $text .= "\n" . $text_offset;
            }
        }
        return $this->expression .=$text . "\n";
    }

    public function tube()
    {
        $text_offset = str_repeat("    ", $this->countModification);
        $text = $text_offset . "TUBE n , m , 1 + 2+16+32 , GET ( n * 3 ) , GET ( m * 4 )";
        return $this->expression .=$text . "\n\n";
    }

    public function prism($height = 'g_prism')
    {
        $text_offset = str_repeat("    ", $this->countModification);
        $text = $text_offset . "prism_ nsp/3,$height,get(nsp)";
        return $this->expression .=$text . "\n\n";
    }

    public function profilU()
    {
        $this->addToExpression('"profil U":');
        $parameters = explode(",", '0,0,15,
		l_prism,0,15,
		l_prism,y_prism,15,
		l_prism-g_profil,y_prism,15,
		l_prism-g_profil,g_profil,15,
		g_profil,g_profil,15,
		g_profil,y_prism,15,
		0,y_prism,15');
        $this->putParameters($parameters);
        $this->prism();
        $this->addToExpression("RETURN\n");
    }

    public function prismSemiRotunjit()
    {
        $this->addToExpression('"prism semi-rotunjit":');
        $parameters = explode(",", 'l_prism/2,0,15+64,
            l_prism-r_prism,0,15+64,
            l_prism,r_prism,1015+64,
            l_prism,y_prism,15+64,
            0,y_prism,15+64,
            0,r_prism,15+64,
            r_prism,0,1015+64');
        $this->putParameters($parameters);
        $this->prism();
        $this->addToExpression("RETURN\n");
    }

    public function prismDec()
    {
        $this->addToExpression('"prism dec":');
        $parameters = explode(",", '0,0,15,
		l_prism,0,15,
		l_prism,y_prism,15,
		0,y_prism,15');
        $this->putParameters($parameters);
        $parameters2 = explode(",", '0,0,-1,
		off_d_prism,off_d_prism,15,
		l_prism-off_d_prism,off_d_prism,15,
		l_prism-off_d_prism,y_prism-off_d_prism,15,
		off_d_prism,y_prism-off_d_prism,15,
		off_d_prism,off_d_prism,-1');
        $this->putParameters($parameters2);
        $this->prism();
        $this->addToExpression("RETURN\n");
    }

}
