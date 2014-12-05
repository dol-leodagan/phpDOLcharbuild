<?php
/**********************************
PHP Character Builder for DOL Revision 3375
Using mostly no dependencies to display skills
based on current content of your database.
Author : Kyp Durron <kyp@durron.net> (Leodagan)
***********************************/

	class CharacterBuilder
	{
		/***
		CONFIGURE DATABASE HERE
		***/
		const SQLITE3_FILE = 'dol.sqlite3.db';
		const MYSQL_DSN = 'host=localhost;dbname=livedol';
		const MYSQL_USR = 'root';
		const MYSQL_PW = '';
	
		private $ClassName;
		private $ClassID;
		private $dbo;
		
		/// <summary>
		/// Holds all character classes
		/// </summary>
		private $eCharacterClass;
		
		private $eStylesIconID;
		private $eSpellsIconID;
	
		function __construct()
		{
			$temp = str_getcsv(file_get_contents('styles.csv'), "\n"); //parse the rows
			foreach($temp as &$Row)
			{
				$Row = str_getcsv($Row, "\t"); //parse the items in rows
				$this->eStylesIconID[$Row[0]] = $Row[2];
			}
			
			$temp = str_getcsv(file_get_contents('spells.csv'), "\n"); //parse the rows
			foreach($temp as &$Row)
			{
				$Row = str_getcsv($Row, "\t"); //parse the items in rows
				$this->eSpellsIconID[$Row[0]] = $Row[2];
			}
			
			if (file_exists($this::SQLITE3_FILE))
			{
				$this->dbo = new PDO('sqlite:'.$this::SQLITE3_FILE);
				// SQLITE ONLY
				$this->dbo->exec("pragma synchronous = 0;");
				$this->dbo->exec("PRAGMA automatic_index = 1;");
				$this->dbo->exec("PRAGMA journal_mode = WAL;");
				$this->dbo->exec("PRAGMA cache_size = 65536;");
			}
			else
				$this->dbo = new PDO('mysql:'.$this::MYSQL_DSN, $this::MYSQL_USR, $this::MYSQL_PW);
		
			$this->eCharacterClass = array();
			//alb classes
			$this->eCharacterClass[2] = array('Name' => 'Armsman', 'Realm' => 'Albion');
			$this->eCharacterClass[13] = array('Name' => 'Cabalist', 'Realm' => 'Albion');
			$this->eCharacterClass[6] = array('Name' => 'Cleric', 'Realm' => 'Albion');
			$this->eCharacterClass[10] = array('Name' => 'Friar', 'Realm' => 'Albion');
			$this->eCharacterClass[33] = array('Name' => 'Heretic', 'Realm' => 'Albion');
			$this->eCharacterClass[9] = array('Name' => 'Infiltrator', 'Realm' => 'Albion');
			$this->eCharacterClass[11] = array('Name' => 'Mercenary', 'Realm' => 'Albion');
			$this->eCharacterClass[4] = array('Name' => 'Minstrel', 'Realm' => 'Albion');
			$this->eCharacterClass[12] = array('Name' => 'Necromancer', 'Realm' => 'Albion');
			$this->eCharacterClass[1] = array('Name' => 'Paladin', 'Realm' => 'Albion');
			$this->eCharacterClass[19] = array('Name' => 'Reaver', 'Realm' => 'Albion');
			$this->eCharacterClass[3] = array('Name' => 'Scout', 'Realm' => 'Albion');
			$this->eCharacterClass[8] = array('Name' => 'Sorcerer', 'Realm' => 'Albion');
			$this->eCharacterClass[5] = array('Name' => 'Theurgist', 'Realm' => 'Albion');
			$this->eCharacterClass[7] = array('Name' => 'Wizard', 'Realm' => 'Albion');
			$this->eCharacterClass[60] = array('Name' => 'Mauler', 'Realm' => 'Albion');

			//mid classes
			$this->eCharacterClass[31] = array('Name' => 'Berserker', 'Realm' => 'Midgard');
			$this->eCharacterClass[30] = array('Name' => 'Bonedancer', 'Realm' => 'Midgard');
			$this->eCharacterClass[26] = array('Name' => 'Healer', 'Realm' => 'Midgard');
			$this->eCharacterClass[25] = array('Name' => 'Hunter', 'Realm' => 'Midgard');
			$this->eCharacterClass[29] = array('Name' => 'Runemaster', 'Realm' => 'Midgard');
			$this->eCharacterClass[32] = array('Name' => 'Savage', 'Realm' => 'Midgard');
			$this->eCharacterClass[23] = array('Name' => 'Shadowblade', 'Realm' => 'Midgard');
			$this->eCharacterClass[28] = array('Name' => 'Shaman', 'Realm' => 'Midgard');
			$this->eCharacterClass[24] = array('Name' => 'Skald', 'Realm' => 'Midgard');
			$this->eCharacterClass[27] = array('Name' => 'Spiritmaster', 'Realm' => 'Midgard');
			$this->eCharacterClass[21] = array('Name' => 'Thane', 'Realm' => 'Midgard');
			$this->eCharacterClass[34] = array('Name' => 'Valkyrie', 'Realm' => 'Midgard');
			$this->eCharacterClass[59] = array('Name' => 'Warlock', 'Realm' => 'Midgard');
			$this->eCharacterClass[22] = array('Name' => 'Warrior', 'Realm' => 'Midgard');
			$this->eCharacterClass[61] = array('Name' => 'Mauler', 'Realm' => 'Midgard');

			//hib classes
			$this->eCharacterClass[55] = array('Name' => 'Animist', 'Realm' => 'Hibernia');
			$this->eCharacterClass[39] = array('Name' => 'Bainshee', 'Realm' => 'Hibernia');
			$this->eCharacterClass[48] = array('Name' => 'Bard', 'Realm' => 'Hibernia');
			$this->eCharacterClass[43] = array('Name' => 'Blademaster', 'Realm' => 'Hibernia');
			$this->eCharacterClass[45] = array('Name' => 'Champion', 'Realm' => 'Hibernia');
			$this->eCharacterClass[47] = array('Name' => 'Druid', 'Realm' => 'Hibernia');
			$this->eCharacterClass[40] = array('Name' => 'Eldritch', 'Realm' => 'Hibernia');
			$this->eCharacterClass[41] = array('Name' => 'Enchanter', 'Realm' => 'Hibernia');
			$this->eCharacterClass[44] = array('Name' => 'Hero', 'Realm' => 'Hibernia');
			$this->eCharacterClass[42] = array('Name' => 'Mentalist', 'Realm' => 'Hibernia');
			$this->eCharacterClass[49] = array('Name' => 'Nightshade', 'Realm' => 'Hibernia');
			$this->eCharacterClass[50] = array('Name' => 'Ranger', 'Realm' => 'Hibernia');
			$this->eCharacterClass[56] = array('Name' => 'Valewalker', 'Realm' => 'Hibernia');
			$this->eCharacterClass[58] = array('Name' => 'Vampiir', 'Realm' => 'Hibernia');
			$this->eCharacterClass[46] = array('Name' => 'Warden', 'Realm' => 'Hibernia');
			$this->eCharacterClass[62] = array('Name' => 'Mauler', 'Realm' => 'Hibernia');

		
			$this->ClassName = 'Choose your Class !';
			$this->ClassID = 0;
			$this->SetClass();
		}
		
		function Main()
		{
			$this->Header();
			
			if ($this->ClassID == 0)
				$this->DisplayClassChoose();
			else
				$this->DisplaySkills();
				
			$this->Footer();
		}
		
		function Header()
		{
			$share = '';
			if ($this->ClassID != 0)
			{
				$share = '<span class="charbuildShare">Share this spec : <span>http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'</span></span>';
			}
		
			echo '<div class="charbuildHeader"><span>Character Builder - '.$this->ClassName.$share.'</span></div>';
		}
		
		function Footer()
		{
			echo '<div class="charbuildFooter"><span>Project by <a href="https://daoc.freyad.net">Leodagan</a></span></div>';
		}
		
		function SetClass()
		{
			if (isset($_REQUEST['c']) && $_REQUEST['c'] > 0)
				$this->ClassID = $_REQUEST['c'];
				
			if (array_key_exists($this->ClassID, $this->eCharacterClass))
				$this->ClassName = $this->eCharacterClass[$this->ClassID]['Name'];
			else
				$this->ClassID = 0;
		}
		
		function DisplayClassChoose()
		{
			$classByRealm = array();
			foreach ($this->eCharacterClass as $key=>$value)
			{
				$classByRealm[$value['Realm']][] = $key;
			}
			
			$chooser = '';
			
			foreach ($classByRealm as $realm=>$classes)
			{
				$charclasses = '';
				
				foreach($classes as $k=>$id)
				{
					$charclasses .= '<span id="charclass_'.$id.'"><a href="?c='.$id.'"><img src="img/'.$this->eCharacterClass[$id]['Name'].'.png" />'.$this->eCharacterClass[$id]['Name'].'</a></span>';
				}
				
				$chooser .= '<div class="charbuildChooserRealm"><span id="charclass_albion"><img src="img/'.$realm.'.png" />'.$realm.'</span>'.$charclasses.'</div>'; 
			}
			
			echo '<div class="charbuildChooser">'.$chooser.'</div>';
			
		}
		
		function DisplaySkills()
		{
			$content = '';
			
			// Retrieve Specializations
			$spec_sql = 'SELECT *
							FROM `classxspecialization`
							LEFT JOIN `specialization` ON `classxspecialization`.`SpecKeyName` = `specialization`.`KeyName`
							WHERE `ClassID`
							IN ( :classid, 0 )
							OR `ClassID` IS NULL
							ORDER BY `LevelAcquired` ASC';
			
			$spec_stmt = $this->dbo->prepare($spec_sql);
			//print_r($this->dbo->errorInfo());
			$spec_stmt->execute(array(':classid' => $this->ClassID));
			$spec_results = $spec_stmt->fetchAll();
			
			foreach($spec_results as $key=>$result)
			{
				$content .= '<div class="charbuildSpec"><span>'.$result['Name'].' ('.$result['KeyName'].'), acquired at level : '.($result['LevelAcquired'] < 1 ? 1 : $result['LevelAcquired']).'<p>'.($result['Description'] == 'no' ? '' : $result['Description']).'</p></span>'.$this->DisplaySpec($result['SpecializationID']).'</div>';
			}
		
			echo '<div class="charbuildDisplay">'.$content.'</div>';
		}
		
		function DisplaySpec($id)
		{
			$total = '';
			
			$total .= $this->DisplayAbilities($id);
			$total .= $this->DisplaySpells($id);
			$total .= $this->DisplayStyles($id);
			
			return $total;
		}
		
		function DisplayStyles($specid)
		{
			$styles = '';
			
			$style_sql = 'SELECT *
							FROM `specialization`
							LEFT JOIN `style` ON `specialization`.`KeyName` = `style`.`SpecKeyName`
							WHERE `specializationID` = :specid
							AND (
							`style`.`ClassId` = :classid
							OR `style`.`ClassId` = 0
							)
							ORDER BY `style`.`SpecLevelRequirement` ASC
							';

			$style_stmt = $this->dbo->prepare($style_sql);
			$style_stmt->execute(array(':specid' => $specid, ':classid' => $this->ClassID));
			$style_results = $style_stmt->fetchAll();
			
			$previousLevel = 0;
			foreach($style_results as $key => $result)
			{
				if ($result['Implementation'] == 'DOL.GS.LiveMasterLevelsSpecialization')
					$iconid = array_key_exists($result['Icon'], $this->eSpellsIconID) ? $this->eSpellsIconID[$result['Icon']] : 0;
				else
					$iconid = array_key_exists($result['Icon'], $this->eStylesIconID) ? $this->eStylesIconID[$result['Icon']] : 0;
				
				$filldivs = '';
				for ($i = 1 ; $i < ($result['SpecLevelRequirement']-$previousLevel) ; $i++)
					$filldivs .= '<div class="charbuildDisplayStyleEmpty"></div>';
					
				$previousLevel = $result['SpecLevelRequirement'];
				$styles .= $filldivs.'<div class="charbuildDisplayStyle"><span class="SkillLevel">'.$result['SpecLevelRequirement'].'</span><img src="png/'.$iconid.'.png" alt="'.$result['Name'].'" title="'.$result['Name'].'"/></div>';
			}
			
			if(strlen($styles))
			{
				return '<div class="charbuildStyles"><span>Styles</span>'.$styles.'</div>';
			}
			
			return '';
		}
		
		function DisplayAbilities($specid)
		{
			$abilities = '';
			
			$ability_sql = 'SELECT `specialization`.`Implementation` as specImpl, `specxability`.*, `ability`.* FROM `specialization` 
								LEFT JOIN `specxability` ON `specialization`.`KeyName` = `specxability`.`Spec` 
								LEFT JOIN `ability` ON `specxability`.`AbilityKey` = `ability`.`KeyName` 
								WHERE `specializationID` = :specid 
								AND (`specxability`.`ClassId` = :classid OR `specxability`.`ClassId` = 0)
								ORDER BY `specxability`.`AbilityKey`, `specxability`.`SpecLevel` ASC';
								
			$ability_stmt = $this->dbo->prepare($ability_sql);
			$ability_stmt->execute(array(':specid' => $specid, ':classid' => $this->ClassID));
			$ability_results = $ability_stmt->fetchAll();
			
			$previousAbility = '';
			$previousLevel = 0;
			foreach($ability_results as $key => $result)
			{
				// Table for each Ability Type...
				if ($result['KeyName'] != $previousAbility)
				{
					if (!strlen($previousAbility))
					{
						$abilities .= '<table>';
					}
					else
					{
						$abilities .= '</td></tr>';
					}
					
					$previousAbility = strlen($result['KeyName']) ? $result['KeyName'] : 'Missing Ability';
					
					$abilities .= '<tr><td>'.$result['KeyName'].'</td><td>';
					$previousLevel = 0;
				}
			
				$iconid = $result['IconID'];
				if ($result['specImpl'] == 'DOL.GS.LiveMasterLevelsSpecialization')
					$iconid = array_key_exists($result['IconID'], $this->eSpellsIconID) ? $this->eSpellsIconID[$result['IconID']] : 0;
				
				$filldivs = '';
				for ($i = 1 ; $i < ($result['SpecLevel']-$previousLevel) ; $i++)
					$filldivs .= '<div class="charbuildDisplayAbilityEmpty"></div>';
					
				$previousLevel = $result['SpecLevel'];
				$abilityName = $result['Name'];
				if (strpos($abilityName, ';') > 0)
				{
					$arrayName = explode(';', $abilityName);
					$arrayAbilities = array();
					foreach($arrayName as $kv=>$val)
					{
						$arrayValue = explode('|', $val);
						$arrayAbilities[$arrayValue[0]] = $arrayValue[1];
					}
						
					$abilityName = $arrayAbilities[$result['AbilityLevel']];
				}
				
				$abilityName = str_replace('%n', $result['AbilityLevel'], $abilityName);
				
				$abilities .= $filldivs.'<div class="charbuildDisplayAbility"><span class="SkillLevel">'.$result['SpecLevel'].'</span><img src="png/'.$iconid.'.png" alt="'.$abilityName.'" title="'.$abilityName.'"/></div>';
			}
			
			if(strlen($abilities))
			{
				return '<div class="charbuildAbilities"><span>Abilities</span>'.$abilities.'</td></tr></table></div>';
			}
			
			return '';
		}
		
		function DisplaySpells($specid)
		{
			$spelllines = '';
			// Gather Spell Lines
			$lines_sql = 'SELECT `spellline`.*
							FROM `specialization`
							JOIN `spellline` ON `specialization`.`KeyName` = `spellline`.`Spec`
							JOIN (SELECT COUNT(`SpellID`) as cnt, `LineName` FROM `linexspell` GROUP BY `LineName`) spcount ON `spellline`.`KeyName` = spcount.`LineName`
							WHERE `specializationID` = :specid
							AND (
							`spellline`.`ClassIDHint` = :classid
							OR `spellline`.`ClassIDHint` = 0
                                                        OR `spellline`.`ClassIDHint` IS NULL
							)
							ORDER BY `spellline`.`IsBaseLine` DESC, `spellline`.`ClassIDHint` DESC';
			
			$lines_stmt = $this->dbo->prepare($lines_sql);
			$lines_stmt->execute(array(':specid' => $specid, ':classid' => $this->ClassID));
			$lines_results = $lines_stmt->fetchAll();
			
			$linearray = array();
			
			$baselineclass = -1;
			$speclineclass = -1;
			foreach($lines_results as $key=>$result)
			{
				// Tell appart class specific lines for Base and Spec
				if ($result['IsBaseLine'] == 0 && $speclineclass == -1)
					$speclineclass = $result['ClassIDHint'] > 0 ? $result['ClassIDHint'] : 0;
					
				if ($result['IsBaseLine'] == 0 && $speclineclass != ($result['ClassIDHint'] > 0 ? $result['ClassIDHint'] : 0))
					continue;
					
				if ($result['IsBaseLine'] != 0 && $baselineclass == -1)
					$baselineclass = $result['ClassIDHint'] > 0 ? $result['ClassIDHint'] : 0;
					
				if ($result['IsBaseLine'] != 0 && $baselineclass != ($result['ClassIDHint'] > 0 ? $result['ClassIDHint'] : 0))
					continue;
				
				$spelllines .= '<div class="charbuildDisplayLines"><span>'.$result['Name'].' '.($result['IsBaseLine'] == 0 ? '(Specline)' : '(Baseline)').'</span>'.$this->GetSpellsForLine($result['KeyName']).'</div>';
			}
			
			if (strlen($spelllines))
			{
				return '<div class="charbuildSpells"><span>Spells</span>'.$spelllines.'</div>';
			}
			
			return $spelllines;
		}
		
		function GetSpellsForLine($lineKeyName)
		{
			$spells_sql = 'SELECT * 
			FROM `linexspell` 
			JOIN 
				(SELECT `spell`.SpellID as `realid`, CASE `spell`.`Type` WHEN "PetSpell" THEN `spell`.`SubSpellID` ELSE `spell`.`SpellID` END as `linkid` from Spell) as `lnktable` 
			ON `linexspell`.`SpellID` = `lnktable`.`realid` 
			JOIN `spell` 
			ON `lnktable`.`linkid` = `spell`.`SpellID`
			WHERE `linexspell`.`LineName` LIKE :lineName AND (`spell`.`SpellGroup` IS NULL OR `spell`.`SpellGroup` = 0)
			ORDER BY `Type`, `Target`, CASE `Radius` WHEN `Radius` > 0 THEN 1 ELSE 0 END, CASE `CastTime` WHEN `CastTime` <= 0 THEN 1 ELSE 0 END, CASE `SubSpellID` WHEN `SubSpellID` > 1 THEN 1 ELSE 0 END, `SharedTimerGroup`, `Level`';
			
			$spells_stmt = $this->dbo->prepare($spells_sql);
			//print_r($this->dbo->errorInfo());
			$spells_stmt->execute(array(':lineName' => $lineKeyName));
			
			$spells_results = $spells_stmt->fetchAll();

			$spells_sql = 'SELECT * 
			FROM `linexspell` 
			JOIN 
				(SELECT `spell`.SpellID as `realid`, CASE `spell`.`Type` WHEN "PetSpell" THEN `spell`.`SubSpellID` ELSE `spell`.`SpellID` END as `linkid` from Spell) `lnktable` 
			ON `linexspell`.`SpellID` = `lnktable`.`realid` 
			JOIN `spell` 
			ON `lnktable`.`linkid` = `spell`.`SpellID`
			WHERE `linexspell`.`LineName` LIKE :lineName AND (`spell`.`SpellGroup` IS NOT NULL AND `spell`.`SpellGroup` > 0)
			ORDER BY `SpellGroup`, `Level`';
			
			$spells_stmt = $this->dbo->prepare($spells_sql);
			$spells_stmt->execute(array(':lineName' => $lineKeyName));
			$groups_results = $spells_stmt->fetchAll();

			$allspells = '';
			
			$previousGroup = -1;
			$previousLevel = 0;
			foreach($groups_results as $key=>$result)
			{
				if ($previousGroup != $result['SpellGroup'])
				{
					$allspells .= '</td></tr><tr><td>'.$this->DisplaySpellLineType($result).'</td><td>';
					$previousLevel = 0;
				}
				$previousGroup = $result['SpellGroup'];
				$filldivs = '';
				for ($i = 1 ; $i < ($result['Level']-$previousLevel) ; $i++)
					$filldivs .= '<div class="charbuildDisplaySpellEmpty"></div>';
					
				$previousLevel = $result['Level'];
				
				$allspells .= $filldivs.$this->DisplaySingleSpell($result);
			}
				
			$previousRow = array('type' => '', 'target' => '', 'isaoe' => false, 'isinstant' => false, 'timergroup' => 0);
			$previousLevel = 0;
			foreach($spells_results as $key=>$result)
			{
				if ($result['Type'] != $previousRow['type'] || $result['Target'] != $previousRow['target'] ||
					($result['Radius'] > 0 ? true : false) != $previousRow['isaoe'] ||
					($result['CastTime'] > 0 ? false : true) != $previousRow['isinstant'] ||
					$result['SharedTimerGroup'] != $previousRow['timergroup'])
				{
					$allspells .= '</td></tr><tr><td>'.$this->DisplaySpellLineType($result).'</td><td>';
					$previousLevel = 0;
				}
				
				$previousRow['type'] = $result['Type'];
				$previousRow['target'] = $result['Target'];
				$previousRow['isaoe'] = $result['Radius'] > 0 ? true : false;
				$previousRow['isinstant'] = $result['CastTime'] > 0 ? false : true;
				$previousRow['timergroup'] = $result['SharedTimerGroup'];
				$filldivs = '';
				for ($i = 1 ; $i < ($result['Level']-$previousLevel) ; $i++) 
					$filldivs .= '<div class="charbuildDisplaySpellEmpty"></div>';
					
				$previousLevel = $result['Level'];

				$allspells .= $filldivs.$this->DisplaySingleSpell($result);
			}
			
			if (strlen($allspells))
				return '<table>'.substr($allspells, 10).'</td></tr></table>';
				
			return $allspells;
		}
		
		function DisplaySpellLineType($result)
		{
			$content = $result['Name'];
			$content .= '<p>'.$result['Type'];
			
			switch($result['Target'])
			{
				case 'Corpse':
				case 'Enemy' :
				break;
				default:
					$content .= ', '.$result['Target'];
			}
			
			if ($result['Radius'] > 0)
			{
				if ($result['Range'] > 0)
					$content .= ', AoE';
				else
					$content .= ', PBAoE';
			}
			
			if ($result['CastTime'] == 0)
				$content .= ', Instant';
			
			return $content.'</p>';
		}
		
		function DisplaySingleSpell($result)
		{
			$content = '';
			$iconid = array_key_exists($result['Icon'], $this->eSpellsIconID) ? $this->eSpellsIconID[$result['Icon']] : 0;
			$content .= '<div class="charbuildDisplaySpell"><span class="SkillLevel">'.$result['Level'].'</span><img src="png/'.$iconid.'.png" alt="'.$result['Name'].'" title="'.$result['Name'].'"/></div>';
			return $content;
		}

	}

?>
<HTML>
	<HEAD>
		<TITLE>Character Builder</TITLE>
		<link rel="stylesheet" type="text/css" href="charbuild.css" media="screen">
	</HEAD>
	<BODY>
		<?php $cb = new CharacterBuilder(); $cb->Main(); ?>
	</BODY>
</HTML>




















