<?php
/* APP VARS **************************************************************/

$ARRAY_CODONES = array(
                        array(  'GCT', 'GCC', 'GCA', 'GCG', 'CGT', 'CGC', 'CGA', 'CGG', 'AGA', 'AGG',
                                'AAT', 'AAC', 'GAT', 'GAC', 'TGT', 'TGC', 'CAA', 'CAG', 'GAA', 'GAG', 'GGT', 'GGC', 'GGA', 'GGG',
                                'CAT', 'CAC', 'ATT', 'ATC', 'ATA', 'TTA', 'TTG', 'CTT', 'CTC', 'CTA', 'CTG', 'AAA', 'AAG', 'ATG',
                                'TTT', 'TTC', 'CCT', 'CCC', 'CCA', 'CCG', 'TCT', 'TCC', 'TCA', 'TCG', 'AGT', 'AGC', 'ACT', 'ACC',
                                'ACA', 'ACG', 'TGG', 'TAT', 'TAC', 'GTT', 'GTC', 'GTA', 'GTG', 'TAA', 'TGA', 'TAG'),
                        array(  'A', 'A', 'A', 'A', 'R', 'R', 'R', 'R', 'R', 'R',
                                'N', 'N', 'D', 'D', 'C', 'C', 'Q', 'Q', 'E', 'E', 'G', 'G', 'G', 'G',
                                'H', 'H', 'I', 'I', 'I', 'L', 'L', 'L', 'L', 'L', 'L', 'K', 'K', '<span class="m_m">M</span>',
                                'F', 'F', 'P', 'P', 'P', 'P', 'S', 'S', 'S', 'S', 'S', 'S', 'T', 'T',
                                'T', 'T', 'W', 'Y', 'Y', 'V', 'V', 'V', 'V', '<span class="m_m">*</span>', '<span class="m_m">*</span>', '<span class="m_m">*</span>')
                    );

$ARRAY_CODONES_2 = array(
                        array(  'GCT', 'GCC', 'GCA', 'GCG', 'CGT', 'CGC', 'CGA', 'CGG', 'AGA', 'AGG',
                                'AAT', 'AAC', 'GAT', 'GAC', 'TGT', 'TGC', 'CAA', 'CAG', 'GAA', 'GAG', 'GGT', 'GGC', 'GGA', 'GGG',
                                'CAT', 'CAC', 'ATT', 'ATC', 'ATA', 'TTA', 'TTG', 'CTT', 'CTC', 'CTA', 'CTG', 'AAA', 'AAG', 'ATG',
                                'TTT', 'TTC', 'CCT', 'CCC', 'CCA', 'CCG', 'TCT', 'TCC', 'TCA', 'TCG', 'AGT', 'AGC', 'ACT', 'ACC',
                                'ACA', 'ACG', 'TGG', 'TAT', 'TAC', 'GTT', 'GTC', 'GTA', 'GTG', 'TAA', 'TGA', 'TAG'),
                        array(  'A', 'A', 'A', 'A', 'R', 'R', 'R', 'R', 'R', 'R',
                                'N', 'N', 'D', 'D', 'C', 'C', 'Q', 'Q', 'E', 'E', 'G', 'G', 'G', 'G',
                                'H', 'H', 'I', 'I', 'I', 'L', 'L', 'L', 'L', 'L', 'L', 'K', 'K', 'M',
                                'F', 'F', 'P', 'P', 'P', 'P', 'S', 'S', 'S', 'S', 'S', 'S', 'T', 'T',
                                'T', 'T', 'W', 'Y', 'Y', 'V', 'V', 'V', 'V', '*', '*', '*')
                    );

/* FUNCTIONS *************************************************************/

/* CHECK FOR VALID CHARACTERS */
function check($string,$valid_chars){
    $n = strlen($string);
    $m = count($valid_chars);

    for($i = 0; $i < $n; $i++){
        $current_char = $string{$i};
        $aux = false;

        for($j = 0; $j < $m; $j++)
            if($current_char === $valid_chars[$j]){
                $aux = true;
                break;
            }

        if(!$aux)
            return false;

    }
    return true;
}
/* CHECK FOR VALID CHARACTERS */

?>
