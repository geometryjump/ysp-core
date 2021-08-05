<?php
include __DIR__ . "/mainLib.php";
$gs = new mainLib();
$accountID = 3200;
echo $gs->getExtID(10046);
/*$cs = $db->prepare("SELECT color_id FROM rcolorused WHERE acc = :accountID");
		$cs->execute([':accountID' => $accountID]);
		$data = $cs->fetchColumn();
		if ($data < 1) {
			echo -1;
        }
        $colorid = "";
        $colorids = $cs->fetch();
        $colorid = $colorids['color_id'];
        $colorid = 1000;
        //$colorid = $colorids['color_id'];
        echo $colorid."<hr/>";
		$query = $db->prepare("SELECT * FROM rcolors WHERE color_id LIKE :colorid");
        $query->execute([':colorid' => $colorid]);
        $data = $query->fetchColumn();
		if ($data < 1) {
			echo -2;
		}
        $role = $query->fetchAll();
        $color = "-1";
        $colorr = "";
        $colorg = "";
        $colorb = "";
        foreach($role as &$s) {
            if($colorr == "") { $colorr = $s['color_r'];}
            if($colorg == "") { $colorg = $s['color_g'];}
            if($colorb == "") { $colorb = $s['color_b'];}
        }
        $color = $colorr.",".$colorg.",".$colorb."";
		echo $color;*/
?>