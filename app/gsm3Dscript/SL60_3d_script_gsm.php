<?php

namespace Script3d;

use models\GsmLanguage as GsmLanguage;
use DomDocument;

class SL60_3d_script_gsm
{

    public function __construct()
    {
        $Script3D = new GsmLanguage();
        $this->start3dScript($Script3D);
        $this->functions3dScript($Script3D);
        $Script3D->outputText($Script3D->expression);
        $this->openXmlFile('nanawall_SL60_door_gsm.xml', $Script3D->gsm_text);

        file_put_contents(get_class($this) . '.txt', $Script3D->gsm_text);
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

    /**
     * 
     * @param type $Script3D
     */
    function start3dScript($Script3D)
    {
        $Script3D->createIfCondition("volum_3d<>1");
        $Script3D->addToExpression('END');
        $Script3D->endIfCondition();
        $Script3D->defineGlassMaterial("glass");
        $Script3D->defineAluMaterial("alu");
        $lemn = array("type" => 3,
            "red" => 0.9,
            "green" => 0.7,
            "blue" => 0.4);
        $Script3D->setSimpleMaterialValues($lemn);
        $Script3D->defineSimpleMaterial("lemn");
        $Script3D->operation("MULZ", '1-2*(1-WIDO_REVEAL_SIDE)');
        $Script3D->operation("rotx", -90);
        $operations = "-a/2,WALL_THICKNESS/2,0";
        $Script3D->operation("ADD", $operations);
        $Script3D->operation("MULY", '-(1-2*(1-WIDO_REVEAL_SIDE))');
        $Script3D->operation("ADDY", "y_rama/2");
        $Script3D->addToExpression("GOSUB \"handles\" !!3D HANDLES");
        $Script3D->addToExpression('MATERIAL gs_frame_mat');
        $Script3D->addToExpression('GOSUB "rame"');
        $Script3D->operation("ADDZ", "h1_door");
        $Script3D->operation("mulz", "-1");
        $Script3D->addToExpression('!GOSUB "balama rama"');
        $Script3D->addDelete(2);
        $Script3D->operation("muly", "-1");
        $Script3D->operation("add", "x_rama-offset_pliere,0,x_profil_usa+sill*(x_rama - 0.02+offseth_prag)");
        $Script3D->addToExpression('GOSUB "pliere"');
        $Script3D->addDeleteAll();
        $Script3D->addToExpression('END');
    }

    public function functions3dScript($Script3D)
    {
        $this->hotspots($Script3D);
        $this->balamaRama($Script3D);
        $this->balamaUsaImpar1($Script3D);
        $this->balamaUsaImpar2($Script3D);
        $this->balamaUsa($Script3D);
        $this->balamaUsa2($Script3D);
        $this->pliere($Script3D);
        $this->expresieUsaImpar($Script3D);
        $this->expresieUsaPar($Script3D);
        $this->rame($Script3D);
        $this->ramaStanga($Script3D);
        $this->ramaDreapta($Script3D);
        $this->ramaSus($Script3D);
        $this->prag($Script3D);
        $this->door($Script3D);
        $this->door1($Script3D);
        $this->door2($Script3D);
        $this->geam($Script3D);
        $this->cadruUsa($Script3D);
        $this->ramaStangaPanou($Script3D);
        $this->profilUU($Script3D);
        $this->pragIntermediar($Script3D);
        $this->flushSill($Script3D);
        $this->profilU3($Script3D);
        $this->profilUsa5t($Script3D);
        $this->profilUsa6t($Script3D);
        $this->profilUsa7t($Script3D);
        $Script3D->profilU();
        $this->handle($Script3D);
        $this->handlePart($Script3D);
        $this->clanta($Script3D);
    }

    public function balamaRama($Script3D)
    {
        $Script3D->addToExpression('"balama rama":');
        $Script3D->addToExpression('h_balama=0.05');
        $Script3D->operation("ADD", "0.03 , 0.003 , 0.01");
        $Script3D->operation("ROTZ", "45");
        $Script3D->operation("ADDX", "0.012");
        $Script3D->createCylind("h_balama", 0.018 / 2);
        $Script3D->addDelete(1);

        $Script3D->operation("ADD", "0.012,-0.006/2,0");
        $Script3D->operation("MULX", "-1");
        $Script3D->createBrick(0.04, 0.012, "h_balama");
        $Script3D->addDelete(2);

        $Script3D->operation("ADD", "0.012,0,h_balama/2-0.03/2");
        $Script3D->operation("ROTZ", "u_diag-90");
        $Script3D->operation("ADDy", "-0.006/2");
        $Script3D->createBrick(0.04, 0.012, "0.03");
        $Script3D->addDelete(3);
        $Script3D->addDeleteAll();

        $Script3D->addToExpression("RETURN\n");
    }

    public function balamaUsa($Script3D)
    {
        $Script3D->addToExpression('"balama usa":');
        $Script3D->addToExpression('h_balama=0.05');
        $Script3D->operation("ADD", "0 , -0.011, -x_profil_usa+0.01");
        $Script3D->createCylind("h_balama", 0.018 / 2);
        $Script3D->operation("ROTZ", "55-u_diag");
        $Script3D->operation("ADDy", "0.003");
        $Script3D->operation("MULy", "-1");
        $Script3D->createBrick(0.023, 0.012, "h_balama");
        $Script3D->addDeleteAll(1);
        $Script3D->operation("ADDz", "h_balama/2-0.03/2");
        $Script3D->operation("MULx", "-1");
        $Script3D->operation("ROTZ", "55-u_diag");
        $Script3D->operation("ADDy", "0.003");
        $Script3D->operation("MULy", "-1");
        $Script3D->createBrick(0.023, 0.012, "h_balama");
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function balamaUsa2($Script3D)
    {
        $Script3D->addToExpression('"balama usa2":');
        $Script3D->addToExpression('h_balama=0.05');
        $operation = "-0.018 * sin(u_diag)/2, -y_profil_usa - 0.011 ,-x_profil_usa + 0.01";
        $Script3D->operation("ADD", $operation);
        $Script3D->createCylind("h_balama", 0.018 / 2);
        $Script3D->operation("ADDz", "h_balama/2-0.03/2");
        $Script3D->operation("ROTZ", "(-u_diag*2-2*u_corectie-u_diff)*rot_panou+55");
        $Script3D->operation("ADDy", "0.003");
        $Script3D->operation("MULy", "-1");
        $Script3D->createBrick(0.03, 0.012, "h_balama");
        $Script3D->addDeleteAll(1);
        $Script3D->operation("MULx", "-1");
        $Script3D->operation("ROTZ", "55");
        $Script3D->operation("ADDy", "0.003");
        $Script3D->operation("MULy", "-1");
        $Script3D->createBrick(0.021, 0.012, "h_balama");
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function pliere($Script3D)
    {
        $Script3D->addToExpression('"pliere":');
        $Script3D->addToExpression('l_cadru = x_panou1');
        $Script3D->addToExpression('offsetx_balama = 0');
        $Script3D->addToExpression('offsety_balama = 0');
        $Script3D->addToExpression('kk = 0  !COUNT');
        $Script3D->createForLoop('ll', 0, 'l1_pliat-0.03', 'dx1_pliat');
        $Script3D->addToExpression('kk = kk+1');
        $Script3D->addToExpression('mm = CEIL ( FRA ( kk / 2 ) )  !MIRROR CHECKING');
        $Script3D->operation("ADDx", "ll");

        $Script3D->createIfCondition('mm');
        $Script3D->gosub('expresie usa nivel impar');
        $Script3D->endIfCondition();

        $Script3D->createIfCondition('NOT ( mm )');
        $Script3D->gosub('expresie usa nivel par');
        $Script3D->endIfCondition();

        $Script3D->addDeleteAll(1);
        $Script3D->endForLoop();

        $Script3D->addToExpression("RETURN\n");
    }

    public function expresieUsaImpar($Script3D)
    {
        $Script3D->addToExpression('"expresie usa nivel impar":');

        $Script3D->gosub('balama usa impar1');

        $Script3D->operation("ADDx", "0.018*sin(u_diag)/2");
        $Script3D->operation("rotz", "(-u_diag)*rot_panou");
        $Script3D->operation("rotz", "(-u_corectie-u_diff*sin(u_diag))*rot_panou");
        $Script3D->gosub('door1');
        $Script3D->gosub('balama usa impar2');
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function expresieUsaPar($Script3D)
    {
        $Script3D->addToExpression('"expresie usa nivel par":');

        $Script3D->operation("ADDx", "dx1_pliat-0.018*sin(u_diag)/2");
        $Script3D->operation("mulx", -1);
        $Script3D->operation("rotz", "(-u_diag)*rot_panou");
        $Script3D->operation("rotz", "(-u_corectie-u_diff*sin(u_diag))*rot_panou");
        $Script3D->gosub('door2');
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function rame($Script3D)
    {
        $Script3D->addToExpression('"rame":');
        $Script3D->addToExpression('dim tip_prag[]');
        $array = array('"contur profil Uu"',
            '"prag intermediar"',
            '"flush sill"',
        );
        $Script3D->showArray('tip_prag', $array);
        $Script3D->addToExpression('rama_stanga="contur profil Uu"');
        $Script3D->addToExpression('rama_dreapta="contur profil Uu"');
        $Script3D->addToExpression('rama_sus="contur profil Uu"');
        $Script3D->addToExpression('prag=tip_prag[mm]');

        $Script3D->addToExpression('l_prism=y_rama  !profunzime rama');
        $Script3D->addToExpression('y_prism=x_rama  !latime rama');
        $Script3D->addToExpression('y_prism1=x_rama-0.03');
        $Script3D->addToExpression('g_profil=0.002');
        $Script3D->addToExpression('g_prism=h_door');

        $Script3D->addToExpression('gx_mij=0.045');
        $Script3D->addToExpression('gy_mij=0.015');

        $Script3D->addToExpression('g_prism=h_door');
        $Script3D->addToExpression('h_offset_rama=h_door');
        $Script3D->gosub('rama stanga');
        $Script3D->addToExpression('g_prism=x_door');
        $Script3D->gosub('rama sus');
        $Script3D->addToExpression('g_prism=h_door');
        $Script3D->gosub('rama dreapta');

        $Script3D->createIfCondition('sill=1');
        $Script3D->addToExpression('g_prism=x_door');
        $Script3D->addToExpression('l_prism=l_prag');
        $Script3D->addToExpression('y_prism=h_prag');
        $Script3D->addToExpression('y_prism1=h1_prag');
        $Script3D->addToExpression('gx_mij=gx_mij_prag');
        $Script3D->addToExpression('gy_mij=gy_mij_prag');
        $Script3D->operation("Addz", "offseth_prag-h_perie");
        $Script3D->gosub('prag');
        $Script3D->addDelete();
        $Script3D->endIfCondition();

        $Script3D->addToExpression("RETURN\n");
    }

    public function ramaStanga($Script3D)
    {
        $Script3D->addToExpression('"rama stanga":');
        $Script3D->operation("Addz", "g_prism");
        $Script3D->operation("ROTy", 45);
        $Script3D->addCutplane();
        $Script3D->addDeleteAll();
        $Script3D->operation("ROTz", -90);
        $Script3D->gosub('rama_stanga !!contur rama_stanga', "");
        $Script3D->addToExpression('prism_ nsp/3,g_prism,get(nsp)');
        $Script3D->addDeleteAll();
        $Script3D->cutendAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function ramaDreapta($Script3D)
    {
        $Script3D->addToExpression('"rama dreapta":');
        $Script3D->operation("Addx", "x_door");
        $Script3D->operation("mulx", -1);
        $Script3D->gosub('rama stanga');
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function ramaSus($Script3D)
    {
        $Script3D->addToExpression('"rama sus":');
        $Script3D->operation("Addz", "h_offset_rama");
        $Script3D->operation("ROTy", 45);
        $Script3D->addCutplane(180);
        $Script3D->addDelete(1);
        $Script3D->operation("ADDX", 'g_prism');
        $Script3D->operation("mulx", -1);
        $Script3D->operation("ROTy", 45);
        $Script3D->addCutplane(180);
        $Script3D->addDeleteAll(1);
        $Script3D->operation("ROTy", 90);
        $Script3D->operation("ROTz", -90);
        $Script3D->gosub('rama_sus !!contur rama_sus', "");
        $Script3D->addToExpression('prism_ nsp/3,g_prism,get(nsp)');
        $Script3D->addDeleteAll();
        $Script3D->cutendAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function prag($Script3D)
    {
        $Script3D->addToExpression('"prag":');
        $Script3D->operation("ROTy", -45);
        $Script3D->createIfCondition('mm=1');
        $Script3D->addCutplane();
        $Script3D->endIfCondition();
        $Script3D->addDelete(1);
        $Script3D->operation("ADDX", 'g_prism');
        $Script3D->operation("mulx", -1);
        $Script3D->operation("ROTy", -45);
        $Script3D->createIfCondition('mm=1');
        $Script3D->addCutplane();
        $Script3D->endIfCondition();
        $Script3D->addDeleteAll();
        $Script3D->operation("muly", -1);
        $Script3D->operation("ROTy", 90);
        $Script3D->operation("ROTz", 90);
        $Script3D->gosub('prag !!contur prag', "");
        $Script3D->addToExpression('prism_ nsp/3,g_prism,get(nsp)');
        $Script3D->addDeleteAll();
        $Script3D->createIfCondition('mm=1');
        $Script3D->cutendAll();
        $Script3D->endIfCondition();
        $Script3D->addToExpression("RETURN\n");
    }

    public function door1($Script3D)
    {
        $Script3D->addToExpression('"door1":');
        $Script3D->addToExpression('l_usa = l_cadru');
        $Script3D->addToExpression('tip_profil="profil usa5 t"');
        $Script3D->addToExpression('tip_profil2="profil usa6 t"');
        $Script3D->addToExpression('offset_profil2=0');
        $Script3D->createIfCondition('kk=n1');
        $Script3D->addToExpression('offset_profil2=0.04');
        $Script3D->addToExpression('l_usa = l_cadru - offset_profil2-0.013');
        $Script3D->addToExpression('tip_profil2="profil usa7 t"');
        $Script3D->endIfCondition();
        $Script3D->operation("ADDy", 'y_profil_usa');
        $Script3D->operation("MULy", -1);
        $Script3D->gosub('door');
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function door2($Script3D)
    {
        $Script3D->addToExpression('"door2":');
        $Script3D->addToExpression('l_usa = l_cadru');
        $Script3D->addToExpression('tip_profil="profil usa5 t"');
        $Script3D->addToExpression('tip_profil2="profil usa6 t"');
        $Script3D->addToExpression('offset_profil2=0');
        $Script3D->operation("ADDx", "x_panou1");
        $Script3D->operation("MULX", -1);

        $Script3D->createIfCondition('kk=n1');
        $Script3D->addToExpression('offset_profil2=0.04');
        $Script3D->addToExpression('l_usa = l_cadru - offset_profil2-0.013');
        $Script3D->addToExpression('tip_profil2="profil usa7 t"');
        $Script3D->endIfCondition();
        $Script3D->operation("ADDy", 'y_profil_usa');
        $Script3D->operation("MULy", -1);
        $Script3D->gosub('door');
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function geam($Script3D)
    {
        $Script3D->addToExpression('"geam":');
        $Script3D->addToExpression('MATERIAL mat_geam');
        $Script3D->operation("ADD", "x_profil_usa-0.015,g_geam/2-0.0185,-0.03/2");
        $Script3D->createBrick('x_glas+0.03', 'g_geam', 'h_glas');
        $Script3D->operation("ADDy", '0.016');
        $Script3D->operation("MULy", -1);
        $Script3D->createBrick('x_glas+0.03', 'g_geam', 'h_glas');
        $Script3D->addDeleteAll();
        $Script3D->addToExpression('MATERIAL gs_frame_mat');

        $Script3D->addToExpression("RETURN\n");
    }

    public function cadruUsa($Script3D)
    {
        $Script3D->addToExpression('"cadru usa":');
        $Script3D->addToExpression('g_prism=h_cadru');
        $Script3D->operation("ADDx", "y_prism");
        $Script3D->operation("ROTZ", 90);
        $Script3D->gosub('tip_profil', "");
        $Script3D->addToExpression('n = nsp/3');
        $parameters = explode(",", '0,-l_usa+2*y_prism,h_cadru/2-y_prism,0,
		0,-l_usa+2*y_prism,h_cadru-y_prism,0,
		0,0,h_cadru-y_prism,0,
		0,0,0,0,
		0,-l_usa+2*y_prism,0,0,
		0,-l_usa+2*y_prism,h_cadru/2-y_prism,0');
        $Script3D->putParameters($parameters, 4);
        $Script3D->addToExpression('m = ( nsp - n * 3 ) / 4 ');
        $Script3D->tube();

        $list = array(
            'y_prism' => 'y_profil_usa+offset_profil2',
            'rama_panou_stanga' => 'tip_profil2',
            'h_prism' => 'h_cadru+y_profil_usa',
        );
        $Script3D->addParameters($list);
        $Script3D->operation("ADD", "0,-l_usa+2*y_profil_usa,-y_profil_usa");
        $Script3D->operation("MULY", -1);
        $Script3D->gosub('rama stanga panou');
        $Script3D->addDeleteAll();

        $Script3D->addToExpression("RETURN\n");
    }

    public function door($Script3D)
    {
        $Script3D->addToExpression('"door":');
        $list = array(
            'l1_prism' => -0.4,
            'l2_prism' => 0.3,
            'x_r_door' => 0.1,
            'g_door' => 0.022,
            'y1_prism' => 0.2,
            'y2_prism' => 0.1,
            'l_prism' => 'x_profil_usa',
            'g_profil' => 0.002,
            'y_prism' => 'y_profil_usa',
            'h_cadru' => 'h1_door',
            'offh1' => 0.03,
            'gx_mij' => 0.012,
            'gy_mij' => 0.015,
            'gx2_mij' => 0.008,
            'gy2_mij' => 0.022,
        );

        $Script3D->addParameters($list);
        $Script3D->addToExpression('MATERIAL gs_frame_mat');

        $Script3D->operation("ADDy", "g_usa/2");
        $Script3D->gosub('cadru usa');

        $list2 = array(
            'x_glas' => 'l_usa-2*x_profil_usa',
            'h_glas' => 'h_cadru-x_profil_usa+0.03',
            'g_geam' => 0.003,
        );
        $Script3D->addParameters($list2);
        $Script3D->gosub('geam');
        $Script3D->addDeleteAll();
        $Script3D->createIfCondition('kk=n1');
        $Script3D->operation("ADD", "dx1_usa,0,offh_handle");
        $Script3D->operation("MULX", -1);
        $Script3D->gosub('handle');
        $Script3D->addDeleteAll(1); //IF CONDITION
        $Script3D->endIfCondition();
        $Script3D->addToExpression("RETURN\n");
    }

    public function ramaStangaPanou($Script3D)
    {
        $Script3D->addToExpression('"rama stanga panou":');
        $Script3D->operation("ADDY", "y_profil_usa");
        $Script3D->operation("ROTz", -90);
        $Script3D->operation("ROTy", -45);
        $Script3D->addCutplane(180);
        $Script3D->addDelete(1);
        $Script3D->operation("ADDZ", 'h_prism');
        $Script3D->operation("ROTy", 45);
        $Script3D->addCutplane();
        $Script3D->addDeleteAll();
        $Script3D->gosub('rama_panou_stanga !!contur rama_stanga', "");
        $Script3D->addToExpression('prism_ nsp/3,h_prism,get(nsp)');
        $Script3D->cutendAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function profilUU($Script3D)
    {
        $Script3D->addToExpression('"contur profil Uu":');
        $parameters = explode(",", '0,0,15,  
		l_prism,0,15,
		l_prism,y_prism1-g_profil,15,
		l_prism+0.01,y_prism1-g_profil,15,
		l_prism+0.01,y_prism,15,
		l_prism,y_prism,15,
		l_prism,y_prism-g_profil,15,
		l_prism+0.01-g_profil,y_prism-g_profil,15,
		l_prism+0.01-g_profil,y_prism1,15, 
		l_prism/2+gx_mij/2,y_prism1,15,
		l_prism/2+gx_mij/2,y_prism1+gy_mij,15,
		l_prism/2-gx_mij/2,y_prism1+gy_mij,15,
		l_prism/2-gx_mij/2,y_prism1,15,
		0,y_prism1,15');
        $Script3D->putParameters($parameters);
        $Script3D->addToExpression("RETURN\n");
    }

    public function pragIntermediar($Script3D)
    {
        $Script3D->addToExpression('"prag intermediar":');
        $parameters = explode(",", '0,0,15,
                l_prism,0,15,
                l_prism,y_prism-g_profil-0.02,15,
                l_prism-0.005/2,y_prism-g_profil-0.02,15,
                l_prism-0.005/2,y_prism-g_profil,15,
                l_prism+0.01,y_prism-g_profil,15,
                l_prism+offset_prag,y_prism1,15,
                l_prism+offset_prag+g_profil,y_prism1,15,
                l_prism+0.012,y_prism,15,
                l_prism/2+gx_mij/2,y_prism,15,
                l_prism/2+gx_mij/2,y_prism-gy_mij,15,
                l_prism/2-gx_mij/2,y_prism-gy_mij,15,
                l_prism/2-gx_mij/2,y_prism,15,
                l_prism/2-gx_mij/2-0.01,y_prism,15,
                l_prism/2-gx_mij/2-0.01,y_prism1,15,
                0,y_prism1,15');
        $Script3D->putParameters($parameters);
        $Script3D->addToExpression("RETURN\n");
    }

    public function flushSill($Script3D)
    {
        $Script3D->addToExpression('"flush sill":');
        $parameters = explode(",", '0,0,15,
                l_prism,0,15,
                l_prism,y_prism-g_profil,15,
                l_prism+offset_prag,y_prism-g_profil,15,
                l_prism+offset_prag,y_prism,15,
                l_prism/2+gx_mij/2,y_prism,15,
                l_prism/2+gx_mij/2,y_prism-gy_mij,15,
                l_prism/2-gx_mij/2,y_prism-gy_mij,15,
                l_prism/2-gx_mij/2,y_prism,15,
                -offset_prag,y_prism,15,
                -offset_prag,y_prism-g_profil,15,
                0,y_prism-g_profil,15'
        );
        $Script3D->putParameters($parameters);
        $Script3D->addToExpression("RETURN\n");
    }

    public function profilU3($Script3D)
    {
        $Script3D->addToExpression('"contur profil U3":');
        $parameters = explode(",", '0,0,15,  
		l_prism,0,15, 
		l_prism,y_prism,15,
		l_prism-offx1,y_prism,15,
		l_prism-offx1,y_prism-g_profil,15,
		l_prism-g_profil,y_prism-g_profil,15,
		l_prism-g_profil,g_profil,15,
		l_prism/2+gx_mij/2,g_profil,15,
		l_prism/2+gx_mij/2,gy_mij,15,
		l_prism/2-gx_mij/2,gy_mij,15,
		l_prism/2-gx_mij/2,g_profil,15,
		g_profil,g_profil,15,
		g_profil,y_prism-g_profil,15,
		offx1,y_prism-g_profil,15,
		offx1,y_prism,15,
		0,y_prism,15');
        $Script3D->putParameters($parameters);
        $Script3D->addToExpression("RETURN\n");
    }

    public function profilUsa5t($Script3D)
    {
        $Script3D->addToExpression('"profil usa5 t":');
        $parameters = explode(",", '-l_prism/2,0,15,
		-l_prism/2+0.026,0,15,
		-l_prism/2+0.026,0.015,15,
		l_prism/2-0.008,0.015,15,
		l_prism/2-0.008,0,15,
		l_prism/2,0,15,
		l_prism/2,y_prism,15,
		l_prism/2-0.006,y_prism,15,
		l_prism/2-0.006,y_prism-0.02,15,
		-l_prism/2+0.006,y_prism-0.02,15,
		-l_prism/2+0.006,y_prism,15,
		-l_prism/2,y_prism,15');
        $Script3D->putParameters($parameters);
        $Script3D->addToExpression("RETURN\n");
    }

    public function profilUsa6t($Script3D)
    {
        $Script3D->addToExpression('"profil usa6 t":');
        $parameters = explode(",", 'l_prism/2,0,15,
		l_prism/2-0.026,0,15,
		l_prism/2-0.026,0.015,15,
		-(l_prism/2-0.008),0.015,15,
		-(l_prism/2-0.008),0,15,
		-l_prism/2,0,15,
		-l_prism/2,y_prism,15,
		-0.03/2,y_prism,15,
		-0.03/2,y_prism+0.015,15,
		0.03/2,y_prism+0.015,15,
		0.03/2,y_prism,15,
		l_prism/2,y_prism,15');
        $Script3D->putParameters($parameters);
        $Script3D->addToExpression("RETURN\n");
    }

    public function profilUsa7t($Script3D)
    {
        $Script3D->addToExpression('"profil usa7 t":');
        $parameters = explode(",", 'l_prism/2,0,15,
		l_prism/2-0.008,0,15,
		l_prism/2-0.008,0.015,15,
		-l_prism/2+0.026,0.015,15,
		-l_prism/2+0.026,0,15,
		-l_prism/2,0,15,
		-l_prism/2,y_prism-0.02,15,
		-l_prism/2+0.005,y_prism-0.02,15,
		-l_prism/2+0.005,y_prism-0.03,15,
		-l_prism/2+0.006,y_prism-0.03,15,
		l_prism/2-0.006,y_prism-0.03,15,
		l_prism/2-0.006,y_prism,15,
		l_prism/2,y_prism,15');
        $Script3D->putParameters($parameters);
        $Script3D->addToExpression("RETURN\n");
    }

    public function balamaUsaImpar1($Script3D)
    {
        $Script3D->addToExpression('"balama usa impar1":');
        $Script3D->createIfCondition('kk>1 AND kk<=n1');
        $Script3D->gosub('balama usa');
        $Script3D->operation("ADDz", "h1_door-x_profil_usa");
        $Script3D->operation("MULz", "-1");
        $Script3D->gosub('balama usa');
        $Script3D->addDeleteAll(1);
        $Script3D->endIfCondition();
        $Script3D->addToExpression("RETURN\n");
    }

    public function balamaUsaImpar2($Script3D)
    {
        $Script3D->addToExpression('"balama usa impar2":');
        $Script3D->createIfCondition('kk<n1');
        $Script3D->operation("ADDx", "dx1_usa");
        $Script3D->operation("MULy", "-1");
        $Script3D->gosub('balama usa2');
        $Script3D->operation("ADDz", "h1_door-x_profil_usa");
        $Script3D->operation("MULz", "-1");
        $Script3D->gosub('balama usa2');
        $Script3D->addDeleteAll(1);
        $Script3D->endIfCondition();
        $Script3D->addToExpression("RETURN\n");
    }

    public function hotspots($Script3D)
    {
        $Script3D->addToExpression('"handles":');
        $Script3D->operation("ADDX", "x_rama");
        $Script3D->createMobileHotspot("l1_pliat");
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function handle($Script3D)
    {
        $Script3D->addToExpression('"handle":');
        $list = array(
            'l_clanta' => -0.02,
            'g1_clanta' => 0.02,
            'y_clanta' => 0.02,
            'h_clanta' => 0.02,
            'g2_clanta' => 0.005,
        );

        $Script3D->addParameters($list);
        $Script3D->addToExpression('MATERIAL gs_frame_mat');

        $Script3D->operation("ADD", "y_profil_usa/2+0.03,x_profil_usa,0");
        $Script3D->gosub('handle_part');
        $Script3D->operation("ADDy", "-x_profil_usa");
        $Script3D->operation("MULy", -1);
        $Script3D->gosub('handle_part');
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function handlePart($Script3D)
    {
        $Script3D->addToExpression('"handle_part":');

        $Script3D->operation("ADDx", -0.03 / 2);
        $Script3D->createBrick(0.03, 0.005, 0.16);
        $Script3D->addDelete();
        $Script3D->operation("ADD", '-g1_clanta/2,0.005,0.09');
        $Script3D->gosub('clanta');
        $Script3D->addDelete();
        $Script3D->operation("ADD", '0,0.005,0.02');
        $Script3D->operation("ROTX", -90);
        $Script3D->createCylind(0.002, 0.01 / 2);
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

    public function clanta($Script3D)
    {
        $Script3D->addToExpression('"clanta":');
        $Script3D->createBrick('g1_clanta', 0.006, 'h_clanta');
        $Script3D->operation("ADDy", 0.006);
        $parameters = explode(",", '0,0,15,
			g1_clanta,0,15,
			g1_clanta,y_clanta-g2_clanta,15,
			l_clanta,y_clanta-g2_clanta/3,15,
			l_clanta,y_clanta,15,
			0.002,y_clanta,15');
        $Script3D->putParameters($parameters);
        $Script3D->prism('h_clanta');
        $Script3D->addDeleteAll();
        $Script3D->addToExpression("RETURN\n");
    }

}
