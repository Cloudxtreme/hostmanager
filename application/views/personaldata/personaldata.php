<?php
$new_content = form_open($action);
$new_content.= '<input type="hidden" name="send_form" value="1" />';
$new_content.= '<input type="hidden" name="household" value="'.$householdId.'" />';
$new_content.= '<h2>Erwachsener</h2>'."\n";
if($householdId != 0){
    $new_content.= 'Haushaltsnummer: '.$householdId;//.' <input type="checkbox" class="checkbox" name="delete_household" id="delete_household" value="1" tabindex="0" />;
} else {
    $new_content.= 'Diese Person ist keinem Haushalt zugewiesen';
}
$new_content.= '<br /><br />'."\n";
$new_content.= '<input type="submit"class="btn" value="Personendaten abschicken" /><br/><br/>'."\n";
if(!empty($errorMessages)) {
    $new_content.= '<div class="infomsg errorMessageTop">Bitte füllen Sie alle zwingenden Angaben (rot hervorgehoben) aus.</div>';
} else {
    $new_content.= '<div class="infomsg">ACHTUNG: Sie müssen allfällige Änderungen speichern, bevor Sie die Seite verlassen.
</div>';
}
//$new_content.= '<div class="accordion">'."\n";
$new_content.= $this->method_call->get_formular($adultArray,$adultDataset);
$new_content.= '</ul></div></div>'."\n"; // close all tag
$new_content.= '</div>'; // end accordion

if($numberChild != 0) {

    for($i=0; $i<$numberChild; $i++) {
        $new_content.= '<br />';
        $new_content.= '<h2>Kind '.($i+1).'</h2>'."\n";
        //$new_content.= '<div class="accordion">'."\n";
        $new_content.= $this->method_call->get_formular($childArray,$childDataset[$i],'_'.$i);
        $new_content.= '</ul></div></div>'."\n"; // close all tag
        $new_content.= '</div>'; // accordion
    }
}
$new_content.= '<br class="clearLeft" />';
$new_content.= '<input type="submit" class="btn" value="Personendaten abschicken" />'."\n";
$new_content.= '</form>';

echo $new_content;
?>
